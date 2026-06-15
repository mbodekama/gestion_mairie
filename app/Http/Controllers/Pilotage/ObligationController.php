<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ObligationFiltreForm;
use App\Models\Collectivite;
use App\Models\Contribuable;
use App\Models\NatureTaxe;
use App\Models\Obligation;
use App\Models\Periodicite;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObligationController extends Controller
{
    private const COLONNES_TRI = [
        'contribuable_id', 'nature_taxe_id', 'periodicite_id', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'created_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ObligationFiltreForm::fromRequest($request);

        $obligations = $filtre->appliquer(
            Obligation::with(['contribuable', 'natureTaxe', 'periodicite'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('pilotage.obligations.index', compact(
            'obligations', 'filtre', 'naturesTaxe', 'periodicites', 'sortActuel', 'dirActuelle',
        ));
    }

    /**
     * Étape 1 (sans `code`) : saisie du numéro de contribuable.
     * Étape 2 (avec `code`) : fiche contribuable + catalogue des natures de taxe
     * avec, pour chacune, l'état d'assignation de l'obligation au contribuable.
     */
    public function create(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));

        // Étape 1 : formulaire de saisie du numéro
        if ($code === '') {
            return view('pilotage.obligations.create');
        }

        // Étape 2 : résolution du contribuable
        $contribuable = $this->resoudreContribuable($code);

        if (! $contribuable) {
            return view('pilotage.obligations.create', [
                'code'       => $code,
                'erreurCode' => "Aucun contribuable trouvé pour « {$code} ».",
            ]);
        }

        // Catalogue complet des natures de taxe (les obligations possibles)
        $naturesTaxe = NatureTaxe::orderBy('code')->get();

        // Obligations déjà assignées au contribuable, indexées par nature de taxe
        $obligations = Obligation::where('contribuable_id', $contribuable->id)
            ->get()
            ->keyBy('nature_taxe_id');

        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('pilotage.obligations.assigner', compact(
            'code', 'contribuable', 'naturesTaxe', 'obligations', 'periodicites',
        ));
    }

    /**
     * Synchronise les obligations du contribuable : crée celles nouvellement
     * cochées, met à jour la périodicité, et supprime celles décochées.
     */
    public function store(Request $request): RedirectResponse
    {
        $contribuable = $this->resoudreContribuable(trim((string) $request->input('code', '')));

        if (! $contribuable) {
            return back()->withInput()->withErrors([
                'code' => 'Contribuable introuvable. Reprenez la saisie du numéro.',
            ]);
        }

        $valide = $request->validate([
            'obligations'    => ['nullable', 'array'],
            'obligations.*'  => ['integer', 'exists:nature_taxe,id'],
            'periodicite'    => ['nullable', 'array'],
            'periodicite.*'  => ['nullable', 'integer', 'exists:periodicite,id'],
        ]);

        $natureCochees = array_map('intval', $valide['obligations'] ?? []);
        $periodicites  = $valide['periodicite'] ?? [];

        // État actuel des obligations du contribuable, indexé par nature de taxe
        $existantes = Obligation::where('contribuable_id', $contribuable->id)
            ->get()
            ->keyBy('nature_taxe_id');

        $collectiviteId = Collectivite::value('id');
        $nbAssignees = $nbRetirees = 0;

        DB::transaction(function () use (
            $natureCochees, $periodicites, $existantes, $contribuable,
            $collectiviteId, &$nbAssignees, &$nbRetirees
        ): void {
            // Création / mise à jour des obligations cochées
            foreach ($natureCochees as $natureId) {
                $periodiciteId = $periodicites[$natureId] ?? null;

                if ($existantes->has($natureId)) {
                    $obligation = $existantes->get($natureId);
                    if ((int) $obligation->periodicite_id !== (int) $periodiciteId) {
                        $obligation->update(['periodicite_id' => $periodiciteId ?: null]);
                    }
                } else {
                    Obligation::create([
                        'contribuable_id' => $contribuable->id,
                        'collectivite_id' => $collectiviteId,
                        'nature_taxe_id'  => $natureId,
                        'periodicite_id'  => $periodiciteId ?: null,
                        'created_by'      => auth()->id(),
                    ]);
                    $nbAssignees++;
                }
            }

            // Suppression des obligations décochées
            foreach ($existantes as $natureId => $obligation) {
                if (! in_array((int) $natureId, $natureCochees, true)) {
                    $obligation->delete();
                    $nbRetirees++;
                }
            }
        });

        $message = trim(
            ($nbAssignees ? "{$nbAssignees} obligation(s) assignée(s). " : '') .
            ($nbRetirees ? "{$nbRetirees} obligation(s) retirée(s). " : '')
        ) ?: 'Aucune modification.';

        return redirect()->route('contribuables.show', $contribuable)
            ->with('success', $message);
    }

    /**
     * Résout un numéro saisi en contribuable (par `numero_identifiant` ou
     * `numero_compte`), en ignorant les contribuables supprimés.
     */
    private function resoudreContribuable(string $code): ?Contribuable
    {
        if ($code === '') {
            return null;
        }

        return Contribuable::whereNull('supprime_le')
            ->where(fn ($q) => $q->where('numero_identifiant', $code)->orWhere('numero_compte', $code))
            ->first();
    }

    public function show(Obligation $obligation): View
    {
        $obligation->load(['contribuable', 'natureTaxe', 'periodicite']);

        return view('pilotage.obligations.show', compact('obligation'));
    }

    public function edit(Obligation $obligation): View
    {
        $obligation->load(['contribuable', 'natureTaxe', 'periodicite']);

        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('pilotage.obligations.edit', compact('obligation', 'naturesTaxe', 'periodicites'));
    }

    public function update(Request $request, Obligation $obligation): RedirectResponse
    {
        $donnees = $request->validate([
            'nature_taxe_id' => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id' => ['nullable', 'integer', 'exists:periodicite,id'],
        ]);

        // Unicité (contribuable, nature) hors l'obligation courante
        $existe = Obligation::where('contribuable_id', $obligation->contribuable_id)
            ->where('nature_taxe_id', $donnees['nature_taxe_id'])
            ->where('id', '!=', $obligation->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'nature_taxe_id' => 'Ce contribuable a déjà une obligation pour cette nature de taxe.',
            ]);
        }

        $obligation->update($donnees);

        return redirect()->route('pilotage.obligations.index')
            ->with('success', 'Obligation fiscale mise à jour.');
    }

    public function destroy(Obligation $obligation): RedirectResponse
    {
        $obligation->delete();

        return redirect()->route('pilotage.obligations.index')
            ->with('success', 'Obligation fiscale supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ObligationFiltreForm::fromRequest($request);

        return $excel->telecharger('obligations_fiscales', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Contribuable', 'N° Identifiant', 'Nature de taxe', 'Périodicité', 'Date création',
            ], $entete));

            $filtre->appliquer(Obligation::with(['contribuable', 'natureTaxe', 'periodicite']))
                ->orderBy('created_at', 'desc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $o) {
                        $contrib = $o->contribuable;
                        $nom = $contrib
                            ? ($contrib->type_personne === 'PP'
                                ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                : ($contrib->raison_sociale ?? ''))
                            : '';

                        $writer->addRow(Row::fromValues([
                            $nom,
                            $contrib?->numero_identifiant ?? '',
                            $o->natureTaxe?->libelle ?? '',
                            $o->periodicite?->libelle ?? '',
                            $o->created_at?->format('d/m/Y') ?? '',
                        ]));
                    }
                });
        });
    }
}
