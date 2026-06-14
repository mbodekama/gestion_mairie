<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ExonerationFiltreForm;
use App\Models\Exoneration;
use App\Models\TypeExoneration;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExonerationController extends Controller
{
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
