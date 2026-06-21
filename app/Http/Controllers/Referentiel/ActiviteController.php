<?php

namespace App\Http\Controllers\Referentiel;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ActiviteFiltreForm;
use App\Models\Activite;
use App\Models\CategorieActivite;
use App\Models\SecteurActivite;
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
class ActiviteController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:ACTIVITE_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:ACTIVITE_GERER', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    private const COLONNES_TRI = ['code', 'libelle', 'secteur_activite_id', 'categorie_activite_id'];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $secteurs    = $this->selectOptions->charger(SecteurActivite::class, 'libelle');
        $categories  = $this->selectOptions->charger(CategorieActivite::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'libelle';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ActiviteFiltreForm::fromRequest($request);

        $activites = $filtre->appliquer(
            Activite::with(['secteurActivite', 'categorieActivite'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('referentiel.activites.index', compact(
            'activites', 'filtre', 'secteurs', 'categories', 'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        $secteurs   = $this->selectOptions->charger(SecteurActivite::class, 'libelle');
        $categories = $this->selectOptions->charger(CategorieActivite::class, 'libelle');

        return view('referentiel.activites.create', compact('secteurs', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'code'                 => ['required', 'string', 'max:5', 'unique:activite,code'],
            'libelle'              => ['required', 'string', 'max:1000'],
            'secteur_activite_id'  => ['required', 'integer', 'exists:secteur_activite,id'],
            'categorie_activite_id'=> ['required', 'integer', 'exists:categorie_activite,id'],
        ]);

        $activite = Activite::create($donnees);

        return redirect()->route('referentiel.activites.show', $activite)
            ->with('success', 'Activité créée avec succès.');
    }

    public function show(Activite $activite): View
    {
        $activite->load(['secteurActivite', 'categorieActivite']);

        $nbEtablissements = $activite->etablissements()->whereNull('supprime_le')->count();

        return view('referentiel.activites.show', compact('activite', 'nbEtablissements'));
    }

    public function edit(Activite $activite): View
    {
        $secteurs   = $this->selectOptions->charger(SecteurActivite::class, 'libelle');
        $categories = $this->selectOptions->charger(CategorieActivite::class, 'libelle');

        return view('referentiel.activites.edit', compact('activite', 'secteurs', 'categories'));
    }

    public function update(Request $request, Activite $activite): RedirectResponse
    {
        $donnees = $request->validate([
            'code'                 => ['required', 'string', 'max:5', 'unique:activite,code,' . $activite->id],
            'libelle'              => ['required', 'string', 'max:1000'],
            'secteur_activite_id'  => ['required', 'integer', 'exists:secteur_activite,id'],
            'categorie_activite_id'=> ['required', 'integer', 'exists:categorie_activite,id'],
        ]);

        $activite->update($donnees);

        return redirect()->route('referentiel.activites.show', $activite)
            ->with('success', 'Activité mise à jour.');
    }

    public function destroy(Activite $activite): RedirectResponse
    {
        if ($activite->etablissements()->whereNull('supprime_le')->exists()) {
            return back()->with('error', 'Impossible de supprimer une activité utilisée par des établissements actifs.');
        }

        $activite->delete();

        return redirect()->route('referentiel.activites.index')
            ->with('success', 'Activité supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ActiviteFiltreForm::fromRequest($request);

        return $excel->telecharger('activites', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues(['Code', 'Libellé', 'Secteur d\'activité', 'Catégorie'], $entete));

            $filtre->appliquer(Activite::with(['secteurActivite', 'categorieActivite']))
                ->orderBy('libelle')->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $a) {
                        $writer->addRow(Row::fromValues([
                            $a->code ?? '',
                            $a->libelle ?? '',
                            $a->secteurActivite?->libelle ?? '',
                            $a->categorieActivite?->libelle ?? '',
                        ]));
                    }
                });
        });
    }
}
