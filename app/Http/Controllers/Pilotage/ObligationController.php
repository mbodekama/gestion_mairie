<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ObligationFiltreForm;
use App\Models\Collectivite;
use App\Models\Contribuable;
use App\Models\NatureTaxe;
use App\Models\Obligation;
use App\Models\Periodicite;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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

    public function create(Request $request): View
    {
        $contribuable = $request->filled('contribuable_id')
            ? Contribuable::whereNull('supprime_le')->findOrFail($request->query('contribuable_id'))
            : null;

        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('pilotage.obligations.create', compact('contribuable', 'naturesTaxe', 'periodicites'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Résolution du matricule saisi → contribuable_id
        if ($request->filled('numero_identifiant') && ! $request->filled('contribuable_id')) {
            $contrib = Contribuable::where('numero_identifiant', $request->input('numero_identifiant'))
                ->whereNull('supprime_le')->first();

            if (! $contrib) {
                return back()->withInput()->withErrors([
                    'numero_identifiant' => 'Aucun contribuable trouvé avec le matricule « ' . $request->input('numero_identifiant') . ' ».',
                ]);
            }

            $request->merge(['contribuable_id' => $contrib->id]);
        }

        $donnees = $request->validate([
            'contribuable_id' => ['required', 'integer', 'exists:contribuable,id'],
            'nature_taxe_id'  => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id'  => ['nullable', 'integer', 'exists:periodicite,id'],
        ]);

        // Une obligation est unique par (contribuable, nature de taxe)
        $existe = Obligation::where('contribuable_id', $donnees['contribuable_id'])
            ->where('nature_taxe_id', $donnees['nature_taxe_id'])
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'nature_taxe_id' => 'Ce contribuable a déjà une obligation pour cette nature de taxe.',
            ]);
        }

        $donnees['collectivite_id'] = Collectivite::value('id');
        $donnees['created_by']      = auth()->id();

        Obligation::create($donnees);

        return redirect()->route('pilotage.obligations.index')
            ->with('success', 'Obligation fiscale créée avec succès.');
    }

    public function show(Obligation $obligation): View
    {
        $obligation->load(['contribuable', 'natureTaxe', 'periodicite']);

        return view('pilotage.obligations.show', compact('obligation'));
    }

    public function edit(Obligation $obligation): View
    {
        $obligation->load(['contribuable', 'natureTaxe', 'periodicite']);

        $naturesTaxe  = $this->selectOptions->charger(NatureTaxe::class, 'libelle');
        $periodicites = $this->selectOptions->charger(Periodicite::class, 'libelle');

        return view('pilotage.obligations.edit', compact('obligation', 'naturesTaxe', 'periodicites'));
    }

    public function update(Request $request, Obligation $obligation): RedirectResponse
    {
        $donnees = $request->validate([
            'nature_taxe_id' => ['required', 'integer', 'exists:nature_taxe,id'],
            'periodicite_id' => ['nullable', 'integer', 'exists:periodicite,id'],
        ]);

        // Unicité (contribuable, nature) hors l'obligation courante
        $existe = Obligation::where('contribuable_id', $obligation->contribuable_id)
            ->where('nature_taxe_id', $donnees['nature_taxe_id'])
            ->where('id', '!=', $obligation->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'nature_taxe_id' => 'Ce contribuable a déjà une obligation pour cette nature de taxe.',
            ]);
        }

        $obligation->update($donnees);

        return redirect()->route('pilotage.obligations.index')
            ->with('success', 'Obligation fiscale mise à jour.');
    }

    public function destroy(Obligation $obligation): RedirectResponse
    {
        $obligation->delete();

        return redirect()->route('pilotage.obligations.index')
            ->with('success', 'Obligation fiscale supprimée.');
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
