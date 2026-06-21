<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Configuration des rôles : consultation de la liste et gestion des permissions
 * affectées à chaque rôle (les rôles eux-mêmes ne sont ni créés ni supprimés
 * depuis cet écran). Permission requise : SECURITE_GERER_ROLE.
 */
class RoleController extends Controller
{
    /** Libellés lisibles des modules, repérés par le préfixe des permissions. */
    private const MODULES = [
        'CONTRIB'      => 'Contribuables',
        'ETAB'         => 'Établissements',
        'DIRIG'        => 'Dirigeants',
        'ACTIVITE'     => 'Activités économiques',
        'PARAMFISC'    => 'Paramétrage fiscal',
        'EXERCICE'     => 'Exercices fiscaux',
        'EMISSION'     => 'Émission des taxes',
        'TF'           => 'Taxe foncière',
        'RECOUVR'      => 'Recouvrement',
        'DOSSIER'      => 'Dossiers',
        'CONVOC'       => 'Convocations',
        'EXO'          => 'Exonérations',
        'CONTROLE'     => 'Contrôle fiscal',
        'REDRESS'      => 'Redressements',
        'TERRITOIRE'   => 'Référentiel territorial',
        'COLLECTIVITE' => 'Collectivités',
        'AGENT'        => 'Agents',
        'SECURITE'     => 'Sécurité & comptes',
        'PILOTAGE'     => 'Pilotage',
        'EDITION'      => 'Éditions & états',
        'AUDIT'        => 'Audit',
    ];

    public function index(): View
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('administration.roles.index', compact('roles'));
    }

    public function edit(Role $role): View
    {
        $permissionsParModule = Permission::orderBy('name')->get()
            ->groupBy(fn (Permission $p) => explode('_', $p->name)[0]);

        return view('administration.roles.edit', [
            'role'                 => $role,
            'permissionsParModule' => $permissionsParModule,
            'permissionsRole'      => $role->permissions->pluck('name')->all(),
            'modules'              => self::MODULES,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $valide = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $role->syncPermissions($valide['permissions'] ?? []);

        return redirect()->route('administration.roles.index')
            ->with('success', "Permissions du rôle « {$role->name} » mises à jour.");
    }
}
