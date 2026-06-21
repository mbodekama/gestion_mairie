<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ServiceFiltreForm;
use App\Http\Requests\ServiceRequest;
use App\Models\Collectivite;
use App\Models\DepartementService;
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

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class ServiceController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:SERVICE_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:SERVICE_GERER', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    private const COLONNES_TRI = ['code', 'libelle', 'sigle', 'departement_service_id'];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $departements = $this->selectOptions->charger(DepartementService::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'libelle';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ServiceFiltreForm::fromRequest($request);

        $services = $filtre->appliquer(
            Service::with(['departementService'])->withCount('agents')
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('administration.services.index', compact(
            'services', 'filtre', 'departements', 'sortActuel', 'dirActuelle',
        ));
    }

    public function create(): View
    {
        $departements = $this->selectOptions->charger(DepartementService::class, 'libelle');

        return view('administration.services.create', compact('departements'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $donnees = $request->validated();
        $donnees['collectivite_id'] = Collectivite::value('id');

        $service = Service::create($donnees);

        return redirect()->route('services.show', $service)
            ->with('success', 'Service ' . $service->code . ' créé avec succès.');
    }

    public function show(Service $service): View
    {
        $service->load(['departementService', 'agents.fonctionAgent']);

        return view('administration.services.show', compact('service'));
    }

    public function edit(Service $service): View
    {
        $departements = $this->selectOptions->charger(DepartementService::class, 'libelle');

        return view('administration.services.edit', compact('service', 'departements'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('services.show', $service)
            ->with('success', 'Service ' . $service->code . ' mis à jour.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        // Garde-fou : un service rattaché à des agents n'est pas supprimable.
        if ($service->agents()->exists()) {
            return back()->with('error', 'Impossible de supprimer un service auquel des agents sont rattachés.');
        }

        $code = $service->code;
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service ' . $code . ' supprimé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ServiceFiltreForm::fromRequest($request);

        return $excel->telecharger('services', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Code', 'Libellé', 'Sigle', 'Département', 'Nb agents',
            ], $entete));

            $filtre->appliquer(Service::with('departementService')->withCount('agents'))
                ->orderBy('libelle', 'asc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $s) {
                        $writer->addRow(Row::fromValues([
                            $s->code ?? '',
                            $s->libelle ?? '',
                            $s->sigle ?? '',
                            $s->departementService?->libelle ?? '',
                            $s->agents_count,
                        ]));
                    }
                });
        });
    }
}
