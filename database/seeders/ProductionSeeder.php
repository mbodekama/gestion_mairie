<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orchestrateur d'EXPLOITATION (production / exploitation 0).
 *
 * Charge uniquement les référentiels et données indispensables au fonctionnement
 * de l'application — JAMAIS de données de démonstration. C'est la porte d'entrée
 * à utiliser lors d'un déploiement réel :
 *
 *     php artisan db:seed --class=ProductionSeeder
 *
 * DatabaseSeeder (le seeder par défaut) appelle ce seeder puis y ajoute la démo.
 * La liste des seeders essentiels n'est donc maintenue qu'ICI.
 */
class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ReferentielSqlSeeder::class,
            NationaliteSqlSeeder::class,
            BaremeSqlSeeder::class,
            ZoneFiscaleSeeder::class,
            RolePermissionSeeder::class,
            ControleWorkflowSeeder::class,
            ReferentielContribuableSeeder::class,
            CollectiviteSeeder::class,
            ServiceSeeder::class,
            TypeExonerationSeeder::class,
            DocTypeSeeder::class,
        ]);
    }
}
