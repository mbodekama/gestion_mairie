<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ParametreFiltreForm;
use App\Models\ParametreApplication;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class ParametreController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:PARAMETRE_CONSULTER', only: ['index', 'export']),
        ];
    }

    private const COLONNES_TRI = ['cle', 'valeur'];

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'cle';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ParametreFiltreForm::fromRequest($request);

        $parametres = $filtre->appliquer(
            ParametreApplication::with('collectivite')
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('administration.parametres.index', compact(
            'parametres', 'filtre', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ParametreFiltreForm::fromRequest($request);

        return $excel->telecharger('parametres_application', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Clé', 'Valeur', 'Description', 'Collectivité',
            ], $entete));

            $filtre->appliquer(ParametreApplication::with('collectivite'))
                ->orderBy('cle', 'asc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $p) {
                        $writer->addRow(Row::fromValues([
                            $p->cle ?? '',
                            $p->valeur ?? '',
                            $p->description ?? '',
                            $p->collectivite?->libelle ?? 'Global',
                        ]));
                    }
                });
        });
    }
}
