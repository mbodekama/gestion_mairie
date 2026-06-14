<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reglement_taxe', function (Blueprint $table) {
            // Paiement par Mobile Money : opérateur et référence de la transaction.
            $table->string('operateur_mobile', 64)->nullable()->after('banque_id');
            $table->string('reference_transaction', 64)->nullable()->after('operateur_mobile');
        });
    }

    public function down(): void
    {
        Schema::table('reglement_taxe', function (Blueprint $table) {
            $table->dropColumn(['operateur_mobile', 'reference_transaction']);
        });
    }
};
