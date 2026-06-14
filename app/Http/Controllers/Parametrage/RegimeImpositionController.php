<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Models\RegimeImposition;
use Illuminate\Http\Request;

class RegimeImpositionController extends Controller
{
    public function index(Request $request)
    {
        $query = RegimeImposition::query();

        if ($request->filled('libelle')) {
            $terme = '%' . $request->libelle . '%';
            $query->where('libelle', 'ilike', $terme)
                  ->orWhere('libelle_court', 'ilike', $terme);
        }

        $regimes = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('parametrage.regimes-imposition.index', compact('regimes'));
    }
}
