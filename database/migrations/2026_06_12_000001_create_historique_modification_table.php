<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_modification', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 100)->index();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('evenement', 20)->default('MODIFICATION');
            // Opérateur dénormalisé pour survivre à la suppression de l'utilisateur
            $table->unsignedBigInteger('utilisateur_id')->nullable()->index();
            $table->string('utilisateur_nom', 150)->nullable();
            $table->jsonb('donnees_avant')->nullable();
            $table->jsonb('donnees_apres')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_type', 'model_id']);
        });

        DB::statement("ALTER TABLE historique_modification ADD CONSTRAINT chk_hm_evenement CHECK (evenement IN ('CREATION','MODIFICATION','SUPPRESSION'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_modification');
    }
};
