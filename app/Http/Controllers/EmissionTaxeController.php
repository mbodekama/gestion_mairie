<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\EmissionTaxeFiltreForm;
use App\Models\Collectivite;
use App\Models\EmissionTaxe;
use App\Models\Etablissement;
use App\Models\ExerciceFiscal;
use App\Models\NatureTaxe;
use App\Models\Obligation;
use App\Models\Periodicite;
use App\Pdf\AvisImposition;
use App\Services\ExcelExportService;
use App\Services\ExonerationService;
use App\Services\LiquidationTaxeService;
use App\Services\SelectOptionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class EmissionTaxeController extends Controller
{
    private const COLONNES_TRI = [
        'numero_emission', 'etablissement_id', 'nature_taxe_id',
        'exercice_fiscal_id', 'periodicite_id', 'montant_annuel', 'date_liquidation', 'updated_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $exercices = $this->selectOptions->charger(ExerciceFiscal::class, 'annee');
        $naturesTaxe = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        $sortActuel = in_array($request->query('sort'), self::COLONNES_TRI, true)
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

    /**
     * Étape 1 (sans `code`) : saisie du numéro d'établissement.
     * Étape 2 (avec `code`) : formulaire d'émission, la nature de taxe étant
     * restreinte aux obligations auxquelles le contribuable est assujetti.
     */
    public function create(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));

        // Compatibilité : arrivée depuis un établissement (etablissement_id)
        if ($code === '' && $request->filled('etablissement_id')) {
            $code = Etablissement::find($request->query('etablissement_id'))?->numero ?? '';
        }

        // Compatibilité : arrivée depuis un contribuable n'ayant qu'un seul établissement
        if ($code === '' && $request->filled('contribuable_id')) {
            $etabs = Etablissement::whereNull('supprime_le')
                ->where('contribuable_id', $request->query('contribuable_id'))->get();
            if ($etabs->count() === 1) {
                $code = $etabs->first()->numero;
            }
        }

        // Étape 1 : formulaire de saisie du numéro d'établissement
        if ($code === '') {
            return view('emissions.create', [
                'exerciceFiscalId' => $request->query('exercice_fiscal_id'),
            ]);
        }

        // Étape 2 : résolution de l'établissement
        $etablissement = Etablissement::with('contribuable')
            ->whereNull('supprime_le')
            ->where('numero', $code)
            ->first();

        if (! $etablissement) {
            return view('emissions.create', [
                'code' => $code,
                'erreurCode' => "Aucun établissement trouvé pour « {$code} ».",
                'exerciceFiscalId' => $request->query('exercice_fiscal_id'),
            ]);
        }

        // Nature de taxe limitée aux obligations du contribuable
        $obligations = Obligation::with(['natureTaxe', 'periodicite'])
            ->where('contribuable_id', $etablissement->contribuable_id)
            ->get();

        $exerciceDefaut = $request->query('exercice_fiscal_id')
            ? ExerciceFiscal::find($request->query('exercice_fiscal_id'))
            : null;

        $exercices = ExerciceFiscal::where('cloture', false)->orderBy('annee', 'desc')->get();
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('emissions.emettre', compact(
            'code', 'etablissement', 'obligations', 'exerciceDefaut', 'exercices', 'periodicites',
        ));
    }

    /**
     * Calcule les montants d'une émission depuis le barème applicable (JSON).
     * Alimente le bouton « Calculer » du formulaire de création.
     */
    public function liquider(Request $request, LiquidationTaxeService $liquidation): JsonResponse
    {
        $valide = $request->validate([
            'etablissement_id' => ['nullable', 'integer', 'exists:etablissement,id'],
            'nature_taxe_id' => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id' => ['required', 'integer', 'exists:periodicite,id'],
            'ca_annuel' => ['nullable', 'numeric', 'min:0'],
            'nb_mois_prorata' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        // Catégorie d'activité de l'établissement (pour cibler le barème spécifique)
        $categorieId = empty($valide['etablissement_id'])
            ? null
            : Etablissement::with('activite')->find($valide['etablissement_id'])?->activite?->categorie_activite_id;

        $resultat = $liquidation->liquider(
            natureTaxeId: (int) $valide['nature_taxe_id'],
            periodiciteId: (int) $valide['periodicite_id'],
            caAnnuel: (string) ($valide['ca_annuel'] ?? '0'),
            categorieActiviteId: $categorieId,
            nbMoisProrata: $valide['nb_mois_prorata'] ?? null,
        );

        return response()->json($resultat);
    }

    public function store(Request $request, ExonerationService $exonerations): RedirectResponse
    {
        $donnees = $request->validate([
            'etablissement_id' => ['required', 'integer', 'exists:etablissement,id'],
            'nature_taxe_id' => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id' => ['required', 'integer', 'exists:periodicite,id'],
            'exercice_fiscal_id' => ['required', 'integer', 'exists:exercice_fiscal,id'],
            'ca_annuel' => ['nullable', 'numeric', 'min:0'],
            'montant_annuel' => ['required', 'numeric', 'min:0'],
            'montant_periode' => ['nullable', 'numeric', 'min:0'],
            'nb_mois_prorata' => ['nullable', 'integer', 'min:1', 'max:12'],
            'montant_prorata' => ['nullable', 'numeric', 'min:0'],
            'date_declaration' => ['nullable', 'date'],
            'date_liquidation' => ['nullable', 'date'],
        ]);

        $exercice = ExerciceFiscal::findOrFail($donnees['exercice_fiscal_id']);
        if ($exercice->cloture) {
            return back()->withInput()->with('error', 'Impossible d\'émettre sur un exercice clôturé.');
        }

        $etab = Etablissement::findOrFail($donnees['etablissement_id']);

        // La nature de taxe doit faire partie des obligations du contribuable
        $estAssujetti = Obligation::where('contribuable_id', $etab->contribuable_id)
            ->where('nature_taxe_id', $donnees['nature_taxe_id'])
            ->exists();

        if (! $estAssujetti) {
            return back()->withInput()->withErrors([
                'nature_taxe_id' => "Le contribuable n'est pas assujetti à cette nature de taxe.",
            ]);
        }

        $collectivite = Collectivite::first();
        $annee = $exercice->annee;

        // Exonération éventuelle : abattement automatique au taux de la ligne active.
        $baseDue = (float) ($donnees['montant_prorata'] ?? 0) > 0
            ? (string) $donnees['montant_prorata']
            : (string) $donnees['montant_annuel'];

        $exo = $exonerations->appliquer($etab->contribuable_id, (int) $donnees['nature_taxe_id'], (int) $annee, $baseDue);

        $messageExo = '';
        if ($exo) {
            foreach (['montant_annuel', 'montant_periode', 'montant_prorata'] as $champ) {
                if (isset($donnees[$champ]) && $donnees[$champ] !== null && $donnees[$champ] !== '') {
                    $donnees[$champ] = bcmul((string) $donnees[$champ], $exo['facteur'], 2);
                }
            }
            $donnees['exoneration_id'] = $exo['exoneration_id'];
            $donnees['montant_exonere'] = $exo['montant_exonere'];
            $messageExo = ' Exonération appliquée ('.rtrim(rtrim($exo['taux'], '0'), '.').' %).';
        }

        $seq = EmissionTaxe::where('numero_emission', 'like', "EMI{$annee}%")
            ->orderBy('numero_emission', 'desc')
            ->value('numero_emission');
        $seq = $seq ? ((int) substr($seq, -6) + 1) : 1;

        // Numéros métier générés : émission, fiche (même séquence annuelle) et article
        $donnees['numero_emission'] = "EMI{$annee}".str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['numero_fiche'] = "FE{$annee}".str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['numero_article'] = $etab->numero.'/'.$annee;
        $donnees['collectivite_id'] = $collectivite?->id;
        $donnees['created_by'] = auth()->id();

        $emission = EmissionTaxe::create($donnees);

        return redirect()->route('emissions.show', $emission)
            ->with('success', 'Émission créée avec succès.'.$messageExo);
    }

    public function show(EmissionTaxe $emission): View
    {
        $emission->load([
            'etablissement.contribuable',
            'etablissement.commune',
            'natureTaxe',
            'periodicite',
            'exerciceFiscal',
            'exoneration',
            'reglements.modeReglement',
            'reglements.typeReglement',
            'reglements.banque',
        ]);

        $montantBase = $emission->montant_prorata > 0 ? $emission->montant_prorata : $emission->montant_annuel;
        $totalRegle = $emission->reglements->reduce(
            fn ($carry, $r) => bcadd($carry, (string) $r->montant_impute, 2), '0'
        );
        $soldeDu = $emission->soldeDu();

        $suppressionBloquee = $emission->reglements()->exists();

        return view('emissions.show', compact('emission', 'montantBase', 'totalRegle', 'soldeDu', 'suppressionBloquee'));
    }

    /**
     * Avis d'imposition (PDF) d'une émission, faisant apparaître le montant brut,
     * l'exonération éventuelle et le montant net dû.
     */
    public function avis(EmissionTaxe $emission): Response
    {
        $emission->load([
            'etablissement.contribuable', 'natureTaxe', 'periodicite',
            'exerciceFiscal', 'exoneration',
        ]);

        $collectivite = Collectivite::first();

        return (new AvisImposition($emission, $collectivite))
            ->reponse('avis-imposition-'.$emission->numero_emission.'.pdf');
    }

    public function edit(EmissionTaxe $emission): View
    {
        $emission->load(['etablissement.contribuable', 'natureTaxe', 'periodicite', 'exerciceFiscal']);

        $naturesTaxe = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        $champsFiges = $emission->reglements()->exists();

        return view('emissions.edit', compact('emission', 'naturesTaxe', 'periodicites', 'champsFiges'));
    }

    public function update(Request $request, EmissionTaxe $emission): RedirectResponse
    {
        if ($emission->exerciceFiscal?->cloture) {
            return back()->with('error', 'Exercice clôturé — modification impossible.');
        }

        // Dès qu'un recouvrement (même partiel) existe, les champs de calcul sont figés :
        // seules les dates restent modifiables.
        if ($emission->reglements()->exists()) {
            $donnees = $request->validate([
                'date_declaration' => ['nullable', 'date'],
                'date_liquidation' => ['nullable', 'date'],
            ]);
        } else {
            $donnees = $request->validate([
                'nature_taxe_id' => ['required', 'integer', 'exists:nature_taxe,id'],
                'periodicite_id' => ['required', 'integer', 'exists:periodicite,id'],
                'ca_annuel' => ['nullable', 'numeric', 'min:0'],
                'montant_annuel' => ['required', 'numeric', 'min:0'],
                'montant_periode' => ['nullable', 'numeric', 'min:0'],
                'nb_mois_prorata' => ['nullable', 'integer', 'min:1', 'max:12'],
                'montant_prorata' => ['nullable', 'numeric', 'min:0'],
                'date_declaration' => ['nullable', 'date'],
                'date_liquidation' => ['nullable', 'date'],
            ]);
        }

        $donnees['updated_by'] = auth()->id();
        $emission->update($donnees);

        return redirect()->route('emissions.show', $emission)
            ->with('success', 'Émission mise à jour.');
    }

    public function destroy(Request $request, EmissionTaxe $emission): RedirectResponse
    {
        if ($emission->reglements()->exists()) {
            return back()->with('error', 'Impossible de supprimer une émission rattachée à des recouvrements.');
        }

        $valide = $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ], ['motif_suppression.required' => 'Le motif de suppression est obligatoire.']);

        $etablissementId = $emission->etablissement_id;
        $emission->update([
            'supprime_le' => now(),
            'motif_suppression' => $valide['motif_suppression'],
            'supprime_par' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('etablissements.show', $etablissementId)
            ->with('success', 'Émission supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = EmissionTaxeFiltreForm::fromRequest($request);

        return $excel->telecharger('emissions_taxes', function (Writer $writer) use ($filtre): void {
            $entete = (new Style)->setFontBold();
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
                            ? trim(($contrib->nom ?? '').' '.($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $montant = (float) ($e->montant_prorata > 0 ? $e->montant_prorata : $e->montant_annuel);
                    $solde = (float) $e->soldeDu();

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
