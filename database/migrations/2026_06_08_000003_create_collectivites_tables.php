<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_collectivite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
        });

        Schema::create('recette', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
            $table->foreignId('departement_id')->nullable()->constrained('departement');
            $table->string('boite_postale', 64)->nullable();
            $table->string('telephone', 64)->nullable();
        });

        Schema::create('collectivite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
            $table->foreignId('type_collectivite_id')->constrained('type_collectivite');
            $table->foreignId('recette_id')->nullable()->constrained('recette');
            $table->foreignId('district_id')->nullable()->constrained('district');
            $table->foreignId('region_id')->nullable()->constrained('region');
            $table->foreignId('departement_id')->nullable()->constrained('departement');
            $table->foreignId('commune_id')->nullable()->constrained('commune');
            $table->string('adresse')->nullable();
            $table->string('boite_postale', 32)->nullable();
            $table->string('telephone1', 32)->nullable();
            $table->string('telephone2', 32)->nullable();
            $table->string('cellulaire1', 32)->nullable();
            $table->string('cellulaire2', 32)->nullable();
            $table->string('fax', 32)->nullable();
            $table->string('email', 128)->nullable();
            $table->string('logo_uri', 512)->nullable();
            $table->boolean('active')->default(true);
            $table->timestampsTz();
        });
        DB::statement('CREATE TRIGGER tg_collectivite BEFORE UPDATE ON collectivite FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        Schema::create('departement_service', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
            $table->string('sigle', 64)->nullable();
            $table->foreignId('type_collectivite_id')->nullable()->constrained('type_collectivite');
        });

        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->string('libelle', 128);
            $table->string('sigle', 64)->nullable();
            $table->foreignId('collectivite_id')->nullable()->constrained('collectivite');
            $table->foreignId('departement_service_id')->nullable()->constrained('departement_service');
        });

        Schema::create('organisation', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->foreignId('type_collectivite_id')->nullable()->constrained('type_collectivite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation');
        Schema::dropIfExists('service');
        Schema::dropIfExists('departement_service');
        Schema::dropIfExists('collectivite');
        Schema::dropIfExists('recette');
        Schema::dropIfExists('type_collectivite');
    }
};
