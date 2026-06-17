<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\ExerciceFiscal;
use Illuminate\Support\Collection;

/**
 * État de restitution des exonérations : montants exonérés par exercice et
 * nature de taxe, avec le détail des émissions concernées. Accent : ardoise.
 */
class EtatExonerations extends BasePdf
{
    public function __construct(
        private readonly Collection $parExercice,
        private readonly string $totalGeneral,
        private readonly ?ExerciceFiscal $exercice,
        ?Collectivite $collectivite,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => 'Service du pilotage fiscal',
            'reference' => 'EXO-'.($exercice?->annee ?? 'ALL'),
        ]), autoPageBreak: true, autoPageBreakMargin: self::FOOTER_H + 2);
    }

    public function build(): static
    {
        $sousTitre = ($this->exercice ? 'Exercice '.$this->exercice->annee : 'Tous exercices');

        $this->drawHeader('ÉTAT N°', $this->exercice?->annee ? (string) $this->exercice->annee : 'TOUS');
        $this->drawBanner('ÉTAT DE RESTITUTION DES EXONÉRATIONS', strtoupper($sousTitre));
        $this->drawDate();

        $this->SetY(58);

        if ($this->parExercice->isEmpty()) {
            $this->SetFont('Helvetica', 'I', 11);
            $this->tc(self::MUTED);
            $this->SetXY(self::ML, 90);
            $this->Cell(self::UW, 8, $this->e('Aucune exonération appliquée sur la période sélectionnée.'), 0, 0, 'C');

            return $this;
        }

        foreach ($this->parExercice as $annee => $emissions) {
            $this->drawExercice((string) $annee, $emissions);
        }

        $this->drawTotalGeneral();

        return $this;
    }

    private function drawExercice(string $annee, Collection $emissions): void
    {
        $accent = $this->hex($this->accent());

        $totalExercice = $emissions->reduce(fn ($c, $e) => bcadd($c, (string) $e->montant_exonere, 2), '0');
        $parNature = $emissions->groupBy(fn ($e) => $e->natureTaxe?->libelle_court ?? $e->natureTaxe?->libelle ?? '—');

        $this->drawSectionTitle('Exercice '.$annee.' — '.$emissions->count().' émission(s)');

        $table = new \easyTable($this, '{60,35,55,30}',
            'width:180; border:1; border-color:#e2e8f0; split-row:1; font-family:Helvetica; font-size:8.5;');

        $table->rowStyle('bgcolor:'.$accent.'; font-color:#ffffff; font-style:B; font-size:8; min-height:8; align:{LLLR};');
        foreach (['Contribuable', 'Établissement', 'Exonération', 'Montant exonéré'] as $h) {
            $table->easyCell($this->e($h));
        }
        $table->printRow(true);

        foreach ($parNature as $nature => $lignes) {
            // Ligne de regroupement par nature de taxe
            $table->rowStyle('bgcolor:#f1f3f5; font-style:B; font-size:8.5; min-height:8; align:{LLLR};');
            $table->easyCell($this->e($nature), 'colspan:3;');
            $table->easyCell($this->e($this->fcfa($lignes->sum('montant_exonere'))));
            $table->printRow();

            foreach ($lignes as $e) {
                $exo = $e->exoneration?->numero ?? '';
                if ($e->exoneration?->reference_decret) {
                    $exo .= ' — '.$e->exoneration->reference_decret;
                }
                $table->rowStyle('min-height:7; align:{LLLR};');
                $table->easyCell($this->e(self::nomContribuable($e->etablissement?->contribuable)));
                $table->easyCell($this->e($e->etablissement?->numero ?? '—'));
                $table->easyCell($this->e($exo));
                $table->easyCell($this->e($this->fcfa($e->montant_exonere)));
                $table->printRow();
            }
        }

        $table->rowStyle('bgcolor:#eef0f2; font-style:B; font-size:9; min-height:9; align:{LLLR};');
        $table->easyCell($this->e('Sous-total exercice '.$annee), 'colspan:3;');
        $table->easyCell($this->e($this->fcfa($totalExercice)));
        $table->printRow();

        $table->endTable(3);
    }

    private function drawTotalGeneral(): void
    {
        $accent = $this->accent();
        $y = $this->GetY() + 4;

        $this->SetLineWidth(0.6);
        $this->dc($accent);
        $this->Line(self::ML, $y, self::ML + self::UW, $y);

        $this->SetFont('Helvetica', 'B', 13);
        $this->tc($accent);
        $this->SetXY(self::ML, $y + 3);
        $this->Cell(self::UW, 7, $this->e('TOTAL GÉNÉRAL EXONÉRÉ : '.$this->fcfa($this->totalGeneral)), 0, 0, 'R');
    }
}
