<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forme_juridique', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('nom_court', 32)->nullable();
            $table->string('libelle', 128);
        });

        Schema::create('regime_imposition', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle')->nullable();
            $table->decimal('ca_borne_inf', 18, 2)->default(0);
            $table->decimal('ca_borne_sup', 18, 2)->default(0);
        });

        Schema::create('banque', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle')->nullable();
        });

        Schema::create('contribuable', function (Blueprint $table) {
            $table->id();
            $table->string('numero_identifiant', 12)->unique();
            $table->string('numero_compte', 12)->unique();
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->string('type_personne', 2);
            // Personne physique
            $table->string('nom', 64)->nullable();
            $table->string('prenoms', 128)->nullable();
            $table->char('sexe', 1)->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 64)->nullable();
            $table->foreignId('nationalite_id')->nullable()->constrained('nationalite');
            $table->string('numero_piece', 64)->nullable();
            $table->string('nature_piece', 20)->nullable();
            $table->string('nom_pere', 64)->nullable();
            $table->string('prenoms_pere', 128)->nullable();
            $table->string('nom_mere', 64)->nullable();
            $table->string('prenoms_mere', 128)->nullable();
            // Personne morale
            $table->string('raison_sociale', 128)->nullable();
            $table->string('sigle', 64)->nullable();
            $table->string('denomination_commerciale')->nullable();
            $table->foreignId('forme_juridique_id')->nullable()->constrained('forme_juridique');
            $table->string('registre_commerce', 32)->nullable();
            $table->date('date_registre_commerce')->nullable();
            $table->string('ville_registre_commerce', 64)->nullable();
            $table->integer('nombre_associes')->nullable();
            $table->decimal('capital_social', 18, 2)->nullable();
            // Régime / contacts
            $table->foreignId('regime_imposition_id')->nullable()->constrained('regime_imposition');
            $table->string('boite_postale')->nullable();
            $table->string('telephone', 32)->nullable();
            $table->string('cellulaire', 32)->nullable();
            $table->string('fax', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('photo_uri', 512)->nullable();
            $table->string('statut', 12)->default('ACTIF');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
            $table->timestampTz('supprime_le')->nullable();
        });
        DB::statement("ALTER TABLE contribuable ADD CONSTRAINT ck_contrib_type_personne CHECK (type_personne IN ('PP','PM'))");
        DB::statement("ALTER TABLE contribuable ADD CONSTRAINT ck_contrib_sexe CHECK (sexe IN ('M','F'))");
        DB::statement("ALTER TABLE contribuable ADD CONSTRAINT ck_contrib_statut CHECK (statut IN ('ACTIF','RADIE','SUSPENDU'))");
        DB::statement("ALTER TABLE contribuable ADD CONSTRAINT ck_contrib_pp CHECK (type_personne <> 'PP' OR nom IS NOT NULL)");
        DB::statement("ALTER TABLE contribuable ADD CONSTRAINT ck_contrib_pm CHECK (type_personne <> 'PM' OR raison_sociale IS NOT NULL)");
        DB::statement('CREATE INDEX ix_contrib_nom ON contribuable(nom, prenoms)');
        DB::statement('CREATE INDEX ix_contrib_rs ON contribuable(raison_sociale)');
        DB::statement('CREATE INDEX ix_contrib_coll ON contribuable(collectivite_id)');
        DB::statement('CREATE TRIGGER tg_contribuable BEFORE UPDATE ON contribuable FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        Schema::create('coordonnee_bancaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_id')->constrained('contribuable')->cascadeOnDelete();
            $table->foreignId('banque_id')->nullable()->constrained('banque');
            $table->string('code_guichet', 16)->nullable();
            $table->string('numero_compte', 34)->nullable();
            $table->string('cle_rib', 8)->nullable();
            $table->string('nom_agence', 128)->nullable();
        });

        Schema::create('qualite_dirigeant', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle', 128);
        });

        Schema::create('dirigeant', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('organisation_id')->nullable()->constrained('organisation');
            $table->foreignId('qualite_dirigeant_id')->nullable()->constrained('qualite_dirigeant');
            $table->string('nom', 64)->nullable();
            $table->string('prenoms', 128)->nullable();
            $table->string('adresse', 128)->nullable();
            $table->string('telephone', 16)->nullable();
            $table->string('cellulaire', 16)->nullable();
            $table->string('email')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestampsTz();
        });
        DB::statement('CREATE TRIGGER tg_dirigeant BEFORE UPDATE ON dirigeant FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');
    }

    public function down(): void
    {
        Schema::dropIfExists('dirigeant');
        Schema::dropIfExists('qualite_dirigeant');
        Schema::dropIfExists('coordonnee_bancaire');
        Schema::dropIfExists('contribuable');
        Schema::dropIfExists('banque');
        Schema::dropIfExists('regime_imposition');
        Schema::dropIfExists('forme_juridique');
    }
};
