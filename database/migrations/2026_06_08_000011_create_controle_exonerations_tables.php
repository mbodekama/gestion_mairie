<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convocation', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->foreignId('etablissement_id')->constrained('etablissement');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('service_id')->constrained('service');
            $table->foreignId('agent_id')->constrained('agent');
            $table->smallInteger('annee');
            $table->string('motif')->nullable();
            $table->date('date_convocation')->nullable();
            $table->integer('delai_reponse')->nullable();
            $table->date('date_limite')->nullable();
            $table->date('date_reponse')->nullable();
            $table->time('heure_reponse')->nullable();
            $table->date('periode_due_debut')->nullable();
            $table->date('periode_due_fin')->nullable();
            $table->integer('nb_mois_du')->nullable();
            $table->integer('nb_jours_du')->nullable();
            $table->decimal('montant_du', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });

        Schema::create('sanction_fiscale', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique();
            $table->string('libelle', 128);
        });

        Schema::create('type_exoneration', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('libelle', 128);
        });

        Schema::create('exoneration', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 32)->unique();
            $table->foreignId('contribuable_id')->constrained('contribuable');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->foreignId('type_exoneration_id')->constrained('type_exoneration');
            $table->string('reference_decret', 32)->nullable();
            $table->date('date_decret')->nullable();
            $table->string('zone', 2)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });

        Schema::create('ligne_exoneration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exoneration_id')->constrained('exoneration')->cascadeOnDelete();
            $table->foreignId('nature_taxe_id')->constrained('nature_taxe');
            $table->smallInteger('annee_application');
            $table->decimal('taux', 5, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_exoneration');
        Schema::dropIfExists('exoneration');
        Schema::dropIfExists('type_exoneration');
        Schema::dropIfExists('sanction_fiscale');
        Schema::dropIfExists('convocation');
    }
};
