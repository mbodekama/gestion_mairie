<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etablissement', function (Blueprint $table) {
            // Dernier chiffre d'affaires connu de l'établissement : valeur indicative
            // servant de défaut à la saisie du CA d'une émission. L'assiette
            // imposable définitive reste portée par l'émission, par exercice.
            $table->decimal('ca_reference', 18, 2)->nullable()->after('surface');
        });
    }

    public function down(): void
    {
        Schema::table('etablissement', function (Blueprint $table) {
            $table->dropColumn('ca_reference');
        });
    }
};
