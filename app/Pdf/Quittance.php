<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\ReglementTaxe;
use Illuminate\Support\Collection;

/**
 * Quittance de paiement (reçu). Une quittance peut regrouper plusieurs
 * règlements partageant le même numéro de quittance. Couleur d'accent : mauve.
 */
class Quittance extends BasePdf
{
    public function __construct(
        private readonly ReglementTaxe $recouvrement,
        private readonly Collection $reglements,
        private readonly mixed $contribuable,
        ?Collectivite $collectivite,
        private readonly string $total,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => 'Service du recouvrement',
            'reference' => $recouvrement->numero_quittance ?? $recouvrement->numero_reglement,
        ]));
    }

    public function build(): static
    {
        $premier = $this->reglements->first();
        $numero = $this->recouvrement->numero_quittance ?? $this->recouvrement->numero_reglement;

        $this->drawHeader('QUITTANCE N°', $numero);
        $this->drawBanner('QUITTANCE DE PAIEMENT', 'REÇU DE RÈGLEMENT');
        $this->drawDate();

        $this->SetXY(self::ML, 58);
        $this->drawParties(
            [
                ['Reçu de', self::nomContribuable($this->contribuable)],
                ['N° identifiant', $this->contribuable->numero_identifiant ?? '—'],
            ],
            [
                ['Date du règlement', optional($premier?->date_reglement)->format('d/m/Y') ?? now()->format('d/m/Y')],
            ],
        );

        $this->drawSectionTitle('Règlements perçus');
        $this->drawReglements();

        $this->drawSectionTitle('Modalités de règlement');
        $this->drawModalites($premier);

        $this->SetY($this->GetY() + 6);
        $this->drawSignature('Le Receveur');

        return $this;
    }

    private function drawReglements(): void
    {
        $accent = $this->hex($this->accent());

        $table = new \easyTable($this, '{45,45,50,40}',
            'width:180; border:1; border-color:#e2e8f0; split-row:1; font-family:Helvetica; font-size:9;');

        $table->rowStyle('bgcolor:'.$accent.'; font-color:#ffffff; font-style:B; font-size:8.5; min-height:9; align:{LLLR};');
        foreach (['N° Règlement', 'Émission', 'Nature', 'Montant'] as $h) {
            $table->easyCell($this->e($h));
        }
        $table->printRow(true);

        foreach ($this->reglements as $i => $r) {
            $bg = ($i % 2 === 0) ? 'bgcolor:#f8fafc;' : 'bgcolor:#ffffff;';
            $table->rowStyle($bg.'min-height:8; align:{LLLR};');
            $table->easyCell($this->e($r->numero_reglement));
            $table->easyCell($this->e($r->emissionTaxe?->numero_emission ?? '—'));
            $table->easyCell($this->e($r->emissionTaxe?->natureTaxe?->libelle_court ?? $r->emissionTaxe?->natureTaxe?->libelle ?? '—'));
            $table->easyCell($this->e($this->fcfa($r->montant_impute)));
            $table->printRow();
        }

        $table->rowStyle('bgcolor:#f8f9fa; font-style:B; font-size:11; min-height:11; align:{LLLR};');
        $table->easyCell($this->e('TOTAL PERÇU'), 'colspan:3;');
        $table->easyCell($this->e($this->fcfa($this->total)), 'font-color:'.$accent.';');
        $table->printRow();

        $table->endTable(2);
    }

    private function drawModalites(?ReglementTaxe $premier): void
    {
        $rows = [
            ['Mode de règlement', $premier?->modeReglement?->libelle ?? '—'],
            ['Type de règlement', $premier?->typeReglement?->libelle ?? '—'],
        ];

        if ($premier?->banque_id || $premier?->numero_cheque) {
            $rows[] = ['Banque / N° chèque', trim(($premier?->banque?->libelle ?? '—').' '.($premier?->numero_cheque ? '— '.$premier->numero_cheque : ''))];
        }
        if ($premier?->operateur_mobile || $premier?->reference_transaction) {
            $rows[] = ['Mobile Money', trim(($premier?->operateur_mobile ?? '—').' '.($premier?->reference_transaction ? '— Réf. '.$premier->reference_transaction : ''))];
        }
        $rows[] = ['Recette', $premier?->recette?->libelle ?? '—'];

        $labelStyle = 'bgcolor:#f8fafc; font-color:#94a3b8; font-style:B; font-size:8; paddingY:2; border:B; border-color:#e2e8f0;';
        $valStyle = 'font-style:B; font-size:9.5; paddingY:2; border:B; border-color:#e2e8f0;';

        $table = new \easyTable($this, '%{30,70}',
            'width:180; font-family:Helvetica; border:0;');
        foreach ($rows as $row) {
            $table->rowStyle('min-height:11;');
            $table->easyCell($this->e(strtoupper($row[0])), $labelStyle);
            $table->easyCell($this->e($row[1]), $valStyle);
            $table->printRow();
        }
        $table->endTable(2);
    }
}
