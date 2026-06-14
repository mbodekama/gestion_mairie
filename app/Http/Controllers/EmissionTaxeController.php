<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\EmissionTaxeFiltreForm;
use App\Models\Collectivite;
use App\Models\EmissionTaxe;
use App\Models\Etablissement;
use App\Models\ExerciceFiscal;
use App\Models\NatureTaxe;
use App\Models\Periodicite;
use App\Services\ExcelExportService;
use App\Services\LiquidationTaxeService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmissionTaxeController extends Controller
{
    private const COLONNES_TRI = [
        'numero_emission', 'etablissement_id', 'nature_taxe_id',
        'exercice_fiscal_id', 'periodicite_id', 'montant_annuel', 'date_liquidation', 'updated_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $exercices    = $this->selectOptions->charger(ExerciceFiscal::class, 'annee');
        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'updated_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = EmissionTaxeFiltreForm::fromRequest($request);

        $emissions = $filtre->appliquer(
            EmissionTaxe::with(['etablissement.contribuable', 'natureTaxe', 'periodicite', 'exerciceFiscal'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('emissions.index', compact(
            'emissions', 'filtre', 'exercices', 'naturesTaxe', 'periodicites',
            'sortActuel', 'dirActuelle',
        ));
    }

    public function create(Request $request): View
    {
        $etablissement = $request->query('etablissement_id')
            ? Etablissement::with('contribuable')->findOrFail($request->query('etablissement_id'))
            : null;

        $exerciceDefaut = $request->query('exercice_fiscal_id')
            ? ExerciceFiscal::findOrFail($request->query('exercice_fiscal_id'))
            : null;

        $exercices    = ExerciceFiscal::where('cloture', false)->orderBy('annee', 'desc')->get();
        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('emissions.create', compact(
            'etablissement', 'exerciceDefaut', 'exercices', 'naturesTaxe', 'periodicites',
        ));
    }

    /**
     * Calcule les montants d'une émission depuis le barème applicable (JSON).
     * Alimente le bouton « Calculer » du formulaire de création.
     */
    public function liquider(Request $request, LiquidationTaxeService $liquidation): \Illuminate\Http\JsonResponse
    {
        $valide = $request->validate([
            'etablissement_id' => ['nullable', 'integer', 'exists:etablissement,id'],
            'nature_taxe_id'   => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id'   => ['required', 'integer', 'exists:periodicite,id'],
            'ca_annuel'        => ['nullable', 'numeric', 'min:0'],
            'nb_mois_prorata'  => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        // Catégorie d'activité de l'établissement (pour cibler le barème spécifique)
        $categorieId = empty($valide['etablissement_id'])
            ? null
            : Etablissement::with('activite')->find($valide['etablissement_id'])?->activite?->categorie_activite_id;

        $resultat = $liquidation->liquider(
            natureTaxeId:        (int) $valide['nature_taxe_id'],
            periodiciteId:       (int) $valide['periodicite_id'],
            caAnnuel:            (string) ($valide['ca_annuel'] ?? '0'),
            categorieActiviteId: $categorieId,
            nbMoisProrata:       $valide['nb_mois_prorata'] ?? null,
        );

        return response()->json($resultat);
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'etablissement_id'   => ['required', 'integer', 'exists:etablissement,id'],
            'nature_taxe_id'     => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id'     => ['required', 'integer', 'exists:periodicite,id'],
            'exercice_fiscal_id' => ['required', 'integer', 'exists:exercice_fiscal,id'],
            'ca_annuel'          => ['nullable', 'numeric', 'min:0'],
            'montant_annuel'     => ['required', 'numeric', 'min:0'],
            'montant_periode'    => ['nullable', 'numeric', 'min:0'],
            'nb_mois_prorata'    => ['nullable', 'integer', 'min:1', 'max:12'],
            'montant_prorata'    => ['nullable', 'numeric', 'min:0'],
            'date_declaration'   => ['nullable', 'date'],
            'date_liquidation'   => ['nullable', 'date'],
        ]);

        $exercice = ExerciceFiscal::findOrFail($donnees['exercice_fiscal_id']);
        if ($exercice->cloture) {
            return back()->withInput()->with('error', 'Impossible d\'émettre sur un exercice clôturé.');
        }

        $collectivite = Collectivite::first();
        $annee        = $exercice->annee;

        $seq = EmissionTaxe::where('numero_emission', 'like', "EMI{$annee}%")
            ->orderBy('numero_emission', 'desc')
            ->value('numero_emission');
        $seq = $seq ? ((int) substr($seq, -6) + 1) : 1;

        $etab = Etablissement::findOrFail($donnees['etablissement_id']);
        // Numéros métier générés : émission, fiche (même séquence annuelle) et article
        $donnees['numero_emission'] = "EMI{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['numero_fiche']    = "FE{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['numero_article']  = $etab->numero . '/' . $annee;
        $donnees['collectivite_id'] = $collectivite?->id;
        $donnees['created_by']      = auth()->id();

        $emission = EmissionTaxe::create($donnees);

        return redirect()->route('emissions.show', $emission)
            ->with('success', 'Émission créée avec succès.');
    }

    public function show(EmissionTaxe $emission): View
    {
        $emission->load([
            'etablissement.contribuable',
            'etablissement.commune',
            'natureTaxe',
            'periodicite',
            'exerciceFiscal',
            'reglements.modeReglement',
            'reglements.typeReglement',
            'reglements.banque',
        ]);

        $montantBase = $emission->montant_prorata > 0 ? $emission->montant_prorata : $emission->montant_annuel;
        $totalRegle  = $emission->reglements->reduce(
            fn($carry, $r) => bcadd($carry, (string) $r->montant_impute, 2), '0'
        );
        $soldeDu = $emission->soldeDu();

        return view('emissions.show', compact('emission', 'montantBase', 'totalRegle', 'soldeDu'));
    }

    public function edit(EmissionTaxe $emission): View
    {
        $emission->load(['etablissement.contribuable', 'natureTaxe', 'periodicite', 'exerciceFiscal']);

        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('emissions.edit', compact('emission', 'naturesTaxe', 'periodicites'));
    }

    public function update(Request $request, EmissionTaxe $emission): RedirectResponse
    {
        if ($emission->exerciceFiscal?->cloture) {
            return back()->with('error', 'Exercice clôturé — modification impossible.');
        }

        $donnees = $request->validate([
            'nature_taxe_id'   => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id'   => ['required', 'integer', 'exists:periodicite,id'],
            'ca_annuel'        => ['nullable', 'numeric', 'min:0'],
            'montant_annuel'   => ['required', 'numeric', 'min:0'],
            'montant_periode'  => ['nullable', 'numeric', 'min:0'],
            'nb_mois_prorata'  => ['nullable', 'integer', 'min:1', 'max:12'],
            'montant_prorata'  => ['nullable', 'numeric', 'min:0'],
            'date_declaration' => ['nullable', 'date'],
            'date_liquidation' => ['nullable', 'date'],
        ]);

        $donnees['updated_by'] = auth()->id();
        $emission->update($donnees);

        return redirect()->route('emissions.show', $emission)
            ->with('success', 'Émission mise à jour.');
    }

    public function destroy(EmissionTaxe $emission): RedirectResponse
    {
        if ($emission->reglements()->exists()) {
            return back()->with('error', 'Impossible de supprimer une émission ayant des règlements.');
        }

        $etablissementId = $emission->etablissement_id;
        $emission->delete();

        return redirect()->route('etablissements.show', $etablissementId)
            ->with('success', 'Émission supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = EmissionTaxeFiltreForm::fromRequest($request);

        return $excel->telecharger('emissions_taxes', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Émission', 'N° Établissement', 'Contribuable', 'Nature taxe',
                'Exercice', 'Périodicité', 'Montant annuel', 'Solde dû', 'Date liquidation',
            ], $entete));

            $filtre->appliquer(
                EmissionTaxe::with(['etablissement.contribuable', 'natureTaxe', 'periodicite', 'exerciceFiscal'])
            )->orderBy('updated_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $e) {
                    $contrib = $e->etablissement?->contribuable;
                    $nomContrib = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $montant = (float) ($e->montant_prorata > 0 ? $e->montant_prorata : $e->montant_annuel);
                    $solde   = (float) $e->soldeDu();

                    $writer->addRow(Row::fromValues([
                        $e->numero_emission ?? '',
                        $e->etablissement?->numero ?? '',
                        $nomContrib,
                        $e->natureTaxe?->libelle_court ?? '',
                        $e->exerciceFiscal?->annee ?? '',
                        $e->periodicite?->libelle_court ?? $e->periodicite?->libelle ?? '',
                        $montant,
                        $solde,
                        $e->date_liquidation?->format('d/m/Y') ?? '',
                    ]));
                }
            });
        });
    }
}
