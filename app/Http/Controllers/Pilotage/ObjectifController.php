<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\FiltreDataForm\ObjectifFiltreForm;
use App\Http\Requests\ObjectifRequest;
use App\Models\Collectivite;
use App\Models\ExerciceFiscal;
use App\Models\Objectif;
use App\Services\ExcelExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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

    public function create(): View
    {
        return view('pilotage.objectifs.create', [
            'exercices' => $this->exercicesSelectionnables(),
        ]);
    }

    public function store(ObjectifRequest $request): RedirectResponse
    {
        $exercice = ExerciceFiscal::findOrFail($request->integer('exercice_fiscal_id'));

        $objectif = Objectif::create($this->donneesAvecExercice($request, $exercice) + [
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('pilotage.objectifs.index')
            ->with('success', "Objectif de recouvrement {$objectif->annee} créé.");
    }

    public function show(Objectif $objectif): View
    {
        $objectif->load('collectivite', 'exerciceFiscal');

        return view('pilotage.objectifs.show', compact('objectif'));
    }

    public function edit(Objectif $objectif): View
    {
        return view('pilotage.objectifs.edit', [
            'objectif'  => $objectif,
            'exercices' => $this->exercicesSelectionnables($objectif),
        ]);
    }

    public function update(ObjectifRequest $request, Objectif $objectif): RedirectResponse
    {
        $exercice = ExerciceFiscal::findOrFail($request->integer('exercice_fiscal_id'));

        $objectif->update($this->donneesAvecExercice($request, $exercice));

        return redirect()->route('pilotage.objectifs.index')
            ->with('success', "Objectif de recouvrement {$objectif->annee} mis à jour.");
    }

    /**
     * Données validées complétées par l'année et la collectivité, déduites de
     * l'exercice fiscal choisi (jamais saisies par l'utilisateur).
     */
    private function donneesAvecExercice(ObjectifRequest $request, ExerciceFiscal $exercice): array
    {
        return $request->validated() + [
            'annee'           => $exercice->annee,
            'collectivite_id' => $exercice->collectivite_id,
        ];
    }

    /**
     * Exercices ouverts de la collectivité, plus l'exercice déjà rattaché à
     * l'objectif en cours d'édition (même s'il est clôturé).
     */
    private function exercicesSelectionnables(?Objectif $objectif = null)
    {
        return ExerciceFiscal::where('collectivite_id', Collectivite::value('id'))
            ->where(function ($q) use ($objectif): void {
                $q->where('cloture', false);
                if ($objectif) {
                    $q->orWhere('id', $objectif->exercice_fiscal_id);
                }
            })
            ->orderBy('annee', 'desc')
            ->get();
    }

    public function destroy(Objectif $objectif): RedirectResponse
    {
        $annee = $objectif->annee;
        $objectif->delete();

        return redirect()->route('pilotage.objectifs.index')
            ->with('success', "Objectif de recouvrement {$annee} supprimé.");
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = ObjectifFiltreForm::fromRequest($request);

        return $excel->telecharger('objectifs_recouvrement', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'Année', 'Période début', 'Période fin', 'Collectivité',
                'Montant objectif (FCFA)', 'Montant révisé (FCFA)',
            ], $entete));

            $filtre->appliquer(Objectif::with('collectivite'))
                ->orderBy('annee', 'desc')
                ->chunk(500, function ($liste) use ($writer): void {
                    foreach ($liste as $o) {
                        $writer->addRow(Row::fromValues([
                            $o->annee,
                            $o->periode_debut?->format('d/m/Y') ?? '',
                            $o->periode_fin?->format('d/m/Y') ?? '',
                            $o->collectivite?->libelle ?? '',
                            $o->montant !== null ? number_format((float) $o->montant, 2, '.', '') : '',
                            $o->montant_revise !== null ? number_format((float) $o->montant_revise, 2, '.', '') : '',
                        ]));
                    }
                });
        });
    }
}
