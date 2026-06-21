<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\ExerciceFiscalFiltreForm;
use App\Models\Collectivite;
use App\Models\ExerciceFiscal;
use App\Services\ExcelExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class ExerciceFiscalController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:EXERCICE_CONSULTER', only: ['index', 'show', 'export']),
            new Middleware('can:EXERCICE_OUVRIR', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('can:EXERCICE_CLOTURER', only: ['cloturer']),
        ];
    }

    private const COLONNES_TRI = ['annee', 'collectivite_id', 'date_debut', 'date_fin', 'cloture'];

    public function index(Request $request)
    {
        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'annee';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = ExerciceFiscalFiltreForm::fromRequest($request);

        $exercices = $filtre->appliquer(
            ExerciceFiscal::with('collectivite')
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('exercices-fiscaux.index', compact('exercices', 'filtre', 'sortActuel', 'dirActuelle'));
    }

    public function create(): View
    {
        return view('exercices-fiscaux.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $collectivite = Collectivite::first();

        $donnees = $request->validate([
            'annee'      => [
                'required', 'integer', 'min:2000', 'max:2100',
                // Année unique au sein de la collectivité (aligné sur UNIQUE(annee, collectivite_id))
                Rule::unique('exercice_fiscal', 'annee')->where('collectivite_id', $collectivite?->id),
            ],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
        ], [
            'annee.unique' => 'Un exercice fiscal existe déjà pour cette année dans cette collectivité.',
        ]);

        // Règle métier : un seul exercice ouvert (non clôturé) par collectivité
        $exerciceOuvert = ExerciceFiscal::where('collectivite_id', $collectivite?->id)
            ->where('cloture', false)
            ->exists();

        if ($exerciceOuvert) {
            return back()->withInput()->withErrors([
                'annee' => 'Un exercice fiscal est déjà ouvert pour cette collectivité. Veuillez le clôturer avant d\'en ouvrir un nouveau.',
            ]);
        }

        $donnees['collectivite_id'] = $collectivite?->id;
        $donnees['cloture']         = false;

        $exercice = ExerciceFiscal::create($donnees);

        return redirect()->route('exercices-fiscaux.show', $exercice)
            ->with('success', 'Exercice fiscal créé avec succès.');
    }

    public function show(ExerciceFiscal $exerciceFiscal): View
    {
        $exerciceFiscal->load('collectivite');

        $nbEmissions = $exerciceFiscal->emissionsTaxe()->count();

        $totalEmis = $exerciceFiscal->emissionsTaxe()
            ->get()
            ->reduce(function ($carry, $e) {
                $base = $e->montant_prorata > 0 ? (string) $e->montant_prorata : (string) $e->montant_annuel;
                return bcadd($carry, $base, 2);
            }, '0');

        $totalRegle = (string) $exerciceFiscal->reglementsTaxe()->sum('montant_impute');

        $emissions = $exerciceFiscal->emissionsTaxe()
            ->with(['etablissement.contribuable', 'natureTaxe', 'periodicite'])
            ->orderBy('numero_emission')
            ->paginate(20);

        return view('exercices-fiscaux.show', compact(
            'exerciceFiscal', 'nbEmissions', 'totalEmis', 'totalRegle', 'emissions',
        ));
    }

    public function edit(ExerciceFiscal $exerciceFiscal): View
    {
        return view('exercices-fiscaux.edit', compact('exerciceFiscal'));
    }

    public function update(Request $request, ExerciceFiscal $exerciceFiscal): RedirectResponse
    {
        if ($exerciceFiscal->cloture) {
            return back()->with('error', 'Un exercice clôturé ne peut pas être modifié.');
        }

        if ($exerciceFiscal->aDesOperations()) {
            return back()->with('error', 'Cet exercice comporte des émissions ou des recouvrements : il ne peut plus être modifié.');
        }

        $donnees = $request->validate([
            'annee'      => [
                'required', 'integer', 'min:2000', 'max:2100',
                Rule::unique('exercice_fiscal', 'annee')
                    ->where('collectivite_id', $exerciceFiscal->collectivite_id)
                    ->ignore($exerciceFiscal->id),
            ],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
        ], [
            'annee.unique' => 'Un exercice fiscal existe déjà pour cette année dans cette collectivité.',
        ]);

        $exerciceFiscal->update($donnees);

        return redirect()->route('exercices-fiscaux.show', $exerciceFiscal)
            ->with('success', 'Exercice fiscal mis à jour.');
    }

    public function destroy(ExerciceFiscal $exerciceFiscal): RedirectResponse
    {
        if ($exerciceFiscal->cloture) {
            return back()->with('error', 'Impossible de supprimer un exercice clôturé.');
        }

        if ($exerciceFiscal->aDesOperations()) {
            return back()->with('error', 'Impossible de supprimer un exercice comportant des émissions ou des recouvrements.');
        }

        $exerciceFiscal->delete();

        return redirect()->route('exercices-fiscaux.index')
            ->with('success', 'Exercice fiscal supprimé.');
    }

    public function cloturer(ExerciceFiscal $exerciceFiscal): RedirectResponse
    {
        if ($exerciceFiscal->cloture) {
            return back()->with('error', 'Exercice déjà clôturé.');
        }

        $exerciceFiscal->update(['cloture' => true]);

        return redirect()->route('exercices-fiscaux.show', $exerciceFiscal)
            ->with('success', 'Exercice fiscal clôturé.');
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ExerciceFiscalFiltreForm::fromRequest($request);

        return $excel->telecharger('exercices_fiscaux', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Année', 'Collectivité', 'Date début', 'Date fin', 'État',
            ], $entete));

            $filtre->appliquer(
                ExerciceFiscal::with('collectivite')
            )->orderBy('annee', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $e) {
                    $writer->addRow(Row::fromValues([
                        $e->annee ?? '',
                        $e->collectivite?->libelle ?? '',
                        $e->date_debut?->format('d/m/Y') ?? '',
                        $e->date_fin?->format('d/m/Y') ?? '',
                        $e->cloture ? 'Clôturé' : 'Ouvert',
                    ]));
                }
            });
        });
    }
}
