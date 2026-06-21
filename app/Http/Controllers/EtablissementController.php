<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\EtablissementFiltreForm;
use App\Models\Activite;
use App\Models\Collectivite;
use App\Models\Commune;
use App\Models\Contribuable;
use App\Models\Etablissement;
use App\Models\ZoneFiscale;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class EtablissementController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:ETAB_CONSULTER', only: ['index', 'show', 'export', 'zonesFiscales']),
            new Middleware('can:ETAB_CREER', only: ['create', 'store']),
            new Middleware('can:ETAB_MODIFIER', only: ['edit', 'update']),
            new Middleware('can:ETAB_SUPPRIMER', only: ['destroy']),
        ];
    }

    private const COLONNES_TRI = [
        'numero', 'contribuable_id', 'denomination', 'type_etablissement',
        'activite_id', 'commune_id', 'telephone', 'statut', 'updated_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $communes  = $this->selectOptions->charger(Commune::class, 'libelle');
        $activites = $this->selectOptions->charger(Activite::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'updated_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = EtablissementFiltreForm::fromRequest($request);

        $etablissements = $filtre->appliquer(
            Etablissement::with(['contribuable', 'activite', 'commune'])->whereNull('supprime_le')
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('etablissements.index', compact(
            'etablissements', 'filtre', 'communes', 'activites',
            'sortActuel', 'dirActuelle',
        ));
    }

    public function create(Request $request): View
    {
        $contribuable  = $request->query('contribuable_id')
            ? Contribuable::findOrFail($request->query('contribuable_id'))
            : null;

        $activites    = $this->selectOptions->charger(Activite::class, 'libelle');
        $communes     = $this->selectOptions->charger(Commune::class, 'libelle');

        // Les zones dépendent de la commune : on ne charge que celles de la commune
        // déjà choisie (repopulation après erreur de validation), sinon liste vide.
        $zonesFiscales = old('commune_id')
            ? $this->selectOptions->charger(ZoneFiscale::class, 'libelle', ['commune_id' => old('commune_id')])
            : collect();

        return view('etablissements.create', compact(
            'contribuable', 'activites', 'communes', 'zonesFiscales',
        ));
    }

    /**
     * Retourne les zones fiscales d'une commune (JSON) pour alimenter le
     * select dépendant du formulaire établissement.
     */
    public function zonesFiscales(Commune $commune): \Illuminate\Http\JsonResponse
    {
        $zones = $this->selectOptions
            ->charger(ZoneFiscale::class, 'libelle', ['commune_id' => $commune->id])
            ->map(fn (ZoneFiscale $z) => ['id' => $z->id, 'libelle' => $z->libelle])
            ->values();

        return response()->json($zones);
    }

    public function store(Request $request): RedirectResponse
    {
        // Résolution du matricule saisi → contribuable_id
        if ($request->filled('numero_identifiant') && ! $request->filled('contribuable_id')) {
            $contrib = Contribuable::where('numero_identifiant', $request->input('numero_identifiant'))
                ->whereNull('supprime_le')
                ->first();

            if (! $contrib) {
                return back()->withInput()->withErrors([
                    'numero_identifiant' => 'Aucun contribuable trouvé avec le matricule « ' . $request->input('numero_identifiant') . ' ».',
                ]);
            }

            $request->merge(['contribuable_id' => $contrib->id]);
        }

        $donnees = $request->validate([
            'contribuable_id'      => ['required', 'integer', 'exists:contribuable,id'],
            'denomination'         => ['nullable', 'string', 'max:255'],
            'type_etablissement'   => ['required', 'in:PRINCIPAL,SECONDAIRE'],
            'activite_id'          => ['required', 'integer', 'exists:activite,id'],
            'commune_id'           => ['required', 'integer', 'exists:commune,id'],
            'zone_fiscale_id'      => ['required', 'integer', 'exists:zone_fiscale,id'],
            'ca_reference'         => ['nullable', 'numeric', 'min:0'],
            'adresse'              => ['nullable', 'string', 'max:64'],
            'date_debut_activite'  => ['required', 'date'],
            'telephone'            => ['nullable', 'string', 'max:32'],
            'email'                => ['nullable', 'email', 'max:128'],
            'boite_postale'        => ['nullable', 'string', 'max:32'],
            'statut'               => ['required', 'in:ACTIF,CESSE,TRANSFERE,SOMMEIL'],
        ]);

        // Un contribuable ne peut avoir qu'un seul établissement principal actif
        if ($donnees['type_etablissement'] === 'PRINCIPAL' && $donnees['statut'] === 'ACTIF') {
            $dejaPrincipalActif = Etablissement::where('contribuable_id', $donnees['contribuable_id'])
                ->where('type_etablissement', 'PRINCIPAL')
                ->where('statut', 'ACTIF')
                ->exists();

            if ($dejaPrincipalActif) {
                return back()->withInput()->withErrors([
                    'type_etablissement' => 'Ce contribuable possède déjà un établissement principal actif. Veuillez d\'abord clôturer ou transférer l\'établissement existant.',
                ]);
            }
        }

        // Génération du numéro métier : ET + année + séquence (10 car., UNIQUE)
        $annee      = now()->year;
        $dernierNum = Etablissement::where('numero', 'like', "ET{$annee}%")->max('numero');
        $sequence   = $dernierNum ? ((int) substr($dernierNum, 6)) + 1 : 1;

        $donnees['numero']          = 'ET' . $annee . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $donnees['collectivite_id'] = Collectivite::value('id');
        $donnees['created_by']      = auth()->id();

        try {
            $etablissement = Etablissement::create($donnees);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()->withErrors([
                'numero' => 'Le numéro d\'établissement généré est déjà utilisé. Veuillez réessayer.',
            ]);
        }

        return redirect()->route('etablissements.show', $etablissement)
            ->with('success', 'Établissement créé avec succès.');
    }

    public function show(Etablissement $etablissement): View
    {
        $etablissement->load([
            'contribuable.regimeImposition',
            'activite.categorieActivite',
            'commune',
            'zoneFiscale',
            'emissionsTaxe.natureTaxe',
            'emissionsTaxe.periodicite',
            'emissionsTaxe.exerciceFiscal',
        ]);

        try {
            $historiques = $etablissement->historique()->latest('created_at')->take(50)->get();
        } catch (\Illuminate\Database\QueryException) {
            $historiques = collect();
        }

        $totalEmis   = $etablissement->emissionsTaxe->reduce(function ($carry, $e) {
            $base = $e->montant_prorata > 0 ? (string) $e->montant_prorata : (string) $e->montant_annuel;
            return bcadd($carry, $base, 2);
        }, '0');
        $totalSolde  = $etablissement->emissionsTaxe->reduce(function ($carry, $e) {
            return bcadd($carry, $e->soldeDu(), 2);
        }, '0');
        $totalRegle  = bcsub($totalEmis, $totalSolde, 2);

        $suppressionBloquee = $etablissement->emissionsTaxe()->exists();

        return view('etablissements.show', compact(
            'etablissement', 'historiques',
            'totalEmis', 'totalRegle', 'totalSolde', 'suppressionBloquee',
        ));
    }

    public function edit(Etablissement $etablissement): View
    {
        $etablissement->load(['contribuable', 'activite', 'commune', 'zoneFiscale']);

        $activites    = $this->selectOptions->charger(Activite::class, 'libelle');
        $communes     = $this->selectOptions->charger(Commune::class, 'libelle');
        $zonesFiscales = $this->selectOptions->charger(ZoneFiscale::class, 'libelle');

        return view('etablissements.edit', compact(
            'etablissement', 'activites', 'communes', 'zonesFiscales',
        ));
    }

    public function update(Request $request, Etablissement $etablissement): RedirectResponse
    {
        $request->request->remove('contribuable_id');

        $donnees = $request->validate([
            'denomination'        => ['nullable', 'string', 'max:255'],
            'type_etablissement'  => ['required', 'in:PRINCIPAL,SECONDAIRE'],
            'activite_id'         => ['required', 'integer', 'exists:activite,id'],
            'commune_id'          => ['required', 'integer', 'exists:commune,id'],
            'zone_fiscale_id'     => ['required', 'integer', 'exists:zone_fiscale,id'],
            'ca_reference'        => ['nullable', 'numeric', 'min:0'],
            'adresse'             => ['nullable', 'string', 'max:64'],
            'date_debut_activite' => ['required', 'date'],
            'date_cessation'      => ['nullable', 'date'],
            'telephone'           => ['nullable', 'string', 'max:32'],
            'email'               => ['nullable', 'email', 'max:128'],
            'boite_postale'       => ['nullable', 'string', 'max:32'],
            'statut'              => ['required', 'in:ACTIF,CESSE,TRANSFERE,SOMMEIL'],
        ]);

        // Un contribuable ne peut avoir qu'un seul établissement principal actif (hors lui-même)
        if ($donnees['type_etablissement'] === 'PRINCIPAL' && $donnees['statut'] === 'ACTIF') {
            $dejaPrincipalActif = Etablissement::where('contribuable_id', $etablissement->contribuable_id)
                ->where('type_etablissement', 'PRINCIPAL')
                ->where('statut', 'ACTIF')
                ->where('id', '!=', $etablissement->id)
                ->exists();

            if ($dejaPrincipalActif) {
                return back()->withInput()->withErrors([
                    'type_etablissement' => 'Ce contribuable possède déjà un autre établissement principal actif. Veuillez d\'abord clôturer ou transférer l\'établissement existant.',
                ]);
            }
        }

        $donnees['updated_by'] = auth()->id();
        $etablissement->update($donnees);

        return redirect()->route('etablissements.show', $etablissement)
            ->with('success', 'Établissement mis à jour avec succès.');
    }

    public function destroy(Request $request, Etablissement $etablissement): RedirectResponse
    {
        if ($etablissement->emissionsTaxe()->exists()) {
            return back()->with('error', 'Impossible de supprimer un établissement rattaché à des émissions ou recouvrements.');
        }

        $valide = $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ], ['motif_suppression.required' => 'Le motif de suppression est obligatoire.']);

        $contribuableId = $etablissement->contribuable_id;
        $etablissement->update([
            'supprime_le'       => now(),
            'motif_suppression' => $valide['motif_suppression'],
            'supprime_par'      => auth()->id(),
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->route('contribuables.show', $contribuableId)
            ->with('success', 'Établissement supprimé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = EtablissementFiltreForm::fromRequest($request);

        return $excel->telecharger('etablissements', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Établissement', 'Contribuable', 'N° Identifiant', 'Dénomination',
                'Type', 'Activité', 'Commune', 'Téléphone', 'Statut',
            ], $entete));

            $filtre->appliquer(
                Etablissement::with(['contribuable', 'activite', 'commune'])->whereNull('supprime_le')
            )->orderBy('updated_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $e) {
                    $contrib = $e->contribuable;
                    $nomContrib = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $e->numero ?? '',
                        $nomContrib,
                        $contrib?->numero_identifiant ?? '',
                        $e->denomination ?? '',
                        $e->type_etablissement ?? '',
                        $e->activite?->libelle ?? '',
                        $e->commune?->libelle ?? '',
                        $e->telephone ?? '',
                        $e->statut ?? '',
                    ]));
                }
            });
        });
    }
}
