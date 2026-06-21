<?php

namespace App\Services;

use App\Mail\IdentifiantsCompte;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Gestion des comptes utilisateurs (accès applicatif) rattachés aux agents :
 * création, mise à jour des informations, des rôles et du mot de passe.
 *
 * Le hachage du mot de passe est assuré par le cast `password => hashed` du
 * modèle User ; aucun mot de passe en clair n'est stocké ni journalisé.
 */
class CompteUtilisateurService
{
    public function __construct(private AuditService $audit) {}

    /**
     * Crée un compte rattaché à l'agent et lui affecte les rôles fournis.
     *
     * @param  array{name:string,email:string,password:string,actif?:bool,roles?:array<string>}  $donnees
     */
    public function creer(Agent $agent, array $donnees): User
    {
        return DB::transaction(function () use ($agent, $donnees): User {
            $user = $agent->utilisateurs()->create([
                'name'     => $donnees['name'],
                'email'    => $donnees['email'],
                'password' => $donnees['password'],
                'actif'    => $donnees['actif'] ?? true,
            ]);

            $user->syncRoles($donnees['roles'] ?? []);

            // Journalisation (jamais le mot de passe).
            $this->audit->enregistrer('users', $user->id, 'INSERT', null, [
                '_evenement' => 'Création du compte utilisateur',
                'name'       => $user->name,
                'email'      => $user->email,
                'roles'      => $user->getRoleNames()->all(),
                'actif'      => $user->actif,
            ]);

            return $user;
        });
    }

    /**
     * Met à jour les informations, les rôles, l'activation et, si fourni, le
     * mot de passe d'un compte existant.
     *
     * @param  array{name:string,email:string,password?:?string,actif?:bool,roles?:array<string>}  $donnees
     */
    public function mettreAJour(User $user, array $donnees): User
    {
        return DB::transaction(function () use ($user, $donnees): User {
            $attributs = [
                'name'  => $donnees['name'],
                'email' => $donnees['email'],
                'actif' => $donnees['actif'] ?? false,
            ];

            // Mot de passe optionnel : seulement s'il est renseigné (réinitialisation).
            if (! empty($donnees['password'])) {
                $attributs['password'] = $donnees['password'];
            }

            $motDePasseChange = ! empty($donnees['password']);

            $user->update($attributs);
            $user->syncRoles($donnees['roles'] ?? []);

            // Journalise spécifiquement la réinitialisation du mot de passe.
            if ($motDePasseChange) {
                $this->audit->enregistrer('users', $user->id, 'UPDATE', null, [
                    '_evenement' => 'Réinitialisation du mot de passe',
                ]);
            }

            return $user;
        });
    }

    /**
     * Envoie au titulaire ses identifiants de connexion (e-mail + mot de passe
     * en clair, à usage unique). Synchrone et tolérant aux pannes : un échec
     * d'envoi est journalisé sans interrompre la création du compte.
     *
     * @return bool  true si l'e-mail a été remis au transport, false sinon.
     */
    public function envoyerIdentifiants(User $user, string $motDePasse): bool
    {
        try {
            Mail::to($user->email)->send(
                new IdentifiantsCompte($user, $motDePasse, route('login'))
            );

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
