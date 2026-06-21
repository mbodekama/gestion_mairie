<?php

namespace App\Http\Controllers\Parametrage;

use App\Http\Controllers\Controller;
use App\Models\StatutContribuable;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class StatutContribuableController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:PARAMFISC_CONSULTER', only: ['index']),
        ];
    }

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
