<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ConvocationFiltreForm;
use App\Http\Requests\ConvocationRequest;
use App\Models\Agent;
use App\Models\Collectivite;
use App\Models\Convocation;
use App\Models\Etablissement;
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
class ConvocationController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:CONVOC_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:CONVOC_CREER', only: ['create', 'store']),
            new Middleware('can:CONVOC_MODIFIER', only: ['edit', 'update', 'destroy']),
        ];
    }

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

    public function create(Request $request): View
    {
        $etablissement = $request->filled('etablissement_id')
            ? Etablissement::with('contribuable')->findOrFail($request->query('etablissement_id'))
            : null;

        return view('convocations.create', array_merge(
            ['etablissement' => $etablissement, 'convocation' => null],
            $this->referentiels($etablissement),
        ));
    }

    public function store(ConvocationRequest $request): RedirectResponse
    {
        $donnees = $request->validated();

        $donnees['numero']          = $this->numeroProchain((int) $donnees['annee']);
        $donnees['collectivite_id'] = Collectivite::value('id');
        $donnees['created_by']      = auth()->id();

        $convocation = Convocation::create($donnees);

        return redirect()->route('convocations.show', $convocation)
            ->with('success', 'Convocation ' . $convocation->numero . ' créée.');
    }

    public function show(Convocation $convocation): View
    {
        $convocation->load(['etablissement.contribuable', 'service', 'agent', 'controle']);

        try {
            $documents = $convocation->documents()->with('docType')->get();
        } catch (\Illuminate\Database\QueryException) {
            $documents = collect();
        }

        return view('convocations.show', compact('convocation', 'documents'));
    }

    public function edit(Convocation $convocation): View
    {
        $convocation->load('etablissement.contribuable');

        return view('convocations.edit', array_merge(
            ['etablissement' => $convocation->etablissement, 'convocation' => $convocation],
            $this->referentiels($convocation->etablissement),
        ));
    }

    public function update(ConvocationRequest $request, Convocation $convocation): RedirectResponse
    {
        $convocation->update($request->validated());

        return redirect()->route('convocations.show', $convocation)
            ->with('success', 'Convocation ' . $convocation->numero . ' mise à jour.');
    }

    public function destroy(Convocation $convocation): RedirectResponse
    {
        // Une convocation issue d'un contrôle est pilotée par le workflow : non supprimable ici.
        if ($convocation->controle_id) {
            return back()->with('error', 'Cette convocation est rattachée à un contrôle et ne peut être supprimée ici.');
        }

        $numero = $convocation->numero;
        $convocation->delete();

        return redirect()->route('convocations.index')
            ->with('success', 'Convocation ' . $numero . ' supprimée.');
    }

    /**
     * Numéro de convocation séquentiel (varchar(10)) : CV + année sur 2 chiffres
     * + 6 chiffres de séquence. Partagé avec les convocations issues du workflow.
     */
    private function numeroProchain(int $annee): string
    {
        $prefixe = 'CV' . substr((string) $annee, -2);
        $dernier = Convocation::where('numero', 'like', "{$prefixe}%")
            ->orderBy('numero', 'desc')->value('numero');
        $seq = $dernier ? ((int) substr($dernier, -6) + 1) : 1;

        return $prefixe . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Référentiels communs aux formulaires (services, agents, et la liste des
     * établissements quand aucun n'est pré-sélectionné).
     *
     * @return array<string, mixed>
     */
    private function referentiels(?Etablissement $etablissement): array
    {
        return [
            'services'       => $this->selectOptions->charger(Service::class, 'libelle'),
            'agents'         => $this->selectOptions->charger(Agent::class, 'nom'),
            'etablissements' => $etablissement
                ? collect()
                : Etablissement::with('contribuable')->whereNull('supprime_le')->orderBy('numero')->get(),
        ];
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
