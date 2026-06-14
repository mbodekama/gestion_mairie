<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\BaremeTaxeFiltreForm;
use App\Models\BaremeTaxe;
use App\Models\CategorieActivite;
use App\Models\NatureTaxe;
use App\Models\Periodicite;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BaremeTaxeController extends Controller
{
    private const COLONNES_TRI = [
        'nature_taxe_id', 'categorie_activite_id', 'periodicite_id',
        'ca_borne_inf', 'ca_borne_sup', 'taux',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request): View
    {
        $naturesTaxe = $this->selectOptions->charger(NatureTaxe::class, 'libelle_court');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');
        $categories  = $this->selectOptions->charger(CategorieActivite::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'nature_taxe_id';
        $dirActuelle = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $filtre = BaremeTaxeFiltreForm::fromRequest($request);

        $baremes = $filtre->appliquer(
            BaremeTaxe::with(['natureTaxe', 'periodicite', 'categorieActivite'])
        )->orderBy($sortActuel, $dirActuelle)->orderBy('ca_borne_inf')->paginate(15)->withQueryString();

        return view('parametrage.baremes-taxe.index', compact(
            'baremes', 'filtre', 'naturesTaxe', 'periodicites', 'categories',
            'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        return view('parametrage.baremes-taxe.create', $this->optionsFormulaire());
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $this->valider($request);

        BaremeTaxe::create($donnees);

        return redirect()->route('parametrage.baremes-taxe.index')
            ->with('success', 'Barème créé avec succès.');
    }

    public function show(BaremeTaxe $baremeTaxe): View
    {
        $baremeTaxe->load(['natureTaxe', 'periodicite', 'categorieActivite']);

        return view('parametrage.baremes-taxe.show', compact('baremeTaxe'));
    }

    public function edit(BaremeTaxe $baremeTaxe): View
    {
        return view('parametrage.baremes-taxe.edit', array_merge(
            ['baremeTaxe' => $baremeTaxe],
            $this->optionsFormulaire(),
        ));
    }

    public function update(Request $request, BaremeTaxe $baremeTaxe): RedirectResponse
    {
        $baremeTaxe->update($this->valider($request));

        return redirect()->route('parametrage.baremes-taxe.show', $baremeTaxe)
            ->with('success', 'Barème mis à jour.');
    }

    public function destroy(BaremeTaxe $baremeTaxe): RedirectResponse
    {
        $baremeTaxe->delete();

        return redirect()->route('parametrage.baremes-taxe.index')
            ->with('success', 'Barème supprimé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = BaremeTaxeFiltreForm::fromRequest($request);

        return $excel->telecharger('baremes_taxe', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Nature taxe', 'Catégorie activité', 'Périodicité',
                'CA borne inf.', 'CA borne sup.', 'Taux (%)',
            ], $entete));

            $filtre->appliquer(
                BaremeTaxe::with(['natureTaxe', 'periodicite', 'categorieActivite'])
            )->orderBy('nature_taxe_id')->orderBy('ca_borne_inf')
             ->chunk(500, function ($liste) use ($writer): void {
                 foreach ($liste as $b) {
                     $writer->addRow(Row::fromValues([
                         $b->natureTaxe?->libelle_court ?? $b->natureTaxe?->libelle ?? '',
                         $b->categorieActivite?->libelle ?? 'Toutes',
                         $b->periodicite?->libelle ?? '',
                         (float) $b->ca_borne_inf,
                         (float) $b->ca_borne_sup === 0.0 ? 'ouverte' : (float) $b->ca_borne_sup,
                         (float) $b->taux,
                     ]));
                 }
             });
        });
    }

    /** Options communes aux formulaires create/edit. */
    private function optionsFormulaire(): array
    {
        return [
            'naturesTaxe'  => $this->selectOptions->charger(NatureTaxe::class, 'libelle_court'),
            'periodicites' => $this->selectOptions->charger(Periodicite::class, 'libelle'),
            'categories'   => $this->selectOptions->charger(CategorieActivite::class, 'libelle'),
        ];
    }

    /** Validation partagée store/update, dont la règle de bornes de CA. */
    private function valider(Request $request): array
    {
        return $request->validate([
            'nature_taxe_id'        => ['required', 'integer', 'exists:nature_taxe,id'],
            'categorie_activite_id' => ['nullable', 'integer', 'exists:categorie_activite,id'],
            'periodicite_id'        => ['required', 'integer', 'exists:periodicite,id'],
            'ca_borne_inf'          => ['required', 'numeric', 'min:0'],
            // borne_sup = 0 ⇒ tranche ouverte ; sinon doit être ≥ borne_inf (aligné sur le CHECK SQL)
            'ca_borne_sup'          => ['required', 'numeric', 'min:0', function ($attribut, $valeur, $echec) use ($request) {
                if ((float) $valeur !== 0.0 && (float) $valeur < (float) $request->input('ca_borne_inf')) {
                    $echec('La borne supérieure doit être 0 (tranche ouverte) ou supérieure ou égale à la borne inférieure.');
                }
            }],
            'taux'                  => ['required', 'numeric', 'min:0', 'max:100'],
        ], [], [
            'nature_taxe_id'        => 'nature de taxe',
            'categorie_activite_id' => 'catégorie d\'activité',
            'periodicite_id'        => 'périodicité',
            'ca_borne_inf'          => 'borne inférieure de CA',
            'ca_borne_sup'          => 'borne supérieure de CA',
        ]);
    }
}
