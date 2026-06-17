<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\EmissionTaxe;

/**
 * Avis d'imposition d'une émission : montant brut, exonération éventuelle
 * et montant net dû. Couleur d'accent : bleu.
 */
class AvisImposition extends BasePdf
{
    public function __construct(
        private readonly EmissionTaxe $emission,
        ?Collectivite $collectivite,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => 'Service des émissions / liquidation',
            'reference' => $emission->numero_emission,
        ]));
    }

    protected function accent(): array
    {
        return self::BLUE;
    }

    public function build(): static
    {
        $em = $this->emission;
        $contrib = $em->etablissement?->contribuable;

        // Montant net = prorata si renseigné, sinon annuel ; brut = net + exonéré
        $net = (float) $em->montant_prorata > 0 ? (string) $em->montant_prorata : (string) $em->montant_annuel;
        $exo = (string) $em->montant_exonere;
        $brut = bcadd($net, $exo, 2);

        $this->drawHeader('RÉFÉRENCE', $em->numero_emission);
        $this->drawBanner("AVIS D'IMPOSITION", 'ARTICLE '.$em->numero_article);
        $this->drawDate();

        $this->SetXY(self::ML, 58);
        $this->drawParties(
            [
                ['Contribuable', self::nomContribuable($contrib)],
                ['N° identifiant', $contrib->numero_identifiant ?? '—'],
                ['Établissement', trim(($em->etablissement?->numero ?? '—').' '.($em->etablissement?->denomination ? '— '.$em->etablissement->denomination : ''))],
            ],
            [
                ['Exercice', (string) ($em->exerciceFiscal?->annee ?? '—')],
                ['Date de liquidation', $em->date_liquidation?->format('d/m/Y') ?? now()->format('d/m/Y')],
            ],
        );

        $this->drawSectionTitle('Détail de la liquidation');
        $this->drawDetail($em, $brut, $exo, $net);

        if ((float) $em->montant_prorata > 0) {
            $this->SetFont('Helvetica', 'I', 8.5);
            $this->tc(self::MUTED);
            $this->SetXY(self::ML, $this->GetY() + 3);
            $this->Cell(self::UW, 4, $this->e('Montant calculé au prorata ('.$em->nb_mois_prorata.' mois).'), 0, 1, 'L');
        }

        $this->SetY($this->GetY() + 6);
        $this->drawSignature('Pour la collectivité');

        return $this;
    }

    private function drawDetail(EmissionTaxe $em, string $brut, string $exo, string $net): void
    {
        $accent = $this->hex($this->accent());

        $table = new \easyTable($this, '{90,40,50}',
            'width:180; border:1; border-color:#e2e8f0; font-family:Helvetica; font-size:9.5;');

        $hStyle = 'bgcolor:'.$accent.'; font-color:#ffffff; font-style:B; font-size:8.5; min-height:9;';
        $table->rowStyle($hStyle.'align:{LLR};');
        $table->easyCell($this->e('Nature de taxe'));
        $table->easyCell($this->e('Périodicité'));
        $table->easyCell($this->e('Montant brut'));
        $table->printRow(true);

        $table->rowStyle('min-height:9; align:{LLR};');
        $table->easyCell($this->e($em->natureTaxe?->libelle ?? $em->natureTaxe?->libelle_court ?? '—'));
        $table->easyCell($this->e($em->periodicite?->libelle ?? '—'));
        $table->easyCell($this->e($this->fcfa($brut)));
        $table->printRow();

        if ($em->exoneration_id && (float) $exo > 0) {
            $ref = 'Exonération '.($em->exoneration?->numero ?? '');
            if ($em->exoneration?->reference_decret) {
                $ref .= ' — réf. '.$em->exoneration->reference_decret;
            }
            $table->rowStyle('min-height:9; font-color:#198754; align:{LLR};');
            $table->easyCell($this->e($ref), 'colspan:2;');
            $table->easyCell($this->e('− '.$this->fcfa($exo)));
            $table->printRow();
        }

        $table->rowStyle('bgcolor:#f8f9fa; font-style:B; font-size:11; min-height:11; align:{LLR};');
        $table->easyCell($this->e('MONTANT NET À PAYER'), 'colspan:2;');
        $table->easyCell($this->e($this->fcfa($net)), 'font-color:'.$accent.';');
        $table->printRow();

        $table->endTable(2);
    }
}
