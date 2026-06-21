<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Models\RegimeImposition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class RegimeImpositionController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:PARAMFISC_CONSULTER', only: ['index']),
            new Middleware('can:PARAMFISC_GERER', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = RegimeImposition::query();

        if ($request->filled('libelle')) {
            $terme = '%' . $request->libelle . '%';
            $query->where(fn ($q) => $q->where('libelle', 'ilike', $terme)
                                       ->orWhere('libelle_court', 'ilike', $terme)
                                       ->orWhere('code', 'ilike', $terme));
        }

        $regimes = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('parametrage.regimes-imposition.index', compact('regimes'));
    }

    public function create(): View
    {
        return view('parametrage.regimes-imposition.create');
    }

    public function store(Request $request): RedirectResponse
    {
        RegimeImposition::create($this->valider($request));

        return redirect()->route('parametrage.regimes-imposition.index')
            ->with('success', 'Régime d\'imposition créé avec succès.');
    }

    public function edit(RegimeImposition $regimeImposition): View
    {
        return view('parametrage.regimes-imposition.edit', compact('regimeImposition'));
    }

    public function update(Request $request, RegimeImposition $regimeImposition): RedirectResponse
    {
        $regimeImposition->update($this->valider($request, $regimeImposition->id));

        return redirect()->route('parametrage.regimes-imposition.index')
            ->with('success', 'Régime d\'imposition mis à jour.');
    }

    public function destroy(RegimeImposition $regimeImposition): RedirectResponse
    {
        if ($regimeImposition->contribuables()->exists()) {
            return back()->with('error', 'Impossible de supprimer un régime rattaché à des contribuables.');
        }

        $regimeImposition->delete();

        return redirect()->route('parametrage.regimes-imposition.index')
            ->with('success', 'Régime d\'imposition supprimé.');
    }

    /** Validation partagée store/update (code unique, bornes de CA cohérentes). */
    private function valider(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code'          => ['required', 'string', 'max:3', Rule::unique('regime_imposition', 'code')->ignore($ignoreId)],
            'libelle_court' => ['nullable', 'string', 'max:16'],
            'libelle'       => ['required', 'string', 'max:255'],
            'ca_borne_inf'  => ['required', 'numeric', 'min:0'],
            // borne_sup = 0 ⇒ tranche ouverte ; sinon doit être ≥ borne_inf
            'ca_borne_sup'  => ['required', 'numeric', 'min:0', function ($attribut, $valeur, $echec) use ($request) {
                if ((float) $valeur !== 0.0 && (float) $valeur < (float) $request->input('ca_borne_inf')) {
                    $echec('La borne supérieure doit être 0 (tranche ouverte) ou supérieure ou égale à la borne inférieure.');
                }
            }],
        ], [], [
            'libelle_court' => 'libellé court',
            'ca_borne_inf'  => 'borne inférieure de CA',
            'ca_borne_sup'  => 'borne supérieure de CA',
        ]);
    }
}
