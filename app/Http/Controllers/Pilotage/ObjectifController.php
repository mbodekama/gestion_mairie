<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ObjectifFiltreForm;
use App\Models\Objectif;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObjectifController extends Controller
{
    private const COLONNES_TRI = ['annee', 'montant', 'montant_revise', 'created_at'];

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'annee';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ObjectifFiltreForm::fromRequest($request);

        $objectifs = $filtre->appliquer(
            Objectif::with('collectivite')
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('pilotage.objectifs.index', compact(
            'objectifs', 'filtre', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ObjectifFiltreForm::fromRequest($request);

        return $excel->telecharger('objectifs_recouvrement', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Année', 'Collectivité', 'Montant objectif (FCFA)', 'Montant révisé (FCFA)',
            ], $entete));

            $filtre->appliquer(Objectif::with('collectivite'))
                ->orderBy('annee', 'desc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $o) {
                        $writer->addRow(Row::fromValues([
                            $o->annee,
                            $o->collectivite?->libelle ?? '',
                            $o->montant !== null ? number_format((float) $o->montant, 2, '.', '') : '',
                            $o->montant_revise !== null ? number_format((float) $o->montant_revise, 2, '.', '') : '',
                        ]));
                    }
                });
        });
    }
}
