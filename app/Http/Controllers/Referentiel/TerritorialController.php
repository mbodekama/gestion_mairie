<?php

namespace App\Http\Controllers\Referentiel;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\CommuneFiltreForm;
use App\Models\Commune;
use App\Models\SousPrefecture;
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
class TerritorialController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:TERRITOIRE_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:TERRITOIRE_GERER', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    private const COLONNES_TRI = ['code', 'libelle', 'sous_prefecture_id'];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'libelle';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = CommuneFiltreForm::fromRequest($request);

        $communes = $filtre->appliquer(
            Commune::with('sousPrefecture')
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('referentiel.territorial.index', compact('communes', 'filtre', 'sortActuel', 'dirActuelle'));
    }

    public function create(): View
    {
        $sousPrefectures = $this->selectOptions->charger(SousPrefecture::class, 'libelle');

        return view('referentiel.territorial.create', compact('sousPrefectures'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'code'               => ['required', 'string', 'max:3', 'unique:commune,code'],
            'libelle'            => ['required', 'string', 'max:255'],
            'sous_prefecture_id' => ['nullable', 'integer', 'exists:sous_prefecture,id'],
            'population'         => ['nullable', 'integer', 'min:0'],
        ]);

        $commune = Commune::create($donnees);

        return redirect()->route('referentiel.territorial.show', $commune)
            ->with('success', 'Commune créée avec succès.');
    }

    public function show(Commune $commune): View
    {
        $commune->load(['sousPrefecture.departement']);

        $nbEtablissements = $commune->etablissements()->whereNull('supprime_le')->count();
        $nbQuartiers      = $commune->quartiers()->count();
        $nbZonesFiscales  = $commune->zonesFiscales()->count();

        return view('referentiel.territorial.show', compact(
            'commune', 'nbEtablissements', 'nbQuartiers', 'nbZonesFiscales',
        ));
    }

    public function edit(Commune $commune): View
    {
        $sousPrefectures = $this->selectOptions->charger(SousPrefecture::class, 'libelle');

        return view('referentiel.territorial.edit', compact('commune', 'sousPrefectures'));
    }

    public function update(Request $request, Commune $commune): RedirectResponse
    {
        $donnees = $request->validate([
            'code'               => ['required', 'string', 'max:3', 'unique:commune,code,' . $commune->id],
            'libelle'            => ['required', 'string', 'max:255'],
            'sous_prefecture_id' => ['nullable', 'integer', 'exists:sous_prefecture,id'],
            'population'         => ['nullable', 'integer', 'min:0'],
        ]);

        $commune->update($donnees);

        return redirect()->route('referentiel.territorial.show', $commune)
            ->with('success', 'Commune mise à jour.');
    }

    public function destroy(Commune $commune): RedirectResponse
    {
        if ($commune->etablissements()->whereNull('supprime_le')->exists()) {
            return back()->with('error', 'Impossible de supprimer une commune avec des établissements actifs.');
        }

        $commune->delete();

        return redirect()->route('referentiel.territorial.index')
            ->with('success', 'Commune supprimée.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = CommuneFiltreForm::fromRequest($request);

        return $excel->telecharger('communes', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues(['Code', 'Libellé', 'Sous-préfecture', 'Population'], $entete));

            $filtre->appliquer(Commune::with('sousPrefecture'))
                ->orderBy('libelle')->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $c) {
                        $writer->addRow(Row::fromValues([
                            $c->code ?? '',
                            $c->libelle ?? '',
                            $c->sousPrefecture?->libelle ?? '',
                            $c->population ?? 0,
                        ]));
                    }
                });
        });
    }
}
