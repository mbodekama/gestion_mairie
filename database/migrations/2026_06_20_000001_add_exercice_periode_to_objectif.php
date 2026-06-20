<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rattache chaque objectif de recouvrement à un exercice fiscal et lui donne
 * une période couverte (periode_debut / periode_fin). Un exercice peut porter
 * plusieurs objectifs (périodes distinctes) : l'unicité (collectivite, année)
 * est donc levée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('objectif', function (Blueprint $table) {
            $table->dropUnique(['collectivite_id', 'annee']);
            $table->foreignId('exercice_fiscal_id')->nullable()->after('collectivite_id')
                  ->constrained('exercice_fiscal');
            $table->date('periode_debut')->nullable()->after('annee');
            $table->date('periode_fin')->nullable()->after('periode_debut');
        });

        // Rétro-remplissage : rattache les objectifs existants à l'exercice de
        // même année et leur donne par défaut la période complète de l'exercice.
        DB::table('exercice_fiscal')->orderBy('id')
            ->each(function ($ex): void {
                DB::table('objectif')
                    ->where('collectivite_id', $ex->collectivite_id)
                    ->where('annee', $ex->annee)
                    ->whereNull('exercice_fiscal_id')
                    ->update([
                        'exercice_fiscal_id' => $ex->id,
                        'periode_debut'      => $ex->date_debut,
                        'periode_fin'        => $ex->date_fin,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('objectif', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exercice_fiscal_id');
            $table->dropColumn(['periode_debut', 'periode_fin']);
            $table->unique(['collectivite_id', 'annee']);
        });
    }
};
