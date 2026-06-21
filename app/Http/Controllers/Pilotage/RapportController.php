<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Models\Collectivite;
use App\Models\EmissionTaxe;
use App\Models\ExerciceFiscal;
use App\Pdf\EtatExonerations;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class RapportController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:PILOTAGE_CONSULTER', only: ['index', 'exonerations']),
        ];
    }

    public function index(): View
    {
        $exercices = ExerciceFiscal::orderBy('annee', 'desc')->get();

        return view('pilotage.rapports.index', compact('exercices'));
    }

    /**
     * État de restitution des exonérations : montants exonérés par exercice et
     * nature de taxe, avec le détail des émissions concernées (PDF).
     */
    public function exonerations(Request $request): Response
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

        $suffixe = $exercice ? '-'.$exercice->annee : '';

        return (new EtatExonerations($parExercice, $totalGeneral, $exercice, $collectivite))
            ->reponse('etat-restitution-exonerations'.$suffixe.'.pdf');
    }
}
