<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ObligationFiltreForm;
use App\Models\NatureTaxe;
use App\Models\Obligation;
use App\Models\Periodicite;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\Request;
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
