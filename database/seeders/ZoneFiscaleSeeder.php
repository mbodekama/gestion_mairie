<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Zones fiscales : découpage de chaque commune en secteurs de tarification de la
 * taxe foncière. Le barème foncier (`bareme_cotisation_fonciere`) porte un
 * `montant_zone1` et un `montant_zone2` → on génère deux zones par commune.
 *
 * `etablissement.zone_fiscale_id` étant NOT NULL, chaque établissement doit être
 * rattaché à une zone : ce seed garantit qu'une zone existe pour toute commune.
 *
 * Code unique global = code commune (3 car.) + numéro de zone (1 car.),
 * ex. Cocody (046) → 0461 / 0462. Idempotent (insertOrIgnore sur `code`).
 * Dépend des communes chargées par ReferentielSqlSeeder.
 */
class ZoneFiscaleSeeder extends Seeder
{
    /** Nombre de zones par commune, aligné sur montant_zone1 / montant_zone2 du barème foncier. */
    private const NB_ZONES = 2;

    public function run(): void
    {
        $communes = DB::table('commune')->select('id', 'code', 'libelle')->get();

        $zones = [];
        foreach ($communes as $commune) {
            for ($numero = 1; $numero <= self::NB_ZONES; $numero++) {
                $zones[] = [
                    'code'       => $commune->code . $numero,
                    'libelle'    => "{$commune->libelle} - Zone {$numero}",
                    'commune_id' => $commune->id,
                ];
            }
        }

        foreach (array_chunk($zones, 500) as $lot) {
            DB::table('zone_fiscale')->insertOrIgnore($lot);
        }

        $this->command?->info(count($zones) . ' zones fiscales préparées (' . self::NB_ZONES . ' par commune).');
    }
}
