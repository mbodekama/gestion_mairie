<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Models\TypePersonne;
use Illuminate\Http\Request;

class TypePersonneController extends Controller
{
    public function index(Request $request)
    {
        $query = TypePersonne::query();

        if ($request->filled('libelle')) {
            $query->where('libelle', 'ilike', '%' . $request->libelle . '%');
        }

        $types = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('parametrage.types-personne.index', compact('types'));
    }
}
