<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Référentiel du workflow de contrôle fiscal : états et transitions autorisées.
 * Données idempotentes (rejouables sans doublon).
 */
class ControleWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // ── États ──
        $etats = [
            ['code' => 'INSTRUCTION', 'libelle' => 'Instruction',  'ordre' => 1, 'est_final' => false, 'type_issue' => null],
            ['code' => 'VALIDE',      'libelle' => 'Validé',        'ordre' => 2, 'est_final' => false, 'type_issue' => null],
            ['code' => 'EXECUTE',     'libelle' => 'Exécuté',       'ordre' => 3, 'est_final' => false, 'type_issue' => null],
            ['code' => 'CLOTURE',     'libelle' => 'Clôturé',       'ordre' => 4, 'est_final' => true,  'type_issue' => 'favorable'],
            ['code' => 'REDRESSE',    'libelle' => 'En redressement','ordre' => 4, 'est_final' => true,  'type_issue' => 'defaillant'],
        ];

        foreach ($etats as $etat) {
            DB::table('etat_controle')->updateOrInsert(['code' => $etat['code']], $etat);
        }

        $id = fn (string $code) => DB::table('etat_controle')->where('code', $code)->value('id');

        // ── Transitions (source → cible) ──
        $transitions = [
            ['INSTRUCTION', 'VALIDE',      'CONTROLE_VALIDER',    'Valider le contrôle',      'convocation'],
            ['VALIDE',      'EXECUTE',     'CONTROLE_EXECUTER',   'Saisir le rapport',        'rapport'],
            ['EXECUTE',     'CLOTURE',     'CONTROLE_CLOTURER',   'Clôturer (favorable)',     'cloture'],
            ['EXECUTE',     'REDRESSE',    'CONTROLE_REDRESSER',  'Ouvrir un redressement',   'redressement'],
            // Renvoi pour correction
            ['VALIDE',      'INSTRUCTION', 'CONTROLE_VALIDER',    'Renvoyer en instruction',  null],
        ];

        foreach ($transitions as [$source, $cible, $permission, $libelle, $effet]) {
            DB::table('transition_controle')->updateOrInsert(
                ['etat_source_id' => $id($source), 'etat_cible_id' => $id($cible)],
                [
                    'permission'     => $permission,
                    'libelle_action' => $libelle,
                    'effet'          => $effet,
                ]
            );
        }
    }
}
