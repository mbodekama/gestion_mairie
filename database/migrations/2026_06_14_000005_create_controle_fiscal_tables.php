<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module « Gestion du Contrôle » — Phase 1.
 *
 * Référentiel de workflow (états + transitions configurables) et entités du
 * contrôle fiscal : dossier maître, constats (rapport) et historique des
 * transitions. La convocation et la sanction existantes sont réutilisées.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Référentiel : états du workflow ──
        Schema::create('etat_controle', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->string('libelle', 64);
            $table->smallInteger('ordre')->default(0);
            $table->boolean('est_final')->default(false);
            // Issue d'un état final : favorable (clôture) ou defaillant (redressement)
            $table->string('type_issue', 16)->nullable();
        });

        // ── Référentiel : transitions autorisées entre états ──
        Schema::create('transition_controle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etat_source_id')->constrained('etat_controle');
            $table->foreignId('etat_cible_id')->constrained('etat_controle');
            // Permission spatie requise pour exécuter la transition
            $table->string('permission', 64);
            $table->string('libelle_action', 64);
            // Effet métier déclenché : convocation | rapport | cloture | redressement
            $table->string('effet', 32)->nullable();
            $table->unique(['etat_source_id', 'etat_cible_id'], 'uq_transition_controle');
        });

        // ── Dossier maître du contrôle ──
        Schema::create('controle_fiscal', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 16)->unique();
            $table->foreignId('etablissement_id')->constrained('etablissement');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('agent_instructeur_id')->nullable()->constrained('agent');
            $table->foreignId('etat_controle_id')->constrained('etat_controle');
            // Convocation générée à la validation (livrable de l'étape 2)
            $table->foreignId('convocation_id')->nullable()->constrained('convocation');
            $table->date('periode_debut')->nullable();
            $table->date('periode_fin')->nullable();
            $table->string('motif')->nullable();
            $table->text('rapport_synthese')->nullable();
            $table->date('date_instruction')->nullable();
            $table->date('date_validation')->nullable();
            $table->date('date_execution')->nullable();
            $table->date('date_cloture')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
        });
        DB::statement('CREATE INDEX ix_controle_etab ON controle_fiscal(etablissement_id)');
        DB::statement('CREATE INDEX ix_controle_etat ON controle_fiscal(etat_controle_id)');
        DB::statement('CREATE TRIGGER tg_controle_fiscal BEFORE UPDATE ON controle_fiscal FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        // ── Constats du rapport (une ligne par nature de taxe contrôlée) ──
        Schema::create('controle_constat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controle_fiscal_id')->constrained('controle_fiscal')->cascadeOnDelete();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('exercice_fiscal_id')->nullable()->constrained('exercice_fiscal');
            $table->decimal('montant_declare', 15, 2)->default(0);
            $table->decimal('montant_verifie', 15, 2)->default(0);
            $table->decimal('ecart', 15, 2)->default(0);
            $table->foreignId('sanction_fiscale_id')->nullable()->constrained('sanction_fiscale');
            $table->string('observation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });

        // ── Historique des transitions d'état ──
        Schema::create('historique_controle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controle_fiscal_id')->constrained('controle_fiscal')->cascadeOnDelete();
            $table->foreignId('etat_source_id')->nullable()->constrained('etat_controle');
            $table->foreignId('etat_cible_id')->nullable()->constrained('etat_controle');
            $table->string('motif')->nullable();
            $table->date('date_mouvement')->default(DB::raw('CURRENT_DATE'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_controle');
        Schema::dropIfExists('controle_constat');
        Schema::dropIfExists('controle_fiscal');
        Schema::dropIfExists('transition_controle');
        Schema::dropIfExists('etat_controle');
    }
};
