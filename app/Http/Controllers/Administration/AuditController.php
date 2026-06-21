<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\AuditLogFiltreForm;
use App\Models\AuditLog;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class AuditController extends Controller implements HasMiddleware
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
        'table_cible', 'cle_ligne', 'action', 'utilisateur_id', 'horodatage',
    ];

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'horodatage';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = AuditLogFiltreForm::fromRequest($request);

        $audits = $filtre->appliquer(
            AuditLog::with('utilisateur')
        )->orderBy($sortActuel, $dirActuelle)->paginate(25)->withQueryString();

        return view('administration.audit.index', compact(
            'audits', 'filtre', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = AuditLogFiltreForm::fromRequest($request);

        return $excel->telecharger('audit_donnees', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Horodatage', 'Table', 'Clé', 'Action', 'Utilisateur',
            ], $entete));

            $filtre->appliquer(AuditLog::with('utilisateur'))
                ->orderBy('horodatage', 'desc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $a) {
                        $writer->addRow(Row::fromValues([
                            $a->horodatage?->format('d/m/Y H:i:s') ?? '',
                            $a->table_cible ?? '',
                            $a->cle_ligne ?? '',
                            $a->action ?? '',
                            $a->utilisateur?->email ?? '',
                        ]));
                    }
                });
        });
    }
}
