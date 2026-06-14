<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ControleFiscalFiltreForm;
use App\Models\Agent;
use App\Models\Collectivite;
use App\Models\ControleFiscal;
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

class ControleFiscalController extends Controller
{
    private const COLONNES_TRI = [
        'numero', 'etablissement_id', 'annee', 'montant_du',
        'date_convocation', 'date_limite', 'date_reponse', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'date_convocation';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ControleFiscalFiltreForm::fromRequest($request);

        $convocations = $filtre->appliquer(
            ControleFiscal::with(['etablissement.contribuable', 'service', 'agent'])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('controle-fiscal.index', compact('convocations', 'filtre', 'sortActuel', 'dirActuelle'));
    }

    public function create(Request $request): View
    {
        $etablissement = $request->query('etablissement_id')
            ? Etablissement::with('contribuable')->findOrFail($request->query('etablissement_id'))
            : null;

        $services = $this->selectOptions->charger(Service::class, 'libelle');
        $agents   = $this->selectOptions->charger(Agent::class, 'nom');

        return view('controle-fiscal.create', compact('etablissement', 'services', 'agents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'etablissement_id'  => ['required', 'integer', 'exists:etablissement,id'],
            'annee'             => ['required', 'integer', 'min:2000', 'max:2100'],
            'motif'             => ['nullable', 'string', 'max:512'],
            'service_id'        => ['required', 'integer', 'exists:service,id'],
            'agent_id'          => ['required', 'integer', 'exists:agent,id'],
            'date_convocation'  => ['nullable', 'date'],
            'delai_reponse'     => ['nullable', 'integer', 'min:1'],
            'date_limite'       => ['nullable', 'date'],
            'periode_due_debut' => ['nullable', 'date'],
            'periode_due_fin'   => ['nullable', 'date'],
            'nb_mois_du'        => ['nullable', 'integer', 'min:0'],
            'nb_jours_du'       => ['nullable', 'integer', 'min:0'],
            'montant_du'        => ['nullable', 'numeric', 'min:0'],
        ]);

        $collectivite = Collectivite::first();
        $annee        = $donnees['annee'];

        $seq = ControleFiscal::where('numero', 'like', "CF{$annee}%")
            ->orderBy('numero', 'desc')
            ->value('numero');
        $seq = $seq ? ((int) substr($seq, -5) + 1) : 1;

        $donnees['numero']        = "CF{$annee}" . str_pad($seq, 5, '0', STR_PAD_LEFT);
        $donnees['collectivite_id'] = $collectivite?->id;
        $donnees['created_by']    = auth()->id();

        $controle = ControleFiscal::create($donnees);

        return redirect()->route('controle-fiscal.show', $controle)
            ->with('success', 'Contrôle fiscal créé — N° ' . $controle->numero);
    }

    public function show(ControleFiscal $controleFiscal): View
    {
        $controleFiscal->load([
            'etablissement.contribuable',
            'etablissement.commune',
            'service',
            'agent',
        ]);

        try {
            $documents = $controleFiscal->documents()->with('docType')->get();
        } catch (\Illuminate\Database\QueryException) {
            $documents = collect();
        }

        return view('controle-fiscal.show', compact('controleFiscal', 'documents'));
    }

    public function edit(ControleFiscal $controleFiscal): View
    {
        $controleFiscal->load(['etablissement.contribuable', 'service', 'agent']);

        $services = $this->selectOptions->charger(Service::class, 'libelle');
        $agents   = $this->selectOptions->charger(Agent::class, 'nom');

        return view('controle-fiscal.edit', compact('controleFiscal', 'services', 'agents'));
    }

    public function update(Request $request, ControleFiscal $controleFiscal): RedirectResponse
    {
        $donnees = $request->validate([
            'annee'             => ['required', 'integer', 'min:2000', 'max:2100'],
            'motif'             => ['nullable', 'string', 'max:512'],
            'service_id'        => ['required', 'integer', 'exists:service,id'],
            'agent_id'          => ['required', 'integer', 'exists:agent,id'],
            'date_convocation'  => ['nullable', 'date'],
            'delai_reponse'     => ['nullable', 'integer', 'min:1'],
            'date_limite'       => ['nullable', 'date'],
            'date_reponse'      => ['nullable', 'date'],
            'heure_reponse'     => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'periode_due_debut' => ['nullable', 'date'],
            'periode_due_fin'   => ['nullable', 'date'],
            'nb_mois_du'        => ['nullable', 'integer', 'min:0'],
            'nb_jours_du'       => ['nullable', 'integer', 'min:0'],
            'montant_du'        => ['nullable', 'numeric', 'min:0'],
        ]);

        $controleFiscal->update($donnees);

        return redirect()->route('controle-fiscal.show', $controleFiscal)
            ->with('success', 'Contrôle fiscal mis à jour.');
    }

    public function destroy(ControleFiscal $controleFiscal): RedirectResponse
    {
        $etablissementId = $controleFiscal->etablissement_id;
        $controleFiscal->delete();

        return redirect()->route('etablissements.show', $etablissementId)
            ->with('success', 'Contrôle fiscal supprimé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ControleFiscalFiltreForm::fromRequest($request);

        return $excel->telecharger('controles_fiscaux', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Convocation', 'Établissement', 'Contribuable', 'N° Identifiant',
                'Année', 'Motif', 'Montant dû', 'Période début', 'Période fin',
                'Date convocation', 'Date limite', 'Date réponse',
            ], $entete));

            $filtre->appliquer(
                ControleFiscal::with(['etablissement.contribuable', 'service'])
            )->orderBy('date_convocation', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $c) {
                    $contrib = $c->etablissement?->contribuable;
                    $nomContrib = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $c->numero ?? '',
                        $c->etablissement?->denomination ?? $c->etablissement?->numero ?? '',
                        $nomContrib,
                        $contrib?->numero_identifiant ?? '',
                        $c->annee ?? '',
                        $c->motif ?? '',
                        (float) $c->montant_du,
                        $c->periode_due_debut?->format('d/m/Y') ?? '',
                        $c->periode_due_fin?->format('d/m/Y') ?? '',
                        $c->date_convocation?->format('d/m/Y') ?? '',
                        $c->date_limite?->format('d/m/Y') ?? '',
                        $c->date_reponse?->format('d/m/Y') ?? '',
                    ]));
                }
            });
        });
    }
}
