<?php

namespace Database\Seeders;

use App\Models\DocType;
use Illuminate\Database\Seeder;

class DocTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ── Contribuable (PP & PM) ─────────────────────────────────────
            ['code' => 'CNT_CNI',      'libelle' => 'Carte Nationale d\'Identité',         'model' => \App\Models\Contribuable::class, 'ordre' => 1,  'ext' => 'pdf,jpg,jpeg,png'],
            ['code' => 'CNT_PASSEPORT','libelle' => 'Passeport',                            'model' => \App\Models\Contribuable::class, 'ordre' => 2,  'ext' => 'pdf,jpg,jpeg,png'],
            ['code' => 'CNT_EXTRAIT',  'libelle' => 'Extrait de naissance',                 'model' => \App\Models\Contribuable::class, 'ordre' => 3,  'ext' => 'pdf,jpg,jpeg,png'],
            ['code' => 'CNT_CONSULAIRE','libelle' => 'Carte consulaire',                    'model' => \App\Models\Contribuable::class, 'ordre' => 4,  'ext' => 'pdf,jpg,jpeg,png'],
            ['code' => 'CNT_SEJOUR',   'libelle' => 'Titre de séjour / Résident',           'model' => \App\Models\Contribuable::class, 'ordre' => 5,  'ext' => 'pdf,jpg,jpeg,png'],
            ['code' => 'CNT_RC',       'libelle' => 'Registre du Commerce',                 'model' => \App\Models\Contribuable::class, 'ordre' => 10, 'ext' => 'pdf'],
            ['code' => 'CNT_STATUTS',  'libelle' => 'Statuts de la société',                'model' => \App\Models\Contribuable::class, 'ordre' => 11, 'ext' => 'pdf,doc,docx'],
            ['code' => 'CNT_ATTEST_FISC','libelle' => 'Attestation fiscale',                'model' => \App\Models\Contribuable::class, 'ordre' => 12, 'ext' => 'pdf'],
            ['code' => 'CNT_AUTRE',    'libelle' => 'Autre document',                       'model' => \App\Models\Contribuable::class, 'ordre' => 99, 'ext' => null],

            // ── Établissement ──────────────────────────────────────────────
            ['code' => 'ETB_PATENTE',  'libelle' => 'Patente',                              'model' => \App\Models\Etablissement::class, 'ordre' => 1,  'ext' => 'pdf'],
            ['code' => 'ETB_BAIL',     'libelle' => 'Contrat de bail',                      'model' => \App\Models\Etablissement::class, 'ordre' => 2,  'ext' => 'pdf,doc,docx'],
            ['code' => 'ETB_PERMIS',   'libelle' => 'Permis d\'exploitation',               'model' => \App\Models\Etablissement::class, 'ordre' => 3,  'ext' => 'pdf'],
            ['code' => 'ETB_AUTRE',    'libelle' => 'Autre document',                       'model' => \App\Models\Etablissement::class, 'ordre' => 99, 'ext' => null],

            // ── Dossier ────────────────────────────────────────────────────
            ['code' => 'DOS_DECLARATION','libelle' => 'Déclaration fiscale',                'model' => \App\Models\Dossier::class, 'ordre' => 1, 'ext' => 'pdf,xls,xlsx'],
            ['code' => 'DOS_RELEVE',   'libelle' => 'Relevé de compte',                     'model' => \App\Models\Dossier::class, 'ordre' => 2, 'ext' => 'pdf,xls,xlsx'],
            ['code' => 'DOS_AUTRE',    'libelle' => 'Autre document',                       'model' => \App\Models\Dossier::class, 'ordre' => 99,'ext' => null],

            // ── Contrôle fiscal ────────────────────────────────────────────
            ['code' => 'CTF_RAPPORT',  'libelle' => 'Rapport de contrôle',                  'model' => \App\Models\ControleFiscal::class, 'ordre' => 1, 'ext' => 'pdf,doc,docx'],
            ['code' => 'CTF_PV',       'libelle' => 'Procès-verbal',                        'model' => \App\Models\ControleFiscal::class, 'ordre' => 2, 'ext' => 'pdf,doc,docx'],
            ['code' => 'CTF_AUTRE',    'libelle' => 'Autre document',                       'model' => \App\Models\ControleFiscal::class, 'ordre' => 99,'ext' => null],
        ];

        foreach ($types as $t) {
            DocType::updateOrCreate(
                ['code' => $t['code']],
                [
                    'libelle'               => $t['libelle'],
                    'model_type'            => $t['model'],
                    'ordre'                 => $t['ordre'],
                    'extensions_autorisees' => $t['ext'],
                    'obligatoire'           => false,
                ]
            );
        }
    }
}
