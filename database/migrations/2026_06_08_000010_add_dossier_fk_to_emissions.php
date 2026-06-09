<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Les colonnes dossier_id existent déjà dans emission_taxe et emission_cotisation_fonciere.
// La FK est ajoutée ici car dossier n'existait pas encore lors de leur création.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->foreign('dossier_id', 'fk_emis_dossier')
                  ->references('id')->on('dossier')->nullOnDelete();
        });

        Schema::table('emission_cotisation_fonciere', function (Blueprint $table) {
            $table->foreign('dossier_id', 'fk_emiscot_dossier')
                  ->references('id')->on('dossier')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('emission_cotisation_fonciere', function (Blueprint $table) {
            $table->dropForeign('fk_emiscot_dossier');
        });

        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->dropForeign('fk_emis_dossier');
        });
    }
};
