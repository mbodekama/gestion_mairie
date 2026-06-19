<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Finalise la dépréciation de la table `utilisateur` au profit de `users` (modèle
// Auth Laravel standard). Repointe les dernières FK résiduelles (journal_connexion,
// audit_log) vers users(id), puis supprime la table `utilisateur` devenue inutile
// (jamais alimentée). Fait suite à 2026_06_13_000001_redirect_audit_fk_to_users.
return new class extends Migration
{
    public function up(): void
    {
        // 1. Repointe les FK utilisateur_id résiduelles vers users(id).
        Schema::table('journal_connexion', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->foreign('utilisateur_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('audit_log', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->foreign('utilisateur_id')->references('id')->on('users')->nullOnDelete();
        });

        // 2. Supprime la table `utilisateur` (et son trigger) désormais orpheline.
        DB::statement('DROP TRIGGER IF EXISTS tg_utilisateur ON utilisateur');
        Schema::dropIfExists('utilisateur');
    }

    public function down(): void
    {
        // 1. Recrée la table `utilisateur` à l'identique de sa migration d'origine.
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->id();
            $table->string('login', 64)->unique();
            $table->string('email', 128)->nullable()->unique();
            $table->string('mot_de_passe', 255);
            $table->string('nom', 128)->nullable();
            $table->string('prenoms', 255)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agent');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->string('statut', 12)->default('ACTIF');
            $table->boolean('mfa_active')->default(false);
            $table->date('date_creation')->default(DB::raw('CURRENT_DATE'));
            $table->date('date_expiration')->nullable();
            $table->timestampTz('derniere_connexion')->nullable();
            $table->timestampsTz();
        });
        DB::statement("ALTER TABLE utilisateur ADD CONSTRAINT ck_utilisateur_statut CHECK (statut IN ('ACTIF','SUSPENDU','EXPIRE','VERROUILLE'))");
        DB::statement('CREATE TRIGGER tg_utilisateur BEFORE UPDATE ON utilisateur FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        // 2. Repointe les FK vers utilisateur(id).
        Schema::table('journal_connexion', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->foreign('utilisateur_id')->references('id')->on('utilisateur')->nullOnDelete();
        });

        Schema::table('audit_log', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->foreign('utilisateur_id')->references('id')->on('utilisateur')->nullOnDelete();
        });
    }
};
