<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ContribuableFiltreForm;
use App\Models\Collectivite;
use App\Models\Contribuable;
use App\Models\EmissionTaxe;
use App\Models\FormeJuridique;
use App\Models\HistoriqueModification;
use App\Models\Nationalite;
use App\Models\RegimeImposition;
use App\Models\ReglementTaxe;
use App\Models\StatutContribuable;
use App\Models\TypePersonne;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContribuableController extends Controller
{
    private const COLONNES_TRI = [
        'numero_identifiant', 'numero_compte', 'type_personne', 'nom',
        'cellulaire', 'regime_imposition_id', 'statut', 'updated_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $regimes       = $this->selectOptions->charger(RegimeImposition::class, 'libelle_court');
        $typesPersonne = $this->selectOptions->charger(TypePersonne::class, 'code');
        $statuts       = $this->selectOptions->charger(StatutContribuable::class, 'code');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'updated_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ContribuableFiltreForm::fromRequest($request);

        $contribuables = $filtre->appliquer(
            Contribuable::with('regimeImposition')->whereNull('supprime_le')
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('contribuables.index', compact(
            'contribuables', 'filtre', 'regimes', 'typesPersonne', 'statuts',
            'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        $regimes      = $this->selectOptions->charger(RegimeImposition::class, 'libelle_court');
        $statuts      = $this->selectOptions->charger(StatutContribuable::class, 'libelle');
        $nationalites = $this->selectOptions->charger(Nationalite::class, 'libelle');
        $formesJurid  = $this->selectOptions->charger(FormeJuridique::class, 'libelle');

        return view('contribuables.create', compact(
            'regimes', 'statuts', 'nationalites', 'formesJurid',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $typePersonne = $request->input('type_personne');

        $donnees = $request->validate([
            'type_personne'        => ['required', 'in:PP,PM'],
            'numero_compte'        => ['nullable', 'string', 'max:50', Rule::unique('contribuable', 'numero_compte')],
            'statut'               => ['required', 'string', 'max:20'],
            'regime_imposition_id' => ['nullable', 'integer', 'exists:regime_imposition,id'],
            // Contacts
            'telephone'            => ['nullable', 'string', 'max:30'],
            'cellulaire'           => ['nullable', 'string', 'max:30'],
            'fax'                  => ['nullable', 'string', 'max:30'],
            'email'                => ['nullable', 'email', 'max:150'],
            'boite_postale'        => ['nullable', 'string', 'max:50'],
            // PP
            'nom'                  => ['nullable', 'required_if:type_personne,PP', 'string', 'max:100'],
            'prenoms'              => ['nullable', 'string', 'max:150'],
            'sexe'                 => ['nullable', 'in:M,F'],
            'date_naissance'       => ['nullable', 'date'],
            'lieu_naissance'       => ['nullable', 'string', 'max:100'],
            'nationalite_id'       => ['nullable', 'integer', 'exists:nationalite,id'],
            'nature_piece'         => ['nullable', 'string', 'max:50'],
            'numero_piece'         => ['nullable', 'string', 'max:50'],
            'nom_pere'             => ['nullable', 'string', 'max:100'],
            'prenoms_pere'         => ['nullable', 'string', 'max:150'],
            'nom_mere'             => ['nullable', 'string', 'max:100'],
            'prenoms_mere'         => ['nullable', 'string', 'max:150'],
            // PM
            'raison_sociale'           => ['nullable', 'required_if:type_personne,PM', 'string', 'max:200'],
            'sigle'                    => ['nullable', 'string', 'max:20'],
            'denomination_commerciale' => ['nullable', 'string', 'max:200'],
            'forme_juridique_id'       => ['nullable', 'integer', 'exists:forme_juridique,id'],
            'registre_commerce'        => ['nullable', 'string', 'max:50'],
            'date_registre_commerce'   => ['nullable', 'date'],
            'ville_registre_commerce'  => ['nullable', 'string', 'max:100'],
            'nombre_associes'          => ['nullable', 'integer', 'min:0'],
            'capital_social'           => ['nullable', 'numeric', 'min:0'],
        ], [
            'numero_compte.unique' => 'Ce numéro de compte est déjà utilisé par un autre contribuable.',
        ]);

        $annee      = now()->year;
        $dernierNum = Contribuable::where('numero_identifiant', 'like', "CI{$annee}%")
            ->max('numero_identifiant');
        $sequence   = $dernierNum ? ((int) substr($dernierNum, 6)) + 1 : 1;

        $donnees['numero_identifiant'] = 'CI' . $annee . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        $donnees['collectivite_id']    = Collectivite::value('id');
        $donnees['created_by']         = auth()->id();

        try {
            $contribuable = Contribuable::create($donnees);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()->withErrors([
                'numero_identifiant' => 'Le numéro identifiant généré est déjà utilisé. Veuillez réessayer.',
            ]);
        }

        return redirect()->route('contribuables.show', $contribuable)
            ->with('success', 'Contribuable créé avec succès.');
    }

    public function show(Contribuable $contribuable): View
    {
        $contribuable->load([
            'nationalite',
            'formeJuridique',
            'regimeImposition',
            'coordonneesBancaires.banque',
            'etablissements',
            'exonerations.typeExoneration',
            'obligations.natureTaxe',
            'obligations.periodicite',
        ]);

        $etablissementIds = $contribuable->etablissements->pluck('id');

        $totalEmis      = (string) EmissionTaxe::whereIn('etablissement_id', $etablissementIds)->sum('montant_prorata');
        $totalRecouvre  = (string) ReglementTaxe::whereHas('emissionTaxe', function ($q) use ($etablissementIds) {
            $q->whereIn('etablissement_id', $etablissementIds);
        })->sum('montant_impute');
        $solde          = bcsub($totalEmis, $totalRecouvre, 2);

        $emissions = EmissionTaxe::with(['natureTaxe', 'exerciceFiscal', 'periodicite', 'etablissement'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->latest()
            ->limit(50)
            ->get();

        // Resilient : la table peut ne pas exister si la migration n'a pas encore été jouée
        try {
            $historiques = HistoriqueModification::where('model_type', Contribuable::class)
                ->where('model_id', $contribuable->id)
                ->latest('created_at')
                ->limit(50)
                ->get();
        } catch (QueryException) {
            $historiques = collect();
        }

        $auditLabels = $contribuable->auditLabels ?? [];

        // Suppression impossible si le contribuable a des émissions (impositions) / recouvrements
        $suppressionBloquee = $contribuable->etablissements()->whereHas('emissionsTaxe')->exists();

        return view('contribuables.show', compact(
            'contribuable', 'totalEmis', 'totalRecouvre', 'solde', 'emissions',
            'historiques', 'auditLabels', 'suppressionBloquee',
        ));
    }

    public function destroy(Request $request, Contribuable $contribuable): RedirectResponse
    {
        if ($contribuable->etablissements()->whereHas('emissionsTaxe')->exists()) {
            return back()->with('error', 'Impossible de supprimer un contribuable rattaché à des impositions ou recouvrements.');
        }

        $valide = $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ], ['motif_suppression.required' => 'Le motif de suppression est obligatoire.']);

        $contribuable->update([
            'supprime_le'       => now(),
            'motif_suppression' => $valide['motif_suppression'],
            'supprime_par'      => auth()->id(),
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->route('contribuables.index')
            ->with('success', 'Contribuable supprimé.');
    }

    public function edit(Contribuable $contribuable): View
    {
        $contribuable->load(['nationalite', 'formeJuridique', 'regimeImposition']);

        $regimes        = $this->selectOptions->charger(RegimeImposition::class, 'libelle_court');
        $statuts        = $this->selectOptions->charger(StatutContribuable::class, 'libelle');
        $nationalites   = $this->selectOptions->charger(Nationalite::class, 'libelle');
        $formesJurid    = $this->selectOptions->charger(FormeJuridique::class, 'libelle');

        return view('contribuables.edit', compact(
            'contribuable', 'regimes', 'statuts', 'nationalites', 'formesJurid',
        ));
    }

    public function update(Request $request, Contribuable $contribuable): RedirectResponse
    {
        $estPP = $contribuable->type_personne === 'PP';

        $donnees = $request->validate([
            'numero_compte'        => ['nullable', 'string', 'max:50'],
            'statut'               => ['required', 'string', 'max:20'],
            'regime_imposition_id' => ['nullable', 'integer', 'exists:regime_imposition,id'],
            // Contacts
            'telephone'            => ['nullable', 'string', 'max:30'],
            'cellulaire'           => ['nullable', 'string', 'max:30'],
            'fax'                  => ['nullable', 'string', 'max:30'],
            'email'                => ['nullable', 'email', 'max:150'],
            'boite_postale'        => ['nullable', 'string', 'max:50'],
            // PP
            'nom'                  => ['nullable', 'string', 'max:100'],
            'prenoms'              => ['nullable', 'string', 'max:150'],
            'sexe'                 => ['nullable', 'in:M,F'],
            'date_naissance'       => ['nullable', 'date'],
            'lieu_naissance'       => ['nullable', 'string', 'max:100'],
            'nationalite_id'       => ['nullable', 'integer', 'exists:nationalite,id'],
            'nature_piece'         => ['nullable', 'string', 'max:50'],
            'numero_piece'         => ['nullable', 'string', 'max:50'],
            'nom_pere'             => ['nullable', 'string', 'max:100'],
            'prenoms_pere'         => ['nullable', 'string', 'max:150'],
            'nom_mere'             => ['nullable', 'string', 'max:100'],
            'prenoms_mere'         => ['nullable', 'string', 'max:150'],
            // PM
            'raison_sociale'               => ['nullable', 'string', 'max:200'],
            'sigle'                        => ['nullable', 'string', 'max:20'],
            'denomination_commerciale'     => ['nullable', 'string', 'max:200'],
            'forme_juridique_id'           => ['nullable', 'integer', 'exists:forme_juridique,id'],
            'registre_commerce'            => ['nullable', 'string', 'max:50'],
            'date_registre_commerce'       => ['nullable', 'date'],
            'ville_registre_commerce'      => ['nullable', 'string', 'max:100'],
            'nombre_associes'              => ['nullable', 'integer', 'min:0'],
            'capital_social'               => ['nullable', 'numeric', 'min:0'],
        ]);

        $contribuable->update($donnees);

        return redirect()->route('contribuables.show', $contribuable)
            ->with('success', 'Contribuable mis à jour avec succès.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ContribuableFiltreForm::fromRequest($request);

        return $excel->telecharger('contribuables', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Identifiant', 'N° Compte', 'Type', 'Nom / Raison sociale',
                'Téléphone', 'Régime d\'imposition', 'Statut',
            ], $entete));

            $filtre->appliquer(
                Contribuable::with('regimeImposition')->whereNull('supprime_le')
            )->orderBy('updated_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $c) {
                    $nom = $c->type_personne === 'PP'
                        ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? ''))
                        : ($c->raison_sociale ?? '') . ($c->sigle ? ' (' . $c->sigle . ')' : '');

                    $writer->addRow(Row::fromValues([
                        $c->numero_identifiant ?? '',
                        $c->numero_compte ?? '',
                        $c->type_personne ?? '',
                        $nom,
                        $c->cellulaire ?? $c->telephone ?? '',
                        $c->regimeImposition?->libelle_court ?? '',
                        $c->statut ?? '',
                    ]));
                }
            });
        });
    }
}
