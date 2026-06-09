<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE EXTENSION IF NOT EXISTS pgcrypto;
            CREATE EXTENSION IF NOT EXISTS unaccent;

            CREATE OR REPLACE FUNCTION trg_set_updated_at() RETURNS trigger AS \$func\$
            BEGIN
              NEW.updated_at := now();
              RETURN NEW;
            END; \$func\$ LANGUAGE plpgsql;
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS trg_set_updated_at() CASCADE;');
    }
};
