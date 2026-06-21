<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\JournalConnexionFiltreForm;
use App\Models\JournalConnexion;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class JournalController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:AUDIT_CONSULTER', only: ['index', 'export']),
        ];
    }

    private const COLONNES_TRI = [
        'login', 'succes', 'adresse_ip', 'application', 'horodatage',
    ];

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'horodatage';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = JournalConnexionFiltreForm::fromRequest($request);

        $journaux = $filtre->appliquer(
            JournalConnexion::with('utilisateur')
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('administration.journal.index', compact(
            'journaux', 'filtre', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = JournalConnexionFiltreForm::fromRequest($request);

        return $excel->telecharger('journal_connexions', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Horodatage', 'Login', 'Application', 'Résultat', 'Adresse IP', 'User-Agent',
            ], $entete));

            $filtre->appliquer(JournalConnexion::with('utilisateur'))
                ->orderBy('horodatage', 'desc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $j) {
                        $writer->addRow(Row::fromValues([
                            $j->horodatage?->format('d/m/Y H:i:s') ?? '',
                            $j->login ?? '',
                            $j->application ?? '',
                            $j->succes ? 'Succès' : 'Échec',
                            $j->adresse_ip ?? '',
                            $j->user_agent ?? '',
                        ]));
                    }
                });
        });
    }
}
