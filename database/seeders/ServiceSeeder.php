<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Référentiel ESSENTIEL : organigramme des services de la mairie (départements
 * de service + services), source de la liste déroulante « Service » lors de la
 * création / modification d'un agent.
 *
 * Idempotent : chaque ligne est insérée/mise à jour par son `code` unique.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $collectiviteId = DB::table('collectivite')->where('code', 'ABJ')->value('id');

        // 1. Départements de service (regroupements / directions).
        $departements = [
            ['code' => 'DGS', 'libelle' => 'Direction Générale des Services',     'sigle' => 'DGS'],
            ['code' => 'DAF', 'libelle' => 'Direction Administrative et Financière', 'sigle' => 'DAF'],
            ['code' => 'DSF', 'libelle' => 'Direction des Services Fiscaux',       'sigle' => 'DSF'],
            ['code' => 'DT',  'libelle' => 'Direction Technique',                  'sigle' => 'DT'],
            ['code' => 'SG',  'libelle' => 'Secrétariat Général',                  'sigle' => 'SG'],
        ];

        foreach ($departements as $dep) {
            DB::table('departement_service')->updateOrInsert(
                ['code' => $dep['code']],
                ['libelle' => $dep['libelle'], 'sigle' => $dep['sigle']],
            );
        }

        $depIds = DB::table('departement_service')->pluck('id', 'code');

        // 2. Services rattachés aux départements.
        $services = [
            // Direction des Services Fiscaux (cœur métier)
            ['code' => 'ASSIET', 'libelle' => "Service de l'Assiette et du Recensement", 'sigle' => 'SAR', 'dep' => 'DSF'],
            ['code' => 'LIQUID', 'libelle' => "Service de la Liquidation et de l'Émission", 'sigle' => 'SLE', 'dep' => 'DSF'],
            ['code' => 'RECOUV', 'libelle' => 'Service du Recouvrement',                  'sigle' => 'SR',  'dep' => 'DSF'],
            ['code' => 'CAISSE', 'libelle' => 'Service de la Caisse et des Recettes',     'sigle' => 'SCR', 'dep' => 'DSF'],
            ['code' => 'CTRLF',  'libelle' => 'Service du Contrôle Fiscal',               'sigle' => 'SCF', 'dep' => 'DSF'],
            ['code' => 'DOSSIE', 'libelle' => 'Service des Dossiers et du Contentieux',   'sigle' => 'SDC', 'dep' => 'DSF'],
            ['code' => 'FONCIE', 'libelle' => 'Service du Foncier et du Domaine',         'sigle' => 'SFD', 'dep' => 'DSF'],

            // Direction Administrative et Financière
            ['code' => 'BUDGET', 'libelle' => 'Service du Budget',                        'sigle' => 'SB',  'dep' => 'DAF'],
            ['code' => 'COMPTA', 'libelle' => 'Service de la Comptabilité',              'sigle' => 'SC',  'dep' => 'DAF'],
            ['code' => 'RH',     'libelle' => 'Service des Ressources Humaines',          'sigle' => 'SRH', 'dep' => 'DAF'],

            // Direction Technique
            ['code' => 'URBANI', 'libelle' => "Service de l'Urbanisme",                   'sigle' => 'SU',  'dep' => 'DT'],
            ['code' => 'TECHNI', 'libelle' => 'Service Technique et de la Voirie',        'sigle' => 'STV', 'dep' => 'DT'],

            // Secrétariat Général
            ['code' => 'ETATCV', 'libelle' => "Service de l'État Civil",                  'sigle' => 'SEC', 'dep' => 'SG'],
            ['code' => 'INFORM', 'libelle' => "Service Informatique et Système d'Information", 'sigle' => 'SI', 'dep' => 'SG'],
            ['code' => 'COURRI', 'libelle' => 'Service du Courrier et des Archives',      'sigle' => 'SCA', 'dep' => 'SG'],
        ];

        foreach ($services as $svc) {
            DB::table('service')->updateOrInsert(
                ['code' => $svc['code']],
                [
                    'libelle'                => $svc['libelle'],
                    'sigle'                  => $svc['sigle'],
                    'collectivite_id'        => $collectiviteId,
                    'departement_service_id' => $depIds[$svc['dep']] ?? null,
                ],
            );
        }
    }
}
