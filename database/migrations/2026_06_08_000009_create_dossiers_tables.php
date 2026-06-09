<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('famille_etat_dossier', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('libelle', 128);
        });

        Schema::create('categorie_etat_dossier', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('libelle', 128);
            $table->foreignId('famille_etat_dossier_id')->nullable()->constrained('famille_etat_dossier');
        });

        Schema::create('dossier', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 8)->unique();
            $table->foreignId('etablissement_id')->constrained('etablissement');
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->date('date_creation')->nullable();
            $table->string('motif_entree')->nullable();
            $table->date('date_retour')->nullable();
            $table->date('date_sortie')->nullable();
            $table->string('motif_sortie')->nullable();
            $table->foreignId('agent_retrait_id')->nullable()->constrained('agent');
            $table->foreignId('service_origine_id')->nullable()->constrained('service');
            $table->foreignId('service_destination_id')->nullable()->constrained('service');
            $table->foreignId('famille_etat_dossier_id')->nullable()->constrained('famille_etat_dossier');
            $table->foreignId('categorie_etat_dossier_id')->nullable()->constrained('categorie_etat_dossier');
            $table->boolean('archive')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampsTz();
        });
        DB::statement('CREATE TRIGGER tg_dossier BEFORE UPDATE ON dossier FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');

        Schema::create('historique_dossier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossier')->cascadeOnDelete();
            $table->date('date_mouvement')->default(DB::raw('CURRENT_DATE'));
            $table->string('motif')->nullable();
            $table->foreignId('service_origine_id')->nullable()->constrained('service');
            $table->foreignId('service_destination_id')->nullable()->constrained('service');
            $table->foreignId('agent_id')->nullable()->constrained('agent');
            $table->boolean('archive')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_dossier');
        Schema::dropIfExists('dossier');
        Schema::dropIfExists('categorie_etat_dossier');
        Schema::dropIfExists('famille_etat_dossier');
    }
};
