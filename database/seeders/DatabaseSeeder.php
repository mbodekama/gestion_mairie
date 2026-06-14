<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ReferentielSqlSeeder::class,
            BaremeSqlSeeder::class,
            ZoneFiscaleSeeder::class,
            RolePermissionSeeder::class,
            ReferentielContribuableSeeder::class,
            ContribuableSeeder::class,
            TypeExonerationSeeder::class,
            DocTypeSeeder::class,
        ]);
    }
}
