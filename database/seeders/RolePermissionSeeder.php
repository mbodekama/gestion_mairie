<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Catalogue complet des permissions (code = name spatie)
        $permissions = [
            // Contribuables
            'CONTRIB_CONSULTER', 'CONTRIB_CREER', 'CONTRIB_MODIFIER', 'CONTRIB_SUPPRIMER',
            // Établissements
            'ETAB_CONSULTER', 'ETAB_CREER', 'ETAB_MODIFIER', 'ETAB_SUPPRIMER',
            // Dirigeants
            'DIRIG_CONSULTER', 'DIRIG_CREER', 'DIRIG_MODIFIER', 'DIRIG_SUPPRIMER',
            // Référentiel activités
            'ACTIVITE_CONSULTER', 'ACTIVITE_GERER',
            // Paramétrage fiscal
            'PARAMFISC_CONSULTER', 'PARAMFISC_GERER',
            // Exercices fiscaux
            'EXERCICE_CONSULTER', 'EXERCICE_OUVRIR', 'EXERCICE_CLOTURER',
            // Émission des taxes
            'EMISSION_CONSULTER', 'EMISSION_CREER', 'EMISSION_MODIFIER', 'EMISSION_SUPPRIMER',
            'EMISSION_LIQUIDER', 'EMISSION_VALIDER',
            // Taxe foncière
            'TF_CONSULTER', 'TF_CREER', 'TF_MODIFIER', 'TF_LIQUIDER',
            // Recouvrement
            'RECOUVR_CONSULTER', 'RECOUVR_ENCAISSER', 'RECOUVR_VALIDER', 'RECOUVR_ANNULER',
            // Dossiers
            'DOSSIER_CONSULTER', 'DOSSIER_CREER', 'DOSSIER_MODIFIER',
            'DOSSIER_TRANSFERER', 'DOSSIER_ARCHIVER',
            // Convocations
            'CONVOC_CONSULTER', 'CONVOC_CREER', 'CONVOC_MODIFIER', 'CONVOC_IMPRIMER',
            // Exonérations
            'EXO_CONSULTER', 'EXO_CREER', 'EXO_MODIFIER', 'EXO_SUPPRIMER',
            // Contrôle / sanctions
            'CONTROLE_CONSULTER', 'CONTROLE_GERER', 'CONTROLE_SANCTIONNER',
            // Workflow du contrôle (valideurs distincts par étape)
            'CONTROLE_INSTRUIRE', 'CONTROLE_VALIDER', 'CONTROLE_EXECUTER',
            'CONTROLE_CLOTURER', 'CONTROLE_REDRESSER',
            // Redressement
            'REDRESS_CONSULTER', 'REDRESS_GERER',
            // Référentiel territorial
            'TERRITOIRE_CONSULTER', 'TERRITOIRE_GERER',
            // Collectivités
            'COLLECTIVITE_CONSULTER', 'COLLECTIVITE_GERER',
            // Agents
            'AGENT_CONSULTER', 'AGENT_CREER', 'AGENT_MODIFIER', 'AGENT_SUPPRIMER',
            // Sécurité / utilisateurs
            'SECURITE_CONSULTER', 'SECURITE_GERER_UTILISATEUR', 'SECURITE_GERER_ROLE', 'SECURITE_RESET_MDP',
            // Pilotage / objectifs
            'PILOTAGE_CONSULTER', 'PILOTAGE_GERER',
            // Éditions / états
            'EDITION_GENERER', 'EDITION_EXPORTER',
            // Audit
            'AUDIT_CONSULTER',
        ];

        foreach ($permissions as $code) {
            Permission::findOrCreate($code);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Rôles et attribution des permissions

        // ADMIN : toutes les permissions
        Role::findOrCreate('ADMIN')->syncPermissions($permissions);

        // CONSULT : lecture seule (toutes les *_CONSULTER)
        Role::findOrCreate('CONSULT')->syncPermissions(
            array_values(array_filter($permissions, fn ($p) => str_ends_with($p, '_CONSULTER')))
        );

        // ADMIN_FISC : tout le métier fiscal, sans sécurité système ni gestion des agents
        $prefixesMetier = [
            'CONTRIB_', 'ETAB_', 'DIRIG_', 'ACTIVITE_', 'PARAMFISC_', 'EXERCICE_',
            'EMISSION_', 'TF_', 'RECOUVR_', 'DOSSIER_', 'CONVOC_', 'EXO_',
            'CONTROLE_', 'REDRESS_', 'TERRITOIRE_', 'COLLECTIVITE_', 'PILOTAGE_', 'EDITION_', 'AUDIT_',
        ];
        Role::findOrCreate('ADMIN_FISC')->syncPermissions(
            array_values(array_filter($permissions, function ($p) use ($prefixesMetier) {
                foreach ($prefixesMetier as $prefix) {
                    if (str_starts_with($p, $prefix)) {
                        return true;
                    }
                }
                return false;
            }))
        );

        // AGENT_RECENS : recensement contribuables / établissements / dirigeants
        Role::findOrCreate('AGENT_RECENS')->syncPermissions([
            'CONTRIB_CONSULTER', 'CONTRIB_CREER', 'CONTRIB_MODIFIER',
            'ETAB_CONSULTER', 'ETAB_CREER', 'ETAB_MODIFIER',
            'DIRIG_CONSULTER', 'DIRIG_CREER', 'DIRIG_MODIFIER',
            'ACTIVITE_CONSULTER', 'TERRITOIRE_CONSULTER',
            'DOSSIER_CONSULTER', 'EDITION_GENERER',
        ]);

        // AGENT_LIQUID : émission et liquidation des taxes
        Role::findOrCreate('AGENT_LIQUID')->syncPermissions([
            'CONTRIB_CONSULTER', 'ETAB_CONSULTER',
            'PARAMFISC_CONSULTER', 'EXERCICE_CONSULTER', 'ACTIVITE_CONSULTER',
            'EMISSION_CONSULTER', 'EMISSION_CREER', 'EMISSION_MODIFIER',
            'EMISSION_LIQUIDER', 'EMISSION_VALIDER',
            'TF_CONSULTER', 'TF_CREER', 'TF_MODIFIER', 'TF_LIQUIDER',
            'EXO_CONSULTER', 'EDITION_GENERER',
        ]);

        // CAISSIER : encaissement des règlements
        Role::findOrCreate('CAISSIER')->syncPermissions([
            'CONTRIB_CONSULTER', 'ETAB_CONSULTER',
            'EMISSION_CONSULTER', 'TF_CONSULTER',
            'RECOUVR_CONSULTER', 'RECOUVR_ENCAISSER', 'RECOUVR_VALIDER',
            'EDITION_GENERER',
        ]);

        // GEST_DOSSIER : suivi et circulation des dossiers
        Role::findOrCreate('GEST_DOSSIER')->syncPermissions([
            'CONTRIB_CONSULTER', 'ETAB_CONSULTER',
            'DOSSIER_CONSULTER', 'DOSSIER_CREER', 'DOSSIER_MODIFIER',
            'DOSSIER_TRANSFERER', 'DOSSIER_ARCHIVER',
            'EDITION_GENERER',
        ]);

        // CONTROLEUR : instruit et exécute les contrôles (sans valider/clôturer)
        Role::findOrCreate('CONTROLEUR')->syncPermissions([
            'CONTRIB_CONSULTER', 'ETAB_CONSULTER',
            'EMISSION_CONSULTER', 'RECOUVR_CONSULTER',
            'CONVOC_CONSULTER', 'CONVOC_CREER', 'CONVOC_MODIFIER', 'CONVOC_IMPRIMER',
            'CONTROLE_CONSULTER', 'CONTROLE_SANCTIONNER',
            'CONTROLE_INSTRUIRE', 'CONTROLE_EXECUTER',
            'REDRESS_CONSULTER',
            'EXO_CONSULTER', 'EXO_CREER', 'EXO_MODIFIER',
            'EDITION_GENERER',
        ]);

        // SUPERVISEUR_CONTROLE : valide, clôture et décide du redressement
        // (séparation des responsabilités : ne peut pas instruire/exécuter)
        Role::findOrCreate('SUPERVISEUR_CONTROLE')->syncPermissions([
            'CONTRIB_CONSULTER', 'ETAB_CONSULTER',
            'EMISSION_CONSULTER', 'RECOUVR_CONSULTER',
            'CONVOC_CONSULTER', 'CONVOC_IMPRIMER',
            'CONTROLE_CONSULTER', 'CONTROLE_VALIDER', 'CONTROLE_CLOTURER', 'CONTROLE_REDRESSER',
            'REDRESS_CONSULTER', 'REDRESS_GERER',
            'EDITION_GENERER',
        ]);

        // 3. Utilisateurs de démarrage (un par rôle, mot de passe : password)
        $utilisateurs = [
            ['name' => 'Administrateur Système', 'email' => 'admin@mairie.ci',       'role' => 'ADMIN'],
            ['name' => 'Responsable Fiscal',     'email' => 'respfisc@mairie.ci',    'role' => 'ADMIN_FISC'],
            ['name' => 'Agent Recensement',      'email' => 'recensement@mairie.ci', 'role' => 'AGENT_RECENS'],
            ['name' => 'Agent Liquidation',      'email' => 'liquidation@mairie.ci', 'role' => 'AGENT_LIQUID'],
            ['name' => 'Caissier',               'email' => 'caisse@mairie.ci',      'role' => 'CAISSIER'],
            ['name' => 'Gestionnaire Dossiers',  'email' => 'dossiers@mairie.ci',    'role' => 'GEST_DOSSIER'],
            ['name' => 'Contrôleur Fiscal',      'email' => 'controleur@mairie.ci',  'role' => 'CONTROLEUR'],
            ['name' => 'Superviseur Contrôle',   'email' => 'superviseur@mairie.ci', 'role' => 'SUPERVISEUR_CONTROLE'],
            ['name' => 'Consultant',             'email' => 'consult@mairie.ci',     'role' => 'CONSULT'],
        ];

        foreach ($utilisateurs as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password')]
            );
            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
