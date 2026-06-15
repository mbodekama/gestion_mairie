<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Traçabilité de l'exonération appliquée à une émission : l'exonération retenue
 * et le montant abattu (réduction). Nul/0 si aucune exonération applicable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->foreignId('exoneration_id')->nullable()->after('redressement_id')
                  ->constrained('exoneration')->nullOnDelete();
            $table->decimal('montant_exonere', 15, 2)->default(0)->after('exoneration_id');
        });
    }

    public function down(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exoneration_id');
            $table->dropColumn('montant_exonere');
        });
    }
};
