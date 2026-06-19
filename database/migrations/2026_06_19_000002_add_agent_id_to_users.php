<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Rattache un compte applicatif (users) à un agent. Optionnel : tous les
// comptes ne correspondent pas à un agent (comptes techniques/admin). La
// suppression d'un agent détache ses comptes (nullOnDelete) sans les supprimer.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->after('id')
                ->constrained('agent')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });
    }
};
