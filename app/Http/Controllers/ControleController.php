<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ControleFiltreForm;
use App\Http\Requests\ControleRapportRequest;
use App\Http\Requests\ControleStoreRequest;
use App\Http\Requests\ControleTransitionRequest;
use App\Models\Agent;
use App\Models\Collectivite;
use App\Models\ControleConstat;
use App\Models\ControleFiscal;
use App\Models\Etablissement;
use App\Models\EtatControle;
use App\Models\ExerciceFiscal;
use App\Models\NatureTaxe;
use App\Models\SanctionFiscale;
use App\Models\Service;
use App\Services\ControleWorkflowService;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ControleController extends Controller
{
    private const COLONNES_TRI = [
        'numero', 'etablissement_id', 'etat_controle_id',
        'date_instruction', 'date_cloture', 'created_at', 'updated_at',
    ];

    public function __construct(
        private SelectOptionsService $selectOptions,
        private ControleWorkflowService $workflow,
    ) {}

    public function index(Request $request)
    {
        $etats = EtatControle::orderBy('ordre')->get();

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'created_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ControleFiltreForm::fromRequest($request);

        $controles = $filtre->appliquer(
            ControleFiscal::with(['etablissement.contribuable', 'etatControle', 'agentInstructeur'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('controles.index', compact('controles', 'filtre', 'etats', 'sortActuel', 'dirActuelle'));
    }

    public function create(Request $request): View
    {
        $etablissement = $request->filled('etablissement_id')
            ? Etablissement::with('contribuable')->findOrFail($request->query('etablissement_id'))
            : null;

        $agents = $this->selectOptions->charger(Agent::class, 'nom');
        $etablissements = $etablissement
            ? collect()
            : Etablissement::with('contribuable')->whereNull('supprime_le')->orderBy('numero')->get();

        return view('controles.create', compact('etablissement', 'etablissements', 'agents'));
    }

    public function store(ControleStoreRequest $request): RedirectResponse
    {
        $donnees = $request->validated();

        $etatInitial = EtatControle::where('code', 'INSTRUCTION')->firstOrFail();
        $annee = (int) (($donnees['periode_fin'] ?? null) ? substr($donnees['periode_fin'], 0, 4) : now()->year);

        $dernier = ControleFiscal::where('numero', 'like', "CTRL{$annee}%")
            ->orderBy('numero', 'desc')->value('numero');
        $seq = $dernier ? ((int) substr($dernier, -6) + 1) : 1;

        $donnees['numero']          = "CTRL{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['etat_controle_id'] = $etatInitial->id;
        $donnees['collectivite_id']  = Collectivite::value('id');
        $donnees['date_instruction'] = now()->toDateString();
        $donnees['created_by']       = auth()->id();

        $controle = ControleFiscal::create($donnees);

        return redirect()->route('controles.show', $controle)
            ->with('success', 'Contrôle créé — N° ' . $controle->numero);
    }

    public function show(ControleFiscal $controle): View
    {
        $controle->load([
            'etablissement.contribuable', 'etatControle', 'agentInstructeur',
            'convocation', 'constats.natureTaxe', 'constats.sanctionFiscale',
            'redressement', 'historiques.etatSource', 'historiques.etatCible',
        ]);

        $transitions = $this->workflow->transitionsDisponibles($controle, auth()->user());

        // Référentiels pour les actions (convocation, clôture, redressement)
        $services = $this->selectOptions->charger(Service::class, 'libelle');
        $agents   = $this->selectOptions->charger(Agent::class, 'nom');

        return view('controles.show', compact('controle', 'transitions', 'services', 'agents'));
    }

    public function edit(ControleFiscal $controle): View
    {
        abort_unless($controle->etatControle?->code === 'INSTRUCTION', 403,
            "Le contrôle n'est plus modifiable une fois validé.");

        $controle->load('etablissement.contribuable');
        $agents = $this->selectOptions->charger(Agent::class, 'nom');

        return view('controles.edit', compact('controle', 'agents'));
    }

    public function update(ControleStoreRequest $request, ControleFiscal $controle): RedirectResponse
    {
        abort_unless($controle->etatControle?->code === 'INSTRUCTION', 403,
            "Le contrôle n'est plus modifiable une fois validé.");

        $donnees = $request->validated();
        $donnees['updated_by'] = auth()->id();
        $controle->update($donnees);

        return redirect()->route('controles.show', $controle)
            ->with('success', 'Contrôle mis à jour.');
    }

    public function destroy(ControleFiscal $controle): RedirectResponse
    {
        abort_unless($controle->etatControle?->code === 'INSTRUCTION', 403,
            "Seul un contrôle en instruction peut être supprimé.");

        $controle->delete();

        return redirect()->route('controles.index')->with('success', 'Contrôle supprimé.');
    }

    /**
     * Écran de saisie du rapport (constats) — disponible quand le contrôle est
     * validé. L'enregistrement bascule le contrôle en « Exécuté ».
     */
    public function rapport(ControleFiscal $controle): View
    {
        abort_unless(in_array($controle->etatControle?->code, ['VALIDE', 'EXECUTE'], true), 403,
            'Le rapport se saisit après validation du contrôle.');

        $controle->load(['etablissement.contribuable', 'constats']);

        $naturesTaxe = NatureTaxe::orderBy('code')->get();
        $sanctions   = SanctionFiscale::orderBy('code')->get();
        $exercices   = ExerciceFiscal::orderBy('annee', 'desc')->get();

        return view('controles.rapport', compact('controle', 'naturesTaxe', 'sanctions', 'exercices'));
    }

    public function rapportStore(ControleRapportRequest $request, ControleFiscal $controle): RedirectResponse
    {
        abort_unless(in_array($controle->etatControle?->code, ['VALIDE', 'EXECUTE'], true), 403);

        $valide = $request->validated();

        DB::transaction(function () use ($controle, $valide) {
            $controle->update([
                'rapport_synthese' => $valide['rapport_synthese'] ?? null,
                'updated_by'       => auth()->id(),
            ]);

            // Remplace l'ensemble des constats
            $controle->constats()->delete();
            foreach ($valide['constats'] ?? [] as $c) {
                $declare = (string) ($c['montant_declare'] ?? 0);
                $verifie = (string) ($c['montant_verifie'] ?? 0);
                ControleConstat::create([
                    'controle_fiscal_id' => $controle->id,
                    'nature_taxe_id'     => $c['nature_taxe_id'],
                    'exercice_fiscal_id' => $c['exercice_fiscal_id'] ?? null,
                    'montant_declare'    => $declare,
                    'montant_verifie'    => $verifie,
                    'ecart'              => bcsub($verifie, $declare, 2),
                    'sanction_fiscale_id'=> $c['sanction_fiscale_id'] ?? null,
                    'observation'        => $c['observation'] ?? null,
                    'created_by'         => auth()->id(),
                ]);
            }
        });

        // Première saisie : bascule en « Exécuté » via le workflow
        if ($controle->etatControle?->code === 'VALIDE') {
            try {
                $this->workflow->transitionner($controle, 'EXECUTE', auth()->user(),
                    motif: 'Saisie du rapport de contrôle');
            } catch (AuthorizationException $e) {
                return redirect()->route('controles.show', $controle)->with('error', $e->getMessage());
            }
        }

        return redirect()->route('controles.show', $controle)
            ->with('success', 'Rapport enregistré.');
    }

    /**
     * Transition générique du workflow (valider, clôturer, redresser, renvoyer).
     */
    public function transition(ControleTransitionRequest $request, ControleFiscal $controle): RedirectResponse
    {
        try {
            $controle = $this->workflow->transitionner(
                $controle,
                $request->validated()['code_cible'],
                auth()->user(),
                $request->payload(),
                $request->input('motif'),
            );
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Vers le redressement créé le cas échéant
        if ($controle->etatControle?->code === 'REDRESSE' && $controle->redressement) {
            return redirect()->route('redressements.show', $controle->redressement)
                ->with('success', 'Redressement ouvert — N° ' . $controle->redressement->numero);
        }

        return redirect()->route('controles.show', $controle)
            ->with('success', 'Contrôle mis à jour : ' . $controle->etatControle?->libelle . '.');
    }

    /**
     * Avis de convocation (PDF) — livrable de la validation du contrôle.
     */
    public function convocationPdf(ControleFiscal $controle): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($controle->convocation_id, 404, 'Aucune convocation rattachée à ce contrôle.');

        $controle->load(['convocation.service', 'convocation.agent', 'etablissement.contribuable']);
        $convocation  = $controle->convocation;
        $contribuable = $controle->etablissement?->contribuable;
        $collectivite = Collectivite::find($controle->collectivite_id);

        $pdf = Pdf::loadView('controles.convocation-pdf', compact(
            'controle', 'convocation', 'contribuable', 'collectivite'
        ))->setPaper('a4');

        return $pdf->download('convocation-' . ($convocation->numero ?? $controle->numero) . '.pdf');
    }

    /**
     * Procès-verbal de clôture (PDF) — contrôle clôturé sans dommage.
     */
    public function pvCloture(ControleFiscal $controle): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($controle->etatControle?->code === 'CLOTURE', 403,
            'Le PV de clôture n\'est disponible que pour un contrôle clôturé.');

        $controle->load([
            'etablissement.contribuable', 'agentInstructeur',
            'constats.natureTaxe', 'etatControle',
        ]);
        $contribuable = $controle->etablissement?->contribuable;
        $collectivite = Collectivite::find($controle->collectivite_id);

        $pdf = Pdf::loadView('controles.pv-cloture-pdf', compact(
            'controle', 'contribuable', 'collectivite'
        ))->setPaper('a4');

        return $pdf->download('pv-cloture-' . $controle->numero . '.pdf');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ControleFiltreForm::fromRequest($request);

        return $excel->telecharger('controles_fiscaux', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Contrôle', 'Établissement', 'Contribuable', 'N° Identifiant',
                'État', 'Période début', 'Période fin', 'Date instruction', 'Date clôture',
            ], $entete));

            $filtre->appliquer(
                ControleFiscal::with(['etablissement.contribuable', 'etatControle'])
            )->orderBy('created_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $c) {
                    $contrib = $c->etablissement?->contribuable;
                    $nom = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $c->numero ?? '',
                        $c->etablissement?->denomination ?? $c->etablissement?->numero ?? '',
                        $nom,
                        $contrib?->numero_identifiant ?? '',
                        $c->etatControle?->libelle ?? '',
                        $c->periode_debut?->format('d/m/Y') ?? '',
                        $c->periode_fin?->format('d/m/Y') ?? '',
                        $c->date_instruction?->format('d/m/Y') ?? '',
                        $c->date_cloture?->format('d/m/Y') ?? '',
                    ]));
                }
            });
        });
    }
}
