<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ExonerationFiltreForm;
use App\Models\Collectivite;
use App\Models\Contribuable;
use App\Models\Exoneration;
use App\Models\LigneExoneration;
use App\Models\NatureTaxe;
use App\Models\TypeExoneration;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class ExonerationController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:EXO_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:EXO_CREER', only: ['create', 'store']),
            new Middleware('can:EXO_MODIFIER', only: ['edit', 'update']),
            new Middleware('can:EXO_SUPPRIMER', only: ['destroy']),
        ];
    }

    private const COLONNES_TRI = [
        'numero', 'contribuable_id', 'type_exoneration_id',
        'date_decret', 'date_debut', 'date_fin', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $typesExoneration = $this->selectOptions->charger(TypeExoneration::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'created_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ExonerationFiltreForm::fromRequest($request);

        $exonerations = $filtre->appliquer(
            Exoneration::with(['contribuable', 'typeExoneration'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('exonerations.index', compact(
            'exonerations', 'filtre', 'typesExoneration', 'sortActuel', 'dirActuelle',
        ));
    }

    public function create(Request $request): View
    {
        $contribuable = $request->filled('contribuable_id')
            ? Contribuable::whereNull('supprime_le')->findOrFail($request->query('contribuable_id'))
            : null;

        $typesExoneration = $this->selectOptions->charger(TypeExoneration::class, 'libelle');
        $naturesTaxe      = NatureTaxe::orderBy('code')->get();

        return view('exonerations.create', compact('contribuable', 'typesExoneration', 'naturesTaxe'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Résolution du matricule saisi → contribuable_id
        if ($request->filled('numero_identifiant') && ! $request->filled('contribuable_id')) {
            $contrib = Contribuable::where('numero_identifiant', $request->input('numero_identifiant'))
                ->whereNull('supprime_le')->first();

            if (! $contrib) {
                return back()->withInput()->withErrors([
                    'numero_identifiant' => 'Aucun contribuable trouvé avec le matricule « ' . $request->input('numero_identifiant') . ' ».',
                ]);
            }

            $request->merge(['contribuable_id' => $contrib->id]);
        }

        $donnees = $this->valider($request, contribuableRequis: true);
        $lignes  = $donnees['lignes'];
        unset($donnees['lignes']);

        $annee = now()->year;
        $seq   = Exoneration::where('numero', 'like', "EXO{$annee}%")
            ->orderBy('numero', 'desc')->value('numero');
        $seq   = $seq ? ((int) substr($seq, -6) + 1) : 1;

        $donnees['numero']          = "EXO{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT);
        $donnees['collectivite_id'] = Collectivite::value('id');
        $donnees['created_by']      = auth()->id();

        $exoneration = DB::transaction(function () use ($donnees, $lignes) {
            $exoneration = Exoneration::create($donnees);
            $this->synchroniserLignes($exoneration, $lignes);

            return $exoneration;
        });

        return redirect()->route('exonerations.show', $exoneration)
            ->with('success', 'Exonération créée — N° ' . $exoneration->numero);
    }

    public function show(Exoneration $exoneration): View
    {
        $exoneration->load(['contribuable', 'typeExoneration', 'lignes.natureTaxe']);

        return view('exonerations.show', compact('exoneration'));
    }

    public function edit(Exoneration $exoneration): View
    {
        $exoneration->load(['contribuable', 'typeExoneration', 'lignes']);

        $typesExoneration = $this->selectOptions->charger(TypeExoneration::class, 'libelle');
        $naturesTaxe      = NatureTaxe::orderBy('code')->get();

        return view('exonerations.edit', compact('exoneration', 'typesExoneration', 'naturesTaxe'));
    }

    public function update(Request $request, Exoneration $exoneration): RedirectResponse
    {
        $donnees = $this->valider($request);
        $lignes  = $donnees['lignes'];
        unset($donnees['lignes']);

        DB::transaction(function () use ($exoneration, $donnees, $lignes) {
            $exoneration->update($donnees);
            $this->synchroniserLignes($exoneration, $lignes);
        });

        return redirect()->route('exonerations.show', $exoneration)
            ->with('success', 'Exonération mise à jour.');
    }

    /** Remplace l'ensemble des lignes (taxes exonérées) de l'exonération. */
    private function synchroniserLignes(Exoneration $exoneration, array $lignes): void
    {
        $exoneration->lignes()->delete();

        foreach ($lignes as $ligne) {
            LigneExoneration::create([
                'exoneration_id'   => $exoneration->id,
                'nature_taxe_id'   => $ligne['nature_taxe_id'],
                'annee_application' => $ligne['annee_application'],
                'taux'             => $ligne['taux'],
                'created_by'       => auth()->id(),
            ]);
        }
    }

    public function destroy(Exoneration $exoneration): RedirectResponse
    {
        $exoneration->delete();

        return redirect()->route('exonerations.index')
            ->with('success', 'Exonération supprimée.');
    }

    /** Validation partagée store/update (le contribuable n'est requis qu'à la création). */
    private function valider(Request $request, bool $contribuableRequis = false): array
    {
        return $request->validate([
            'contribuable_id'     => [$contribuableRequis ? 'required' : 'sometimes', 'integer', 'exists:contribuable,id'],
            'type_exoneration_id' => ['required', 'integer', 'exists:type_exoneration,id'],
            'reference_decret'    => ['nullable', 'string', 'max:32'],
            'date_decret'         => ['nullable', 'date'],
            'zone'                => ['nullable', 'string', 'max:2'],
            'date_debut'          => ['nullable', 'date'],
            'date_fin'            => ['nullable', 'date', 'after_or_equal:date_debut'],

            // Taxes exonérées : nature + année d'application + taux d'exonération
            'lignes'                     => ['required', 'array', 'min:1'],
            'lignes.*.nature_taxe_id'    => ['required', 'integer', 'exists:nature_taxe,id'],
            'lignes.*.annee_application' => ['required', 'integer', 'min:2000', 'max:2100'],
            'lignes.*.taux'              => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'lignes.required' => 'Ajoutez au moins une taxe exonérée.',
        ]);
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ExonerationFiltreForm::fromRequest($request);

        return $excel->telecharger('exonerations', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Exonération', 'Contribuable', 'N° Identifiant', 'Type exonération',
                'Réf. décret', 'Date décret', 'Date début', 'Date fin', 'Zone',
            ], $entete));

            $filtre->appliquer(
                Exoneration::with(['contribuable', 'typeExoneration'])
            )->orderBy('created_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $e) {
                    $contrib = $e->contribuable;
                    $nom = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $e->numero ?? '',
                        $nom,
                        $contrib?->numero_identifiant ?? '',
                        $e->typeExoneration?->libelle ?? '',
                        $e->reference_decret ?? '',
                        $e->date_decret?->format('d/m/Y') ?? '',
                        $e->date_debut?->format('d/m/Y') ?? '',
                        $e->date_fin?->format('d/m/Y') ?? '',
                        $e->zone ?? '',
                    ]));
                }
            });
        });
    }
}
