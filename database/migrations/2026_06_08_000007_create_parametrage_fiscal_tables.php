<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domaine_taxe', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle', 128);
        });

        Schema::create('categorie_impot_taxe', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle')->nullable();
        });

        Schema::create('periodicite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle', 128);
            $table->smallInteger('nb_mois')->nullable();
        });

        Schema::create('nature_taxe', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle_court', 16)->nullable();
            $table->string('libelle')->nullable();
            $table->foreignId('domaine_taxe_id')->constrained('domaine_taxe');
            $table->foreignId('categorie_impot_taxe_id')->constrained('categorie_impot_taxe');
        });

        Schema::create('bareme_taxe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('categorie_activite_id')->nullable()->constrained('categorie_activite');
            $table->foreignId('periodicite_id')->constrained('periodicite');
            $table->decimal('ca_borne_inf', 18, 2)->default(0);
            $table->decimal('ca_borne_sup', 18, 2)->default(0);
            $table->decimal('taux', 10, 4)->default(0);
        });
        DB::statement('ALTER TABLE bareme_taxe ADD CONSTRAINT ck_bareme_bornes CHECK (ca_borne_sup = 0 OR ca_borne_sup >= ca_borne_inf)');

        Schema::create('exercice_fiscal', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('annee');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->boolean('cloture')->default(false);
            $table->unique(['annee', 'collectivite_id']);
        });

        Schema::create('categorie_cotisation_fonciere', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
        });

        Schema::create('bareme_cotisation_fonciere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('activite_id')->nullable()->constrained('activite');
            $table->foreignId('periodicite_id')->constrained('periodicite');
            $table->decimal('ca_borne_inf', 18, 2)->nullable()->default(0);
            $table->decimal('ca_borne_sup', 18, 2)->nullable()->default(0);
            $table->decimal('montant_zone1', 15, 2)->nullable()->default(0);
            $table->decimal('montant_zone2', 15, 2)->nullable()->default(0);
            $table->boolean('forfaitaire')->default(false);
        });

        Schema::create('obligation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_id')->constrained('contribuable')->cascadeOnDelete();
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('periodicite_id')->nullable()->constrained('periodicite');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obligation');
        Schema::dropIfExists('bareme_cotisation_fonciere');
        Schema::dropIfExists('categorie_cotisation_fonciere');
        Schema::dropIfExists('exercice_fiscal');
        Schema::dropIfExists('bareme_taxe');
        Schema::dropIfExists('nature_taxe');
        Schema::dropIfExists('periodicite');
        Schema::dropIfExists('categorie_impot_taxe');
        Schema::dropIfExists('domaine_taxe');
    }
};
