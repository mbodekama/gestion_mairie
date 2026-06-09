<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mode_reglement', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 64);
        });

        Schema::create('type_reglement', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 64);
        });

        // dossier_id est nullable, la FK est ajoutée après création de dossier (migration 010)
        Schema::create('emission_taxe', function (Blueprint $table) {
            $table->id();
            $table->string('numero_emission', 15)->unique();
            $table->string('numero_fiche', 15)->nullable();
            $table->string('numero_article', 18);
            $table->foreignId('etablissement_id')->constrained('etablissement');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->unsignedBigInteger('dossier_id')->nullable();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('periodicite_id')->constrained('periodicite');
            $table->foreignId('exercice_fiscal_id')->constrained('exercice_fiscal');
            $table->decimal('ca_annuel', 18, 2)->nullable();
            $table->decimal('montant_annuel', 15, 2)->default(0);
            $table->decimal('montant_periode', 15, 2)->default(0);
            $table->smallInteger('nb_mois_prorata')->nullable();
            $table->decimal('montant_prorata', 15, 2)->default(0);
            $table->date('date_declaration')->nullable();
            $table->date('date_liquidation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
        });
        DB::statement('CREATE INDEX ix_emis_etab ON emission_taxe(etablissement_id)');
        DB::statement('CREATE INDEX ix_emis_exer ON emission_taxe(exercice_fiscal_id)');
        DB::statement('CREATE TRIGGER tg_emission_taxe BEFORE UPDATE ON emission_taxe FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        Schema::create('emission_cotisation_fonciere', function (Blueprint $table) {
            $table->id();
            $table->string('numero_fiche', 15)->nullable();
            $table->string('numero_article', 18);
            $table->foreignId('etablissement_id')->constrained('etablissement');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->unsignedBigInteger('dossier_id')->nullable();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->foreignId('bareme_cotisation_id')->nullable()->constrained('bareme_cotisation_fonciere');
            $table->foreignId('periodicite_id')->constrained('periodicite');
            $table->foreignId('exercice_fiscal_id')->constrained('exercice_fiscal');
            $table->decimal('ca_annuel', 18, 2)->nullable();
            $table->decimal('montant', 15, 2)->nullable();
            $table->decimal('montant_periode', 15, 2)->default(0);
            $table->smallInteger('nb_mois_prorata')->nullable();
            $table->decimal('montant_prorata', 15, 2)->nullable();
            $table->date('date_declaration')->nullable();
            $table->date('date_liquidation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });

        Schema::create('reglement_taxe', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reglement', 12)->unique();
            $table->foreignId('emission_taxe_id')->nullable()->constrained('emission_taxe');
            $table->foreignId('emission_cotisation_id')->nullable()->constrained('emission_cotisation_fonciere');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('recette_id')->constrained('recette');
            $table->foreignId('exercice_fiscal_id')->constrained('exercice_fiscal');
            $table->date('date_reglement')->nullable();
            $table->decimal('montant', 15, 2)->default(0);
            $table->decimal('montant_impute', 15, 2)->default(0);
            $table->foreignId('mode_reglement_id')->constrained('mode_reglement');
            $table->foreignId('type_reglement_id')->constrained('type_reglement');
            $table->string('numero_cheque', 64)->nullable();
            $table->foreignId('banque_id')->nullable()->constrained('banque');
            $table->string('numero_quittance', 64)->nullable();
            $table->smallInteger('mois_impute')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });
        // Un règlement cible une émission de taxe OU une cotisation foncière, jamais les deux
        DB::statement("ALTER TABLE reglement_taxe ADD CONSTRAINT ck_regl_cible CHECK ((emission_taxe_id IS NOT NULL) <> (emission_cotisation_id IS NOT NULL))");
        DB::statement('CREATE INDEX ix_regl_emis ON reglement_taxe(emission_taxe_id)');
        DB::statement('CREATE INDEX ix_regl_date ON reglement_taxe(date_reglement)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reglement_taxe');
        Schema::dropIfExists('emission_cotisation_fonciere');
        Schema::dropIfExists('emission_taxe');
        Schema::dropIfExists('type_reglement');
        Schema::dropIfExists('mode_reglement');
    }
};
