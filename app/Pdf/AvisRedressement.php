<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\ControleFiscal;
use App\Models\Redressement;

/**
 * Avis de redressement : notification au contribuable des droits et pénalités
 * issus d'un contrôle fiscal. Couleur d'accent : rouge.
 */
class AvisRedressement extends BasePdf
{
    public function __construct(
        private readonly Redressement $redressement,
        private readonly ?ControleFiscal $controle,
        private readonly mixed $contribuable,
        ?Collectivite $collectivite,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => 'Service du contrôle fiscal',
            'reference' => $redressement->numero,
        ]), autoPageBreak: true, autoPageBreakMargin: self::FOOTER_H + 2);
    }

    public function build(): static
    {
        $r = $this->redressement;
        $controle = $this->controle;
        $etab = $controle?->etablissement;

        $this->drawHeader('RÉFÉRENCE', $r->numero);
        $this->drawBanner('AVIS DE REDRESSEMENT', 'NOTIFICATION AU CONTRIBUABLE');
        $this->drawDate();

        $this->SetXY(self::ML, 58);
        $this->drawParties(
            [
                ['Contribuable', self::nomContribuable($this->contribuable)],
                ['N° identifiant', $this->contribuable->numero_identifiant ?? '—'],
                ['Établissement', trim(($etab?->numero ?? '—').' '.($etab?->denomination ? '— '.$etab->denomination : ''))],
            ],
            [
                ['Date', $r->date_redressement?->format('d/m/Y') ?? now()->format('d/m/Y')],
                ["Contrôle d'origine", $controle?->numero ?? '—'],
            ],
        );

        $this->drawParagraphe(sprintf(
            'À la suite du contrôle fiscal portant sur la période du %s au %s, des insuffisances '.
            'ont été constatées. En conséquence, il est procédé au redressement suivant.',
            $controle?->periode_debut?->format('d/m/Y') ?? '—',
            $controle?->periode_fin?->format('d/m/Y') ?? '—',
        ));

        if ($controle && $controle->constats->isNotEmpty()) {
            $this->drawSectionTitle('Constats du contrôle');
            $this->drawConstats($controle);
        }

        if ($r->emissionsTaxe->isNotEmpty()) {
            $this->drawSectionTitle('Émissions complémentaires émises');
            $this->drawEmissions($r);
        }

        $this->drawSectionTitle('Récapitulatif');
        $this->drawRecap($r);

        $this->SetY($this->GetY() + 4);
        $this->drawSignature('Le Receveur');

        return $this;
    }

    private function drawConstats(ControleFiscal $controle): void
    {
        $accent = $this->hex($this->accent());

        $table = new \easyTable($this, '{60,40,40,40}',
            'width:180; border:1; border-color:#e2e8f0; split-row:1; font-family:Helvetica; font-size:9;');

        $table->rowStyle('bgcolor:'.$accent.'; font-color:#ffffff; font-style:B; font-size:8.5; min-height:9; align:{LRRR};');
        foreach (['Nature de taxe', 'Déclaré', 'Vérifié', 'Écart'] as $h) {
            $table->easyCell($this->e($h));
        }
        $table->printRow(true);

        foreach ($controle->constats as $i => $c) {
            $bg = ($i % 2 === 0) ? 'bgcolor:#f8fafc;' : 'bgcolor:#ffffff;';
            $table->rowStyle($bg.'min-height:8; align:{LRRR};');
            $table->easyCell($this->e($c->natureTaxe?->libelle_court ?? $c->natureTaxe?->libelle ?? '—'));
            $table->easyCell($this->e($this->fcfa($c->montant_declare)));
            $table->easyCell($this->e($this->fcfa($c->montant_verifie)));
            $table->easyCell($this->e($this->fcfa($c->ecart)));
            $table->printRow();
        }
        $table->endTable(2);
    }

    private function drawEmissions(Redressement $r): void
    {
        $accent = $this->hex($this->accent());

        $table = new \easyTable($this, '{45,55,30,50}',
            'width:180; border:1; border-color:#e2e8f0; split-row:1; font-family:Helvetica; font-size:9;');

        $table->rowStyle('bgcolor:'.$accent.'; font-color:#ffffff; font-style:B; font-size:8.5; min-height:9; align:{LLLR};');
        foreach (['N° Émission', 'Nature', 'Exercice', 'Montant'] as $h) {
            $table->easyCell($this->e($h));
        }
        $table->printRow(true);

        foreach ($r->emissionsTaxe as $i => $em) {
            $bg = ($i % 2 === 0) ? 'bgcolor:#f8fafc;' : 'bgcolor:#ffffff;';
            $table->rowStyle($bg.'min-height:8; align:{LLLR};');
            $table->easyCell($this->e($em->numero_emission));
            $table->easyCell($this->e($em->natureTaxe?->libelle_court ?? $em->natureTaxe?->libelle ?? '—'));
            $table->easyCell($this->e((string) ($em->exerciceFiscal?->annee ?? '—')));
            $table->easyCell($this->e($this->fcfa($em->montant_annuel)));
            $table->printRow();
        }
        $table->endTable(2);
    }

    private function drawRecap(Redressement $r): void
    {
        $accent = $this->hex($this->accent());
        $labelStyle = 'font-color:#94a3b8; font-style:B; font-size:8.5; paddingY:2; align:L;';
        $valStyle = 'font-style:B; font-size:10; paddingY:2; align:R;';

        // Tableau aligné à droite (moitié de la largeur utile)
        $table = new \easyTable($this, '{50,40}',
            'width:90; font-family:Helvetica; border:0;');
        $this->SetX(self::ML + self::UW - 90);

        $table->rowStyle('');
        $table->easyCell($this->e('Droits'), $labelStyle);
        $table->easyCell($this->e($this->fcfa($r->montant_droits)), $valStyle);
        $table->printRow();

        $this->SetX(self::ML + self::UW - 90);
        $table->rowStyle('');
        $table->easyCell($this->e('Pénalités'), $labelStyle);
        $table->easyCell($this->e($this->fcfa($r->montant_penalites)), $valStyle);
        $table->printRow();

        $this->SetX(self::ML + self::UW - 90);
        $table->rowStyle('bgcolor:#f8f9fa; min-height:11;');
        $table->easyCell($this->e('TOTAL À RÉGLER'), 'font-style:B; font-size:10; font-color:'.$accent.'; paddingY:2; align:L; border:T; border-color:'.$accent.';');
        $table->easyCell($this->e($this->fcfa($r->montant_total)), 'font-style:B; font-size:12; font-color:'.$accent.'; paddingY:2; align:R; border:T; border-color:'.$accent.';');
        $table->printRow();

        $table->endTable(2);
    }
}
