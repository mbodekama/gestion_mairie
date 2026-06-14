<?php

namespace App\Http\Controllers\Referentiel;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\NatureTaxeFiltreForm;
use App\Models\CategorieImpotTaxe;
use App\Models\DomaineTaxe;
use App\Models\NatureTaxe;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ParametrageController extends Controller
{
    private const COLONNES_TRI = ['code', 'libelle', 'libelle_court', 'domaine_taxe_id', 'categorie_impot_taxe_id'];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $domaines    = $this->selectOptions->charger(DomaineTaxe::class, 'libelle');
        $categories  = $this->selectOptions->charger(CategorieImpotTaxe::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'code';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = NatureTaxeFiltreForm::fromRequest($request);

        $naturesTaxe = $filtre->appliquer(
            NatureTaxe::with(['domaineTaxe', 'categorieImpotTaxe'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('referentiel.parametrage.index', compact(
            'naturesTaxe', 'filtre', 'domaines', 'categories', 'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        $domaines   = $this->selectOptions->charger(DomaineTaxe::class, 'libelle');
        $categories = $this->selectOptions->charger(CategorieImpotTaxe::class, 'libelle');

        return view('referentiel.parametrage.create', compact('domaines', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'code'                    => ['required', 'string', 'max:3', 'unique:nature_taxe,code'],
            'libelle_court'           => ['nullable', 'string', 'max:16'],
            'libelle'                 => ['nullable', 'string', 'max:255'],
            'domaine_taxe_id'         => ['required', 'integer', 'exists:domaine_taxe,id'],
            'categorie_impot_taxe_id' => ['required', 'integer', 'exists:categorie_impot_taxe,id'],
        ]);

        $natureTaxe = NatureTaxe::create($donnees);

        return redirect()->route('referentiel.parametrage.show', $natureTaxe)
            ->with('success', 'Nature de taxe créée avec succès.');
    }

    public function show(NatureTaxe $natureTaxe): View
    {
        $natureTaxe->load(['domaineTaxe', 'categorieImpotTaxe', 'baremesTaxe.periodicite']);

        $nbEmissions = $natureTaxe->emissionsTaxe()->count();

        return view('referentiel.parametrage.show', compact('natureTaxe', 'nbEmissions'));
    }

    public function edit(NatureTaxe $natureTaxe): View
    {
        $domaines   = $this->selectOptions->charger(DomaineTaxe::class, 'libelle');
        $categories = $this->selectOptions->charger(CategorieImpotTaxe::class, 'libelle');

        return view('referentiel.parametrage.edit', compact('natureTaxe', 'domaines', 'categories'));
    }

    public function update(Request $request, NatureTaxe $natureTaxe): RedirectResponse
    {
        $donnees = $request->validate([
            'code'                    => ['required', 'string', 'max:3', 'unique:nature_taxe,code,' . $natureTaxe->id],
            'libelle_court'           => ['nullable', 'string', 'max:16'],
            'libelle'                 => ['nullable', 'string', 'max:255'],
            'domaine_taxe_id'         => ['required', 'integer', 'exists:domaine_taxe,id'],
            'categorie_impot_taxe_id' => ['required', 'integer', 'exists:categorie_impot_taxe,id'],
        ]);

        $natureTaxe->update($donnees);

        return redirect()->route('referentiel.parametrage.show', $natureTaxe)
            ->with('success', 'Nature de taxe mise à jour.');
    }

    public function destroy(NatureTaxe $natureTaxe): RedirectResponse
    {
        if ($natureTaxe->emissionsTaxe()->exists()) {
            return back()->with('error', 'Impossible de supprimer une nature de taxe utilisée dans des émissions.');
        }

        $natureTaxe->delete();

        return redirect()->route('referentiel.parametrage.index')
            ->with('success', 'Nature de taxe supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = NatureTaxeFiltreForm::fromRequest($request);

        return $excel->telecharger('natures_taxe', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues(['Code', 'Abrégé', 'Libellé', 'Domaine', 'Catégorie'], $entete));

            $filtre->appliquer(NatureTaxe::with(['domaineTaxe', 'categorieImpotTaxe']))
                ->orderBy('code')->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $n) {
                        $writer->addRow(Row::fromValues([
                            $n->code ?? '',
                            $n->libelle_court ?? '',
                            $n->libelle ?? '',
                            $n->domaineTaxe?->libelle ?? '',
                            $n->categorieImpotTaxe?->libelle ?? '',
                        ]));
                    }
                });
        });
    }
}
