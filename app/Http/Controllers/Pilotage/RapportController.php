<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Models\Collectivite;
use App\Models\EmissionTaxe;
use App\Models\ExerciceFiscal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RapportController extends Controller
{
    public function index(): View
    {
        $exercices = ExerciceFiscal::orderBy('annee', 'desc')->get();

        return view('pilotage.rapports.index', compact('exercices'));
    }

    /**
     * État de restitution des exonérations : montants exonérés par exercice et
     * nature de taxe, avec le détail des émissions concernées (PDF).
     */
    public function exonerations(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'exercice_fiscal_id' => ['nullable', 'integer', 'exists:exercice_fiscal,id'],
        ]);

        $exercice = $request->filled('exercice_fiscal_id')
            ? ExerciceFiscal::find($request->input('exercice_fiscal_id'))
            : null;

        $emissions = EmissionTaxe::query()
            ->whereNotNull('exoneration_id')
            ->where('montant_exonere', '>', 0)
            ->when($exercice, fn ($q) => $q->where('exercice_fiscal_id', $exercice->id))
            ->with(['natureTaxe', 'exerciceFiscal', 'etablissement.contribuable', 'exoneration'])
            ->orderBy('exercice_fiscal_id')
            ->orderBy('nature_taxe_id')
            ->get();

        // Regroupement : exercice → nature de taxe
        $parExercice = $emissions->groupBy(fn ($e) => $e->exerciceFiscal?->annee ?? '—');

        $totalGeneral = $emissions->reduce(fn ($c, $e) => bcadd($c, (string) $e->montant_exonere, 2), '0');

        $collectivite = Collectivite::first();

        $pdf = Pdf::loadView('pilotage.rapports.exonerations-pdf', compact(
            'parExercice', 'totalGeneral', 'exercice', 'collectivite'
        ))->setPaper('a4');

        $suffixe = $exercice ? '-' . $exercice->annee : '';

        return $pdf->download('etat-restitution-exonerations' . $suffixe . '.pdf');
    }
}
