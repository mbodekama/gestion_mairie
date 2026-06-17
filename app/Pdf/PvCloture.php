<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\ControleFiscal;

/**
 * Procès-verbal de clôture d'un contrôle fiscal sans redressement.
 * Couleur d'accent : vert.
 */
class PvCloture extends BasePdf
{
    public function __construct(
        private readonly ControleFiscal $controle,
        private readonly mixed $contribuable,
        ?Collectivite $collectivite,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => 'Service du contrôle fiscal',
            'reference' => $controle->numero,
        ]), autoPageBreak: true, autoPageBreakMargin: self::FOOTER_H + 2);
    }

    public function build(): static
    {
        $controle = $this->controle;
        $etab = $controle->etablissement;

        $this->drawHeader('CONTRÔLE N°', $controle->numero);
        $this->drawBanner('PROCÈS-VERBAL DE CLÔTURE', 'CONTRÔLE CLÔTURÉ SANS REDRESSEMENT');
        $this->drawDate();

        $this->SetXY(self::ML, 58);
        $this->drawParties(
            [
                ['Contribuable', self::nomContribuable($this->contribuable)],
                ['N° identifiant', $this->contribuable->numero_identifiant ?? '—'],
                ['Établissement', trim(($etab?->numero ?? '—').' '.($etab?->denomination ? '— '.$etab->denomination : ''))],
            ],
            [
                ['Période contrôlée', ($controle->periode_debut?->format('d/m/Y') ?? '—').' → '.($controle->periode_fin?->format('d/m/Y') ?? '—')],
                ['Clôturé le', $controle->date_cloture?->format('d/m/Y') ?? now()->format('d/m/Y')],
            ],
        );

        $instructeur = trim(($controle->agentInstructeur?->nom ?? '').' '.($controle->agentInstructeur?->prenoms ?? '')) ?: 'le service';
        $this->drawParagraphe(sprintf(
            'À l\'issue des opérations de contrôle menées par %s, le présent procès-verbal constate la '.
            'clôture du contrôle sans redressement. La situation fiscale du contribuable est jugée conforme '.
            'pour la période vérifiée.',
            $instructeur,
        ));

        if ($controle->constats->isNotEmpty()) {
            $this->drawSectionTitle('Constats du contrôle');
            $this->drawConstats($controle);
        }

        if ($controle->rapport_synthese) {
            $this->drawSectionTitle('Synthèse du vérificateur');
            $this->drawSynthese($controle->rapport_synthese);
        }

        $this->SetY($this->GetY() + 6);
        $this->drawSignature('Le vérificateur');

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

    private function drawSynthese(string $texte): void
    {
        $table = new \easyTable($this, '{180}',
            'width:180; font-family:Helvetica; font-size:9.5; border:1; border-color:'.$this->hex($this->accent()).';');
        $table->easyCell($this->e($texte), 'bgcolor:#f1f5f9; paddingY:4; paddingX:5; min-height:14;');
        $table->printRow();
        $table->endTable(2);
    }
}
