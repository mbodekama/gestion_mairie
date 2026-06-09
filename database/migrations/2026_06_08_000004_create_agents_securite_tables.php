<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Note : les tables role / permission / role_permission / utilisateur_role
// sont gérées par spatie/laravel-permission (migration déjà présente).

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_agent', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('libelle', 64);
        });

        Schema::create('fonction_agent', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('libelle');
        });

        Schema::create('agent', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 32)->unique();
            $table->string('nom', 64)->nullable();
            $table->string('prenoms', 128)->nullable();
            $table->foreignId('fonction_agent_id')->nullable()->constrained('fonction_agent');
            $table->foreignId('grade_agent_id')->nullable()->constrained('grade_agent');
            $table->foreignId('service_id')->nullable()->constrained('service');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->unsignedBigInteger('superieur_id')->nullable();
            $table->string('observation')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestampsTz();
        });
        Schema::table('agent', function (Blueprint $table) {
            $table->foreign('superieur_id')->references('id')->on('agent')->nullOnDelete();
        });
        DB::statement('CREATE TRIGGER tg_agent BEFORE UPDATE ON agent FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

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

        Schema::create('journal_connexion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateur');
            $table->string('login', 64)->nullable();
            $table->string('application', 128)->nullable();
            $table->boolean('succes')->default(true);
            $table->ipAddress('adresse_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestampTz('horodatage')->default(DB::raw('now()'));
        });
        DB::statement('CREATE INDEX ix_journal_connexion_user ON journal_connexion(utilisateur_id, horodatage)');

        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('table_cible', 64);
            $table->string('cle_ligne', 64);
            $table->string('action', 8);
            $table->jsonb('donnees_avant')->nullable();
            $table->jsonb('donnees_apres')->nullable();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateur');
            $table->timestampTz('horodatage')->default(DB::raw('now()'));
        });
        DB::statement("ALTER TABLE audit_log ADD CONSTRAINT ck_audit_log_action CHECK (action IN ('INSERT','UPDATE','DELETE'))");
        DB::statement('CREATE INDEX ix_audit_log_cible ON audit_log(table_cible, cle_ligne)');
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('journal_connexion');
        Schema::dropIfExists('utilisateur');
        Schema::dropIfExists('agent');
        Schema::dropIfExists('fonction_agent');
        Schema::dropIfExists('grade_agent');
    }
};
