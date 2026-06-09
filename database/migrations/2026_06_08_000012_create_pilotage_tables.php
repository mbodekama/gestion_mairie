<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objectif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->smallInteger('annee');
            $table->decimal('montant', 18, 2)->default(0);
            $table->decimal('montant_revise', 18, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
            $table->unique(['collectivite_id', 'annee']);
        });

        Schema::create('parametre_application', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collectivite_id')->nullable()->constrained('collectivite');
            $table->string('cle', 64);
            $table->text('valeur')->nullable();
            $table->string('description')->nullable();
            $table->unique(['collectivite_id', 'cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametre_application');
        Schema::dropIfExists('objectif');
    }
};
