<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pénalité par déclaration complémentaire (redressement). Le montant de
 * l'émission inclut droits + pénalité ; la pénalité est conservée pour le
 * détail. Nulle/0 pour les émissions ordinaires.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->decimal('penalite', 15, 2)->default(0)->after('redressement_id');
        });
    }

    public function down(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->dropColumn('penalite');
        });
    }
};
