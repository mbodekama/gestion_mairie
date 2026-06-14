<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_personne', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('libelle', 64);
        });

        Schema::create('statut_contribuable', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->string('libelle', 64);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statut_contribuable');
        Schema::dropIfExists('type_personne');
    }
};
