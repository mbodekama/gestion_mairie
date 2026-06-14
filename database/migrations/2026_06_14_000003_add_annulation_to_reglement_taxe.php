<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reglement_taxe', function (Blueprint $table) {
            // Annulation d'un règlement (avec motif obligatoire). annule_le = null ⇒ actif.
            $table->timestampTz('annule_le')->nullable();
            $table->string('motif_annulation', 255)->nullable();
            $table->unsignedBigInteger('annule_par')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reglement_taxe', function (Blueprint $table) {
            $table->dropColumn(['annule_le', 'motif_annulation', 'annule_par']);
        });
    }
};
