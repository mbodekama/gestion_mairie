<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompteUtilisateurRequest;
use App\Models\Agent;
use App\Models\User;
use App\Services\CompteUtilisateurService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Volet « accès » des agents : gestion des comptes utilisateurs (création,
 * rôles, réinitialisation du mot de passe, activation, suppression).
 *
 * Routes imbriquées sous agents/{agent}/comptes, protégées par la permission
 * SECURITE_GERER_UTILISATEUR.
 */
class CompteUtilisateurController extends Controller
{
    public function __construct(private CompteUtilisateurService $service) {}

    public function create(Agent $agent): View|RedirectResponse
    {
        if ($agent->utilisateurs()->exists()) {
            return redirect()->route('agents.show', $agent)
                ->with('warning', "Cet agent dispose déjà d'un compte utilisateur.");
        }

        return view('administration.comptes.create', [
            'agent'         => $agent,
            'roles'         => $this->roles(),
            'nomParDefaut'  => trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')),
        ]);
    }

    public function store(CompteUtilisateurRequest $request, Agent $agent): RedirectResponse
    {
        if ($agent->utilisateurs()->exists()) {
            return redirect()->route('agents.show', $agent)
                ->with('warning', "Cet agent dispose déjà d'un compte utilisateur.");
        }

        $donnees = $request->validated();
        $compte  = $this->service->creer($agent, $donnees);

        $envoye = $this->service->envoyerIdentifiants($compte, $donnees['password']);

        return redirect()->route('agents.show', $agent)->with(
            $envoye ? 'success' : 'warning',
            "Compte « {$compte->email} » créé et rattaché à l'agent {$agent->matricule}. " .
            ($envoye
                ? 'Les identifiants de connexion ont été envoyés par e-mail.'
                : "L'e-mail d'identifiants n'a pas pu être envoyé — communiquez-les manuellement.")
        );
    }

    public function edit(Agent $agent, User $compte): View
    {
        $this->verifierRattachement($agent, $compte);

        return view('administration.comptes.edit', [
            'agent'        => $agent,
            'compte'       => $compte,
            'roles'        => $this->roles(),
            'rolesActuels' => $compte->getRoleNames()->all(),
        ]);
    }

    public function update(CompteUtilisateurRequest $request, Agent $agent, User $compte): RedirectResponse
    {
        $this->verifierRattachement($agent, $compte);

        $donnees = $request->validated();
        $this->service->mettreAJour($compte, $donnees);

        // Réinitialisation : si un nouveau mot de passe est saisi, on renvoie
        // les identifiants au titulaire.
        if (! empty($donnees['password'])) {
            $envoye = $this->service->envoyerIdentifiants($compte, $donnees['password']);

            return redirect()->route('agents.show', $agent)->with(
                $envoye ? 'success' : 'warning',
                "Compte « {$compte->email} » mis à jour. " .
                ($envoye
                    ? 'Les nouveaux identifiants ont été envoyés par e-mail.'
                    : "L'e-mail d'identifiants n'a pas pu être envoyé — communiquez-les manuellement.")
            );
        }

        return redirect()->route('agents.show', $agent)
            ->with('success', "Compte « {$compte->email} » mis à jour.");
    }

    /** Catalogue des rôles affectables, triés par nom. */
    private function roles()
    {
        return Role::orderBy('name')->pluck('name');
    }

    /** Garantit que le compte appartient bien à l'agent de l'URL. */
    private function verifierRattachement(Agent $agent, User $compte): void
    {
        abort_unless((int) $compte->agent_id === (int) $agent->id, 404);
    }
}
