<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secteur_activite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
        });

        Schema::create('categorie_activite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle')->nullable();
        });

        Schema::create('activite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('libelle', 1000);
            $table->foreignId('secteur_activite_id')->constrained('secteur_activite');
            $table->foreignId('categorie_activite_id')->constrained('categorie_activite');
        });

        Schema::create('etablissement', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->foreignId('contribuable_id')->constrained('contribuable');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('activite_id')->constrained('activite');
            $table->string('denomination')->nullable();
            $table->string('type_etablissement', 12);
            // Localisation
            $table->foreignId('commune_id')->constrained('commune');
            $table->foreignId('quartier_id')->nullable()->constrained('quartier');
            $table->foreignId('voie_id')->nullable()->constrained('voie');
            $table->foreignId('zone_fiscale_id')->constrained('zone_fiscale');
            $table->string('adresse', 64)->nullable();
            $table->string('lot_ilot', 10)->nullable();
            $table->string('section_parcelle', 12)->nullable();
            $table->decimal('surface', 11, 2)->default(0);
            // Contacts
            $table->string('boite_postale', 32)->nullable();
            $table->string('telephone', 32)->nullable();
            $table->string('fax', 32)->nullable();
            $table->string('email', 128)->nullable();
            // Cycle de vie
            $table->date('date_debut_activite');
            $table->date('date_cessation')->nullable();
            $table->date('date_transfert')->nullable();
            $table->date('date_sommeil')->nullable();
            $table->string('statut', 12)->default('ACTIF');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
            $table->timestampTz('supprime_le')->nullable();
        });
        DB::statement("ALTER TABLE etablissement ADD CONSTRAINT ck_etab_type CHECK (type_etablissement IN ('PRINCIPAL','SECONDAIRE'))");
        DB::statement("ALTER TABLE etablissement ADD CONSTRAINT ck_etab_statut CHECK (statut IN ('ACTIF','CESSE','TRANSFERE','SOMMEIL'))");
        DB::statement('CREATE INDEX ix_etab_contrib ON etablissement(contribuable_id)');
        DB::statement('CREATE INDEX ix_etab_coll ON etablissement(collectivite_id)');
        DB::statement('CREATE INDEX ix_etab_activite ON etablissement(activite_id)');
        DB::statement('CREATE TRIGGER tg_etablissement BEFORE UPDATE ON etablissement FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissement');
        Schema::dropIfExists('activite');
        Schema::dropIfExists('categorie_activite');
        Schema::dropIfExists('secteur_activite');
    }
};
