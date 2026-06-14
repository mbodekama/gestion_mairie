<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Models\StatutContribuable;
use Illuminate\Http\Request;

class StatutContribuableController extends Controller
{
    public function index(Request $request)
    {
        $query = StatutContribuable::query();

        if ($request->filled('libelle')) {
            $query->where('libelle', 'ilike', '%' . $request->libelle . '%');
        }

        $statuts = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('parametrage.statuts-contribuable.index', compact('statuts'));
    }
}
