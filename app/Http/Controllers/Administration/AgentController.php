<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\AgentFiltreForm;
use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\Collectivite;
use App\Models\FonctionAgent;
use App\Models\GradeAgent;
use App\Models\Service;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
            Agent::with(['fonctionAgent', 'gradeAgent', 'service'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('administration.agents.index', compact(
            'agents', 'filtre', 'services', 'fonctions', 'grades', 'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        return view('administration.agents.create', $this->referentiels());
    }

    public function store(AgentRequest $request): RedirectResponse
    {
        $donnees = $request->validated();
        $donnees['collectivite_id'] = Collectivite::value('id');

        $agent = Agent::create($donnees);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'Agent ' . $agent->matricule . ' créé avec succès.');
    }

    public function show(Agent $agent): View
    {
        $agent->load(['fonctionAgent', 'gradeAgent', 'service', 'superieur', 'subordonnes']);

        return view('administration.agents.show', compact('agent'));
    }

    public function edit(Agent $agent): View
    {
        return view('administration.agents.edit', array_merge(
            ['agent' => $agent],
            $this->referentiels($agent),
        ));
    }

    public function update(AgentRequest $request, Agent $agent): RedirectResponse
    {
        $agent->update($request->validated());

        return redirect()->route('agents.show', $agent)
            ->with('success', 'Agent ' . $agent->matricule . ' mis à jour.');
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $matricule = $agent->matricule;
        $agent->delete();

        return redirect()->route('agents.index')
            ->with('success', 'Agent ' . $matricule . ' supprimé.');
    }

    /**
     * Référentiels communs aux formulaires de création/édition.
     * Exclut l'agent courant de la liste des supérieurs possibles.
     *
     * @return array<string, mixed>
     */
    private function referentiels(?Agent $agent = null): array
    {
        return [
            'fonctions'  => $this->selectOptions->charger(FonctionAgent::class, 'libelle'),
            'grades'     => $this->selectOptions->charger(GradeAgent::class, 'libelle'),
            'services'   => $this->selectOptions->charger(Service::class, 'libelle'),
            'superieurs' => Agent::when($agent, fn ($q) => $q->whereKeyNot($agent->id))
                ->orderBy('nom')->get(['id', 'matricule', 'nom', 'prenoms']),
        ];
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
