<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Redirige les FK created_by / updated_by de utilisateur(id) vers users(id)
// pour rester aligné sur le modèle Auth Laravel standard (User → users).

return new class extends Migration
{
    private const TABLES_CB_UB = [
        'contribuable' => ['fk_contrib_cb', 'fk_contrib_ub'],
        'etablissement' => ['fk_etab_cb',   'fk_etab_ub'],
        'emission_taxe' => ['fk_emis_cb',   'fk_emis_ub'],
    ];

    private const TABLES_CB = [
        'emission_cotisation_fonciere' => 'fk_emiscot_cb',
        'reglement_taxe'               => 'fk_regl_cb',
        'dossier'                      => 'fk_doss_cb',
        'historique_dossier'           => 'fk_histdoss_cb',
        'convocation'                  => 'fk_conv_cb',
        'exoneration'                  => 'fk_exo_cb',
        'obligation'                   => 'fk_oblig_cb',
        'objectif'                     => 'fk_obj_cb',
    ];

    public function up(): void
    {
        foreach (self::TABLES_CB_UB as $table => [$fkCb, $fkUb]) {
            Schema::table($table, function (Blueprint $t) use ($fkCb, $fkUb) {
                $t->dropForeign($fkCb);
                $t->dropForeign($fkUb);
                $t->foreign('created_by', $fkCb)->references('id')->on('users')->nullOnDelete();
                $t->foreign('updated_by', $fkUb)->references('id')->on('users')->nullOnDelete();
            });
        }

        foreach (self::TABLES_CB as $table => $fkCb) {
            Schema::table($table, function (Blueprint $t) use ($fkCb) {
                $t->dropForeign($fkCb);
                $t->foreign('created_by', $fkCb)->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES_CB_UB as $table => [$fkCb, $fkUb]) {
            Schema::table($table, function (Blueprint $t) use ($fkCb, $fkUb) {
                $t->dropForeign($fkCb);
                $t->dropForeign($fkUb);
                $t->foreign('created_by', $fkCb)->references('id')->on('utilisateur')->nullOnDelete();
                $t->foreign('updated_by', $fkUb)->references('id')->on('utilisateur')->nullOnDelete();
            });
        }

        foreach (self::TABLES_CB as $table => $fkCb) {
            Schema::table($table, function (Blueprint $t) use ($fkCb) {
                $t->dropForeign($fkCb);
                $t->foreign('created_by', $fkCb)->references('id')->on('utilisateur')->nullOnDelete();
            });
        }
    }
};
