<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\RedressementFiltreForm;
use App\Http\Requests\RedressementEmissionRequest;
use App\Models\Collectivite;
use App\Models\ExerciceFiscal;
use App\Models\NatureTaxe;
use App\Models\Periodicite;
use App\Models\Redressement;
use App\Pdf\AvisRedressement;
use App\Services\ControleWorkflowService;
use App\Services\ExcelExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class RedressementController extends Controller
{
    private const COLONNES_TRI = [
        'numero', 'montant_total', 'etat', 'date_redressement', 'created_at',
    ];

    public function __construct(private ControleWorkflowService $workflow) {}

    public function index(Request $request)
    {
        $sortActuel = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'created_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = RedressementFiltreForm::fromRequest($request);

        $redressements = $filtre->appliquer(
            Redressement::with(['controleFiscal.etablissement.contribuable'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('redressements.index', compact('redressements', 'filtre', 'sortActuel', 'dirActuelle'));
    }

    public function show(Redressement $redressement): View
    {
        $redressement->load([
            'controleFiscal.etablissement.contribuable',
            'controleFiscal.constats.natureTaxe',
            'emissionsTaxe.natureTaxe', 'emissionsTaxe.exerciceFiscal',
            'collectivite',
        ]);

        $naturesTaxe = NatureTaxe::orderBy('code')->get();
        $periodicites = Periodicite::orderBy('libelle')->get();
        $exercices = ExerciceFiscal::where('cloture', false)->orderBy('annee', 'desc')->get();

        return view('redressements.show', compact(
            'redressement', 'naturesTaxe', 'periodicites', 'exercices'
        ));
    }

    /**
     * Génère les émissions complémentaires du redressement (recouvrables ensuite
     * via le module Recouvrement).
     */
    public function emissions(RedressementEmissionRequest $request, Redressement $redressement): RedirectResponse
    {
        $emissions = $this->workflow->genererEmissionsComplementaires(
            $redressement,
            $request->validated()['lignes'],
            auth()->user(),
        );

        return redirect()->route('redressements.show', $redressement)
            ->with('success', $emissions->count().' émission(s) complémentaire(s) générée(s).');
    }

    /**
     * Enregistre la pénalité saisie pour chaque déclaration complémentaire.
     * Le montant recouvrable de chaque émission devient droits + pénalité, puis
     * les totaux du redressement sont recalculés.
     */
    public function penalites(Request $request, Redressement $redressement): RedirectResponse
    {
        // Nettoie les séparateurs de milliers éventuels.
        $saisies = collect($request->input('penalite', []))
            ->map(fn ($v) => is_string($v) ? preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $v) : $v)
            ->all();
        $request->merge(['penalite' => $saisies]);

        $request->validate([
            'penalite' => ['array'],
            'penalite.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($redressement, $saisies) {
            foreach ($redressement->emissionsTaxe as $emission) {
                $nouvelle = (string) ($saisies[$emission->id] ?? '0');
                // Droits = montant courant − ancienne pénalité ; montant = droits + nouvelle pénalité.
                $droits = bcsub((string) $emission->montant_annuel, (string) $emission->penalite, 2);
                $montant = bcadd($droits, $nouvelle, 2);

                $emission->update([
                    'penalite' => $nouvelle,
                    'montant_annuel' => $montant,
                    'montant_periode' => $montant,
                    'montant_prorata' => $montant,
                    'updated_by' => auth()->id(),
                ]);
            }

            $this->workflow->recalculerTotaux($redressement);
        });

        return redirect()->route('redressements.show', $redressement)
            ->with('success', 'Pénalités enregistrées.');
    }

    /** Met à jour l'état du dossier (notifié, soldé, annulé). */
    public function etat(Request $request, Redressement $redressement): RedirectResponse
    {
        $valide = $request->validate([
            'etat' => ['required', 'string', 'in:ouvert,notifie,solde,annule'],
        ]);

        $redressement->update(['etat' => $valide['etat'], 'updated_by' => auth()->id()]);

        return redirect()->route('redressements.show', $redressement)
            ->with('success', 'État du redressement mis à jour.');
    }

    /**
     * Avis de redressement (PDF) — notification des montants au contribuable.
     */
    public function avis(Redressement $redressement): Response
    {
        $redressement->load([
            'controleFiscal.etablissement.contribuable',
            'controleFiscal.constats.natureTaxe',
            'emissionsTaxe.natureTaxe', 'emissionsTaxe.exerciceFiscal',
        ]);

        $controle = $redressement->controleFiscal;
        $contribuable = $controle?->etablissement?->contribuable;
        $collectivite = Collectivite::find($redressement->collectivite_id);

        return (new AvisRedressement($redressement, $controle, $contribuable, $collectivite))
            ->reponse('avis-redressement-'.$redressement->numero.'.pdf');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = RedressementFiltreForm::fromRequest($request);

        return $excel->telecharger('redressements', function (Writer $writer) use ($filtre): void {
            $entete = (new Style)->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Redressement', 'N° Contrôle', 'Établissement', 'Contribuable',
                'Droits', 'Pénalités', 'Total', 'État', 'Date',
            ], $entete));

            $filtre->appliquer(
                Redressement::with(['controleFiscal.etablissement.contribuable'])
            )->orderBy('created_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $r) {
                    $contrib = $r->controleFiscal?->etablissement?->contribuable;
                    $nom = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '').' '.($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $r->numero ?? '',
                        $r->controleFiscal?->numero ?? '',
                        $r->controleFiscal?->etablissement?->denomination
                            ?? $r->controleFiscal?->etablissement?->numero ?? '',
                        $nom,
                        (float) $r->montant_droits,
                        (float) $r->montant_penalites,
                        (float) $r->montant_total,
                        $r->etat ?? '',
                        $r->date_redressement?->format('d/m/Y') ?? '',
                    ]));
                }
            });
        });
    }
}
