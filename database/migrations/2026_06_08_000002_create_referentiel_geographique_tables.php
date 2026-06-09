<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->char('code_iso2', 2)->nullable();
            $table->char('code_iso3', 3)->nullable();
            $table->boolean('actif')->default(true);
        });

        Schema::create('nationalite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->foreignId('pays_id')->nullable()->constrained('pays');
        });

        Schema::create('district', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
        });

        Schema::create('region', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique();
            $table->string('libelle');
            $table->foreignId('district_id')->constrained('district');
        });

        Schema::create('departement', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->foreignId('region_id')->constrained('region');
        });

        Schema::create('sous_prefecture', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->foreignId('departement_id')->constrained('departement');
        });

        Schema::create('commune', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle');
            $table->foreignId('sous_prefecture_id')->nullable()->constrained('sous_prefecture');
            $table->integer('population')->nullable();
        });

        Schema::create('quartier', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('libelle');
            $table->foreignId('commune_id')->constrained('commune');
        });

        Schema::create('voie', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('libelle');
            $table->string('type_voie', 12);
            $table->foreignId('quartier_id')->nullable()->constrained('quartier');
        });
        DB::statement("ALTER TABLE voie ADD CONSTRAINT ck_voie_type_voie CHECK (type_voie IN ('RUE','AVENUE','BOULEVARD','AUTRE'))");

        Schema::create('zone_fiscale', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique();
            $table->string('libelle');
            $table->foreignId('commune_id')->nullable()->constrained('commune');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_fiscale');
        Schema::dropIfExists('voie');
        Schema::dropIfExists('quartier');
        Schema::dropIfExists('commune');
        Schema::dropIfExists('sous_prefecture');
        Schema::dropIfExists('departement');
        Schema::dropIfExists('region');
        Schema::dropIfExists('district');
        Schema::dropIfExists('nationalite');
        Schema::dropIfExists('pays');
    }
};
