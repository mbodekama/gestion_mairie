<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder par défaut (dev / test / démo) : tous les référentiels essentiels
 * (délégués à ProductionSeeder) PLUS les données de démonstration.
 *
 * Pour un déploiement réel sans démo, lancer plutôt :
 *     php artisan db:seed --class=ProductionSeeder
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Référentiels & données indispensables (source unique : ProductionSeeder).
        $this->call(ProductionSeeder::class);

        // ── Données de démonstration (ne pas charger en exploitation 0) ──
        // Ordre = chaîne fiscale : contribuable → exercice → établissement → émission/règlement.
        $this->call([
            \Database\Seeders\Demo\ContribuableSeeder::class,
            \Database\Seeders\Demo\ExerciceFiscalSeeder::class,
            \Database\Seeders\Demo\EtablissementSeeder::class,
            \Database\Seeders\Demo\EmissionTaxeSeeder::class,
            \Database\Seeders\Demo\ObjectifSeeder::class,
        ]);
    }
}
