<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module « Gestion du Contrôle » — Phase 1 (suite).
 *
 * Dossier de redressement, né d'un contrôle défaillant. Il porte les montants
 * redressés et génère des émissions de taxe complémentaires (recouvrables via
 * le module Recouvrement existant). Liaisons : convocation ↔ contrôle, et
 * emission_taxe → redressement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redressement', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 16)->unique();
            $table->foreignId('controle_fiscal_id')->constrained('controle_fiscal');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->decimal('montant_droits', 15, 2)->default(0);
            $table->decimal('montant_penalites', 15, 2)->default(0);
            $table->decimal('montant_total', 15, 2)->default(0);
            // ouvert | notifie | solde | annule
            $table->string('etat', 16)->default('ouvert');
            $table->date('date_redressement')->nullable();
            $table->string('observation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
            // Un redressement par contrôle
            $table->unique('controle_fiscal_id', 'uq_redressement_controle');
        });
        DB::statement('CREATE TRIGGER tg_redressement BEFORE UPDATE ON redressement FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        // Convocation issue d'un contrôle (lien retour)
        Schema::table('convocation', function (Blueprint $table) {
            $table->foreignId('controle_id')->nullable()->after('etablissement_id')
                  ->constrained('controle_fiscal');
        });

        // Émission complémentaire générée par un redressement
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->foreignId('redressement_id')->nullable()->after('dossier_id')
                  ->constrained('redressement');
        });
    }

    public function down(): void
    {
        Schema::table('emission_taxe', function (Blueprint $table) {
            $table->dropConstrainedForeignId('redressement_id');
        });

        Schema::table('convocation', function (Blueprint $table) {
            $table->dropConstrainedForeignId('controle_id');
        });

        Schema::dropIfExists('redressement');
    }
};
