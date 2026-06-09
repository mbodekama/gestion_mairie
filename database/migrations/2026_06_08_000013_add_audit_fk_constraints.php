<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ajout des FK created_by / updated_by → utilisateur(id)
// Séparées car utilisateur n'existait pas lors de la création des tables métier.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contribuable', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_contrib_cb')->references('id')->on('utilisateur')->nullOnDelete();
            $table->foreign('updated_by', 'fk_contrib_ub')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('etablissement', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_etab_cb')->references('id')->on('utilisateur')->nullOnDelete();
            $table->foreign('updated_by', 'fk_etab_ub')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_emis_cb')->references('id')->on('utilisateur')->nullOnDelete();
            $table->foreign('updated_by', 'fk_emis_ub')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('emission_cotisation_fonciere', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_emiscot_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('reglement_taxe', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_regl_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('dossier', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_doss_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('historique_dossier', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_histdoss_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('convocation', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_conv_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('exoneration', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_exo_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('obligation', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_oblig_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('objectif', function (Blueprint $table) {
            $table->foreign('created_by', 'fk_obj_cb')->references('id')->on('utilisateur')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('objectif', fn($t) => $t->dropForeign('fk_obj_cb'));
        Schema::table('obligation', fn($t) => $t->dropForeign('fk_oblig_cb'));
        Schema::table('exoneration', fn($t) => $t->dropForeign('fk_exo_cb'));
        Schema::table('convocation', fn($t) => $t->dropForeign('fk_conv_cb'));
        Schema::table('historique_dossier', fn($t) => $t->dropForeign('fk_histdoss_cb'));
        Schema::table('dossier', fn($t) => $t->dropForeign('fk_doss_cb'));
        Schema::table('reglement_taxe', fn($t) => $t->dropForeign('fk_regl_cb'));
        Schema::table('emission_cotisation_fonciere', fn($t) => $t->dropForeign('fk_emiscot_cb'));
        Schema::table('emission_taxe', function ($t) {
            $t->dropForeign('fk_emis_cb');
            $t->dropForeign('fk_emis_ub');
        });
        Schema::table('etablissement', function ($t) {
            $t->dropForeign('fk_etab_cb');
            $t->dropForeign('fk_etab_ub');
        });
        Schema::table('contribuable', function ($t) {
            $t->dropForeign('fk_contrib_cb');
            $t->dropForeign('fk_contrib_ub');
        });
    }
};
