<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Motif et auteur de la suppression logique sur contribuable / établissement
        foreach (['contribuable', 'etablissement'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('motif_suppression', 255)->nullable();
                $t->unsignedBigInteger('supprime_par')->nullable();
            });
        }

        // emission_taxe n'avait pas de suppression logique : on l'ajoute
        Schema::table('emission_taxe', function (Blueprint $t) {
            $t->timestampTz('supprime_le')->nullable();
            $t->string('motif_suppression', 255)->nullable();
            $t->unsignedBigInteger('supprime_par')->nullable();
        });
    }

    public function down(): void
    {
        foreach (['contribuable', 'etablissement'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['motif_suppression', 'supprime_par']);
            });
        }

        Schema::table('emission_taxe', function (Blueprint $t) {
            $t->dropColumn(['supprime_le', 'motif_suppression', 'supprime_par']);
        });
    }
};
