<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_connexion', function (Blueprint $table) {
            $table->string('type_evenement', 20)->default('CONNEXION')->after('succes');
        });

        DB::statement("ALTER TABLE journal_connexion ADD CONSTRAINT ck_journal_type_evenement
            CHECK (type_evenement IN ('CONNEXION','DECONNEXION','VERROUILLAGE','DEVERROUILLAGE'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE journal_connexion DROP CONSTRAINT IF EXISTS ck_journal_type_evenement');

        Schema::table('journal_connexion', function (Blueprint $table) {
            $table->dropColumn('type_evenement');
        });
    }
};
