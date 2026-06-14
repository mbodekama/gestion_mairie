<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\RecouvrementFiltreForm;
use App\Models\Banque;
use App\Models\Collectivite;
use App\Models\Contribuable;
use App\Models\EmissionTaxe;
use App\Models\Etablissement;
use App\Models\ExerciceFiscal;
use App\Models\ModeReglement;
use App\Models\Recette;
use App\Models\ReglementTaxe;
use App\Models\TypeReglement;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RecouvrementController extends Controller
{
    private const COLONNES_TRI = [
        'numero_reglement', 'exercice_fiscal_id', 'date_reglement',
        'mode_reglement_id', 'type_reglement_id',
        'montant', 'montant_impute', 'numero_quittance', 'updated_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $exercices = $this->selectOptions->charger(ExerciceFiscal::class, 'annee');
        $modes     = $this->selectOptions->charger(ModeReglement::class, 'libelle');
        $types     = $this->selectOptions->charger(TypeReglement::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'date_reglement';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = RecouvrementFiltreForm::fromRequest($request);

        $reglements = $filtre->appliquer(
            ReglementTaxe::with([
                'emissionTaxe.etablissement.contribuable',
                'emissionCotisation.etablissement.contribuable',
                'modeReglement',
                'typeReglement',
                'exerciceFiscal',
            ])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('recouvrements.index', compact(
            'reglements', 'filtre', 'exercices', 'modes', 'types',
            'sortActuel', 'dirActuelle',
        ));
    }

    /**
     * Étape 1 (sans `code`) : saisie du code contribuable / établissement.
     * Étape 2 (avec `code`) : fiche contribuable + émissions à régler + paiement.
     */
    public function create(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));

        // Compatibilité : arrivée depuis une émission → on cible son établissement
        if ($code === '' && $request->filled('emission_taxe_id')) {
            $em = EmissionTaxe::with('etablissement')->find($request->query('emission_taxe_id'));
            $code = $em?->etablissement?->numero ?? '';
        }

        // Étape 1 : formulaire de saisie du code
        if ($code === '') {
            return view('recouvrements.create');
        }

        // Étape 2 : résolution de la cible
        $cible = $this->resoudreCible($code);

        if (! $cible['contribuable']) {
            return view('recouvrements.create', [
                'code'       => $code,
                'erreurCode' => "Aucun contribuable ou établissement trouvé pour « {$code} ».",
            ]);
        }

        return view('recouvrements.encaisser', array_merge($cible, [
            'code'     => $code,
            'modes'    => $this->selectOptions->charger(ModeReglement::class, 'libelle'),
            'types'    => $this->selectOptions->charger(TypeReglement::class, 'libelle'),
            'recettes' => $this->selectOptions->charger(Recette::class, 'libelle'),
            'banques'  => $this->selectOptions->charger(Banque::class, 'libelle'),
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalise les montants saisis : retire les séparateurs de milliers
        // (espaces normaux et insécables) avant validation.
        $montants = $request->input('montant', []);
        if (is_array($montants)) {
            $request->merge([
                'montant' => array_map(
                    fn ($v) => is_string($v) ? preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $v) : $v,
                    $montants
                ),
            ]);
        }

        $valide = $request->validate([
            'code'              => ['nullable', 'string'],
            'recette_id'        => ['required', 'integer', 'exists:recette,id'],
            'mode_reglement_id' => ['required', 'integer', 'exists:mode_reglement,id'],
            'type_reglement_id' => ['required', 'integer', 'exists:type_reglement,id'],
            'numero_cheque'     => ['nullable', 'string', 'max:64'],
            'banque_id'         => ['nullable', 'integer', 'exists:banque,id'],
            'operateur_mobile'     => ['nullable', 'string', 'max:64'],
            'reference_transaction' => ['nullable', 'string', 'max:64'],
            'emissions'         => ['required', 'array', 'min:1'],
            'emissions.*'       => ['integer', 'exists:emission_taxe,id'],
            'montant'           => ['required', 'array'],
            'montant.*'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'emissions.required' => 'Sélectionnez au moins une émission à régler.',
        ]);

        // Construit la liste (émission, montant) des lignes cochées avec un montant > 0,
        // en contrôlant que le montant ne dépasse pas le solde dû.
        $lignes  = [];
        $erreurs = [];

        foreach ($valide['emissions'] as $emId) {
            $montant = (float) ($request->input("montant.$emId", 0));
            if ($montant <= 0) {
                continue; // cochée mais sans montant → ignorée
            }

            $emission = EmissionTaxe::find($emId);
            $solde    = (float) $emission->soldeDu();

            if ($emission->exerciceFiscal?->cloture) {
                $erreurs["montant.$emId"] = 'Exercice clôturé : règlement impossible.';
            } elseif ($montant > $solde) {
                $erreurs["montant.$emId"] = 'Le montant dépasse le solde dû (' . number_format($solde, 0, ',', ' ') . ' FCFA).';
            } else {
                $lignes[] = [$emission, $montant];
            }
        }

        if ($erreurs) {
            return back()->withInput()->withErrors($erreurs);
        }

        if (! $lignes) {
            return back()->withInput()->withErrors([
                'emissions' => 'Saisissez un montant à payer pour au moins une émission cochée.',
            ]);
        }

        $collectivite = Collectivite::first();
        $annee = now()->year;
        $dateReglement = now()->toDateString();

        $seq = ReglementTaxe::where('numero_reglement', 'like', "RG{$annee}%")
            ->orderBy('numero_reglement', 'desc')
            ->value('numero_reglement');
        $seq = $seq ? ((int) substr($seq, -6) + 1) : 1;

        // Une quittance (reçu) unique pour l'ensemble de l'encaissement, partagée
        // par tous les règlements de ce lot. Générée selon le modèle des autres numéros.
        $seqQuittance = ReglementTaxe::where('numero_quittance', 'like', "QT{$annee}%")
            ->orderBy('numero_quittance', 'desc')
            ->value('numero_quittance');
        $seqQuittance = $seqQuittance ? ((int) substr($seqQuittance, -6) + 1) : 1;
        $numeroQuittance = "QT{$annee}" . str_pad($seqQuittance, 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($lignes, &$seq, $valide, $request, $collectivite, $annee, $dateReglement, $numeroQuittance): void {
            foreach ($lignes as [$emission, $montant]) {
                ReglementTaxe::create([
                    'numero_reglement'   => "RG{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT),
                    'emission_taxe_id'   => $emission->id,
                    'collectivite_id'    => $collectivite?->id,
                    'recette_id'         => $valide['recette_id'],
                    'exercice_fiscal_id' => $emission->exercice_fiscal_id,
                    'date_reglement'     => $dateReglement,
                    'montant'            => $montant,
                    'montant_impute'     => $montant,
                    'mode_reglement_id'  => $valide['mode_reglement_id'],
                    'type_reglement_id'  => $valide['type_reglement_id'],
                    'numero_cheque'      => $request->input('numero_cheque'),
                    'banque_id'          => $request->input('banque_id'),
                    'operateur_mobile'      => $request->input('operateur_mobile'),
                    'reference_transaction' => $request->input('reference_transaction'),
                    'numero_quittance'   => $numeroQuittance,
                    'created_by'         => auth()->id(),
                ]);
                $seq++;
            }
        });

        return redirect()->route('recouvrements.index')
            ->with('success', count($lignes) . ' règlement(s) enregistré(s).');
    }

    /**
     * Résout un code en cible (établissement par `numero`, sinon contribuable par
     * `numero_identifiant`/`numero_compte`) et ses émissions au solde non nul.
     *
     * @return array{contribuable: ?Contribuable, etablissement: ?Etablissement, emissions: \Illuminate\Support\Collection}
     */
    private function resoudreCible(string $code): array
    {
        $etablissement = Etablissement::with('contribuable')
            ->whereNull('supprime_le')
            ->where('numero', $code)
            ->first();

        if ($etablissement) {
            return [
                'contribuable'  => $etablissement->contribuable,
                'etablissement' => $etablissement,
                'emissions'     => $this->emissionsAvecSolde(
                    EmissionTaxe::where('etablissement_id', $etablissement->id)
                ),
            ];
        }

        $contribuable = Contribuable::whereNull('supprime_le')
            ->where(fn ($q) => $q->where('numero_identifiant', $code)->orWhere('numero_compte', $code))
            ->first();

        if ($contribuable) {
            $etabIds = $contribuable->etablissements()->pluck('id');

            return [
                'contribuable'  => $contribuable,
                'etablissement' => null,
                'emissions'     => $this->emissionsAvecSolde(
                    EmissionTaxe::whereIn('etablissement_id', $etabIds)
                ),
            ];
        }

        return ['contribuable' => null, 'etablissement' => null, 'emissions' => collect()];
    }

    /** Émissions chargées avec leurs relations, restreintes à un solde dû > 0. */
    private function emissionsAvecSolde(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Support\Collection
    {
        return $query->with(['etablissement', 'natureTaxe', 'periodicite', 'exerciceFiscal'])
            ->orderBy('numero_emission')
            ->get()
            ->filter(fn (EmissionTaxe $e) => (float) $e->soldeDu() > 0)
            ->values();
    }

    public function show(ReglementTaxe $recouvrement): View
    {
        $recouvrement->load([
            'emissionTaxe.etablissement.contribuable',
            'emissionTaxe.natureTaxe',
            'emissionTaxe.exerciceFiscal',
            'modeReglement',
            'typeReglement',
            'exerciceFiscal',
            'banque',
            'recette',
        ]);

        try {
            $historiques = $recouvrement->historique()->latest('created_at')->take(50)->get();
        } catch (\Illuminate\Database\QueryException) {
            $historiques = collect();
        }

        return view('recouvrements.show', compact('recouvrement', 'historiques'));
    }

    /**
     * Génère et télécharge la quittance (reçu) au format PDF. Une quittance peut
     * regrouper plusieurs règlements partageant le même numéro de quittance.
     */
    public function quittance(ReglementTaxe $recouvrement): Response
    {
        $relations = [
            'emissionTaxe.etablissement.contribuable',
            'emissionTaxe.natureTaxe',
            'modeReglement', 'typeReglement', 'banque', 'recette',
        ];

        $reglements = $recouvrement->numero_quittance
            ? ReglementTaxe::with($relations)
                ->where('numero_quittance', $recouvrement->numero_quittance)
                ->orderBy('numero_reglement')
                ->get()
            : collect([$recouvrement->load($relations)]);

        $contribuable = $reglements->first()?->emissionTaxe?->etablissement?->contribuable;
        $collectivite = Collectivite::find($recouvrement->collectivite_id);
        $total = $reglements->reduce(fn ($c, $r) => bcadd($c, (string) $r->montant_impute, 2), '0');

        $pdf = Pdf::loadView('recouvrements.quittance-pdf', compact(
            'recouvrement', 'reglements', 'contribuable', 'collectivite', 'total'
        ))->setPaper('a4');

        return $pdf->download('quittance-' . ($recouvrement->numero_quittance ?? $recouvrement->numero_reglement) . '.pdf');
    }

    /**
     * Annule un règlement avec un motif obligatoire. Le règlement annulé ne
     * réduit plus le solde dû de l'émission.
     */
    public function annuler(Request $request, ReglementTaxe $recouvrement): RedirectResponse
    {
        if ($recouvrement->estAnnule()) {
            return back()->with('error', 'Ce règlement est déjà annulé.');
        }

        $valide = $request->validate([
            'motif_annulation' => ['required', 'string', 'max:255'],
        ], [
            'motif_annulation.required' => 'Le motif d\'annulation est obligatoire.',
        ]);

        $recouvrement->update([
            'annule_le'        => now(),
            'motif_annulation' => $valide['motif_annulation'],
            'annule_par'       => auth()->id(),
        ]);

        return redirect()->route('recouvrements.show', $recouvrement)
            ->with('success', 'Règlement ' . $recouvrement->numero_reglement . ' annulé.');
    }

    public function edit(ReglementTaxe $recouvrement): View
    {
        $recouvrement->load([
            'emissionTaxe.natureTaxe',
            'emissionTaxe.etablissement.contribuable',
            'modeReglement',
            'typeReglement',
            'banque',
        ]);

        $modes   = $this->selectOptions->charger(ModeReglement::class, 'libelle');
        $types   = $this->selectOptions->charger(TypeReglement::class, 'libelle');
        $banques = $this->selectOptions->charger(Banque::class, 'libelle');

        return view('recouvrements.edit', compact('recouvrement', 'modes', 'types', 'banques'));
    }

    public function update(Request $request, ReglementTaxe $recouvrement): RedirectResponse
    {
        $donnees = $request->validate([
            'date_reglement'    => ['required', 'date'],
            'montant'           => ['required', 'numeric', 'min:0'],
            'montant_impute'    => ['required', 'numeric', 'min:0'],
            'mode_reglement_id' => ['required', 'integer', 'exists:mode_reglement,id'],
            'type_reglement_id' => ['required', 'integer', 'exists:type_reglement,id'],
            'numero_cheque'     => ['nullable', 'string', 'max:64'],
            'banque_id'         => ['nullable', 'integer', 'exists:banque,id'],
            'numero_quittance'  => ['nullable', 'string', 'max:64'],
            'mois_impute'       => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $recouvrement->update($donnees);

        return redirect()->route('recouvrements.show', $recouvrement)
            ->with('success', 'Règlement mis à jour.');
    }

    public function destroy(ReglementTaxe $recouvrement): RedirectResponse
    {
        $emissionId = $recouvrement->emission_taxe_id;
        $recouvrement->delete();

        if ($emissionId) {
            return redirect()->route('emissions.show', $emissionId)
                ->with('success', 'Règlement supprimé.');
        }

        return redirect()->route('recouvrements.index')
            ->with('success', 'Règlement supprimé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = RecouvrementFiltreForm::fromRequest($request);

        return $excel->telecharger('recouvrements', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Règlement', 'Contribuable', 'N° Identifiant', 'N° Émission / Article',
                'Type', 'Exercice', 'Date règlement', 'Mode', 'Type règlement',
                'Montant', 'Montant imputé', 'N° Quittance',
            ], $entete));

            $filtre->appliquer(ReglementTaxe::with([
                'emissionTaxe.etablissement.contribuable',
                'emissionCotisation.etablissement.contribuable',
                'modeReglement', 'typeReglement', 'exerciceFiscal',
            ]))->orderBy('date_reglement', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $r) {
                    $contrib = $r->emissionTaxe?->etablissement?->contribuable
                        ?? $r->emissionCotisation?->etablissement?->contribuable;

                    $nomContrib = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $numeroEmission = $r->emissionTaxe?->numero_emission
                        ?? $r->emissionCotisation?->numero_article
                        ?? '';

                    $typeEmission = $r->emission_taxe_id ? 'Taxe' : 'Foncier';

                    $writer->addRow(Row::fromValues([
                        $r->numero_reglement ?? '',
                        $nomContrib,
                        $contrib?->numero_identifiant ?? '',
                        $numeroEmission,
                        $typeEmission,
                        $r->exerciceFiscal?->annee ?? '',
                        $r->date_reglement?->format('d/m/Y') ?? '',
                        $r->modeReglement?->libelle ?? '',
                        $r->typeReglement?->libelle ?? '',
                        (float) $r->montant,
                        (float) $r->montant_impute,
                        $r->numero_quittance ?? '',
                    ]));
                }
            });
        });
    }
}
