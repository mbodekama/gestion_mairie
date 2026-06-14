<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\AgentFiltreForm;
use App\Models\Agent;
use App\Models\FonctionAgent;
use App\Models\GradeAgent;
use App\Models\Service;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AgentController extends Controller
{
    private const COLONNES_TRI = [
        'matricule', 'nom', 'prenoms', 'service_id',
        'fonction_agent_id', 'grade_agent_id', 'actif', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $services   = $this->selectOptions->charger(Service::class, 'libelle');
        $fonctions  = $this->selectOptions->charger(FonctionAgent::class, 'libelle');
        $grades     = $this->selectOptions->charger(GradeAgent::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'nom';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = AgentFiltreForm::fromRequest($request);

        $agents = $filtre->appliquer(
            Agent::with(['fonctionAgent', 'gradeAgent', 'service', 'utilisateurs'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('administration.agents.index', compact(
            'agents', 'filtre', 'services', 'fonctions', 'grades', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = AgentFiltreForm::fromRequest($request);

        return $excel->telecharger('agents', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Matricule', 'Nom', 'Prénoms', 'Fonction', 'Grade', 'Service', 'Actif',
            ], $entete));

            $filtre->appliquer(Agent::with(['fonctionAgent', 'gradeAgent', 'service']))
                ->orderBy('nom', 'asc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $a) {
                        $writer->addRow(Row::fromValues([
                            $a->matricule ?? '',
                            $a->nom ?? '',
                            $a->prenoms ?? '',
                            $a->fonctionAgent?->libelle ?? '',
                            $a->gradeAgent?->libelle ?? '',
                            $a->service?->libelle ?? '',
                            $a->actif ? 'Oui' : 'Non',
                        ]));
                    }
                });
        });
    }
}
