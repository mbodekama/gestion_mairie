<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ConvocationFiltreForm;
use App\Models\Convocation;
use App\Models\Service;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConvocationController extends Controller
{
    private const COLONNES_TRI = [
        'numero', 'etablissement_id', 'service_id',
        'annee', 'date_convocation', 'date_limite', 'date_reponse', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $services = $this->selectOptions->charger(Service::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'date_convocation';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ConvocationFiltreForm::fromRequest($request);

        $convocations = $filtre->appliquer(
            Convocation::with(['etablissement.contribuable', 'service', 'agent'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('convocations.index', compact(
            'convocations', 'filtre', 'services', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ConvocationFiltreForm::fromRequest($request);

        return $excel->telecharger('convocations', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Convocation', 'Établissement', 'Contribuable', 'N° Identifiant',
                'Service', 'Année', 'Motif', 'Date convocation', 'Délai réponse',
                'Date limite', 'Date réponse',
            ], $entete));

            $filtre->appliquer(
                Convocation::with(['etablissement.contribuable', 'service'])
            )->orderBy('date_convocation', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $c) {
                    $contrib = $c->etablissement?->contribuable;
                    $nom = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $c->numero ?? '',
                        $c->etablissement?->denomination ?? $c->etablissement?->numero ?? '',
                        $nom,
                        $contrib?->numero_identifiant ?? '',
                        $c->service?->libelle ?? '',
                        $c->annee ?? '',
                        $c->motif ?? '',
                        $c->date_convocation?->format('d/m/Y') ?? '',
                        $c->delai_reponse ?? '',
                        $c->date_limite?->format('d/m/Y') ?? '',
                        $c->date_reponse?->format('d/m/Y') ?? '',
                    ]));
                }
            });
        });
    }
}
