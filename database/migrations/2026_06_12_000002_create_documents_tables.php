<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Types de documents par modèle métier
        Schema::create('doc_type', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('libelle', 150);
            $table->string('model_type', 150)->index();   // ex. App\Models\Contribuable
            $table->boolean('obligatoire')->default(false);
            $table->string('extensions_autorisees', 200)->nullable(); // ex. pdf,jpg,png
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        // Pièces jointes rattachées à n'importe quel modèle
        Schema::create('document', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_type_id')->constrained('doc_type');
            $table->string('model_type', 150)->index();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('nom', 255);                   // libellé donné par l'opérateur
            $table->string('nom_original', 255);          // nom d'origine du fichier uploadé
            $table->string('chemin', 500);                // chemin relatif dans storage/app/private
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('taille')->nullable(); // octets
            $table->unsignedBigInteger('collectivite_id')->nullable()->index();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('uploaded_by_nom', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_type', 'model_id']);
        });

        DB::statement('ALTER TABLE document ADD CONSTRAINT chk_doc_taille CHECK (taille >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('document');
        Schema::dropIfExists('doc_type');
    }
};
