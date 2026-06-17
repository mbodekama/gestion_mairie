<?php

namespace App\Pdf;

use App\Models\Collectivite;
use App\Models\ControleFiscal;

/**
 * Avis de convocation dans le cadre d'un contrôle fiscal. Couleur : indigo.
 */
class Convocation extends BasePdf
{
    public function __construct(
        private readonly ControleFiscal $controle,
        private readonly mixed $convocation,
        private readonly mixed $contribuable,
        ?Collectivite $collectivite,
    ) {
        parent::__construct(self::societe($collectivite, [
            'service' => $convocation->service?->libelle ?? 'Service du contrôle fiscal',
            'reference' => $convocation->numero,
        ]));
    }

    public function build(): static
    {
        $controle = $this->controle;
        $conv = $this->convocation;
        $etab = $controle->etablissement;

        $this->drawHeader('CONVOCATION N°', $conv->numero);
        $this->drawBanner('AVIS DE CONVOCATION', 'CONTRÔLE FISCAL');
        $this->drawDate();

        $this->SetXY(self::ML, 58);
        $this->drawParties(
            [
                ['Destinataire', self::nomContribuable($this->contribuable)],
                ['N° identifiant', $this->contribuable->numero_identifiant ?? '—'],
                ['Établissement', trim(($etab?->numero ?? '—').' '.($etab?->denomination ? '— '.$etab->denomination : ''))],
            ],
            [
                ['Date de convocation', $conv->date_convocation?->format('d/m/Y') ?? now()->format('d/m/Y')],
                ['Contrôle', $controle->numero],
            ],
        );

        $texte = sprintf(
            "Dans le cadre d'un contrôle fiscal portant sur la période du %s au %s, vous êtes prié(e) ".
            'de bien vouloir vous présenter, ou de désigner un représentant dûment mandaté, muni(e) de '.
            "l'ensemble des pièces justificatives relatives à votre activité.",
            $controle->periode_debut?->format('d/m/Y') ?? '—',
            $controle->periode_fin?->format('d/m/Y') ?? '—',
        );
        if ($controle->motif) {
            $texte .= '  Motif : '.$controle->motif.'.';
        }
        $this->drawParagraphe($texte);

        $this->drawSectionTitle('Modalités');
        $this->drawModalites($conv);

        $this->SetY($this->GetY() + 6);
        $this->drawSignature('Pour la collectivité');

        return $this;
    }

    private function drawModalites(mixed $conv): void
    {
        $labelStyle = 'bgcolor:#f8fafc; font-color:#94a3b8; font-style:B; font-size:8; paddingY:3; border:1; border-color:#e2e8f0; align:C;';
        $valStyle = 'font-style:B; font-size:10; paddingY:3; border:1; border-color:#e2e8f0; align:C;';

        $agent = trim(($conv->agent?->nom ?? '').' '.($conv->agent?->prenoms ?? '')) ?: '—';

        $table = new \easyTable($this, '%{33,34,33}',
            'width:180; font-family:Helvetica; border:0;');

        $table->rowStyle('min-height:9;');
        $table->easyCell($this->e('DÉLAI DE RÉPONSE'), $labelStyle);
        $table->easyCell($this->e('DATE LIMITE'), $labelStyle);
        $table->easyCell($this->e('AGENT CHARGÉ'), $labelStyle);
        $table->printRow();

        $table->rowStyle('min-height:11;');
        $table->easyCell($this->e($conv->delai_reponse ? $conv->delai_reponse.' jours' : '—'), $valStyle);
        $table->easyCell($this->e($conv->date_limite?->format('d/m/Y') ?? '—'), $valStyle.'font-color:'.$this->hex($this->accent()).';');
        $table->easyCell($this->e($agent), $valStyle);
        $table->printRow();

        $table->endTable(2);
    }
}
