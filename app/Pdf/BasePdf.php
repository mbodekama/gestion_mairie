<?php

namespace App\Pdf;

use App\Models\Collectivite;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Http\Response;

/**
 * Classe de base pour tous les documents PDF officiels de la collectivité
 * (avis d'imposition, quittances, convocations, procès-verbaux, états…).
 *
 * Bâtie sur FPDF + l'extension easyTable (matthew-elisha/fpdf-easytable) et
 * bacon/bacon-qr-code pour le QR de vérification. Reprend le modèle de design
 * éprouvé du projet voisin GES-COLAB : en-tête à badge, bandeau coloré,
 * tableaux clé/valeur, pied de page avec QR et pagination.
 *
 * La couleur d'accent est surchargeable par document via accent() puisque
 * chaque type de pièce fiscale possède sa teinte (bleu, mauve, rouge…).
 */
abstract class BasePdf extends \exFPDF
{
    // ── Marges & largeur utile (mm) ──────────────────────────────
    protected const ML = 15.0;

    protected const UW = 180.0;

    // ── Palette [R, G, B] ────────────────────────────────────────
    protected const BLUE = [13,  110, 253];

    protected const PURPLE = [111, 66,  193];

    protected const RED = [220, 53,  69];

    protected const INDIGO = [108, 92,  231];

    protected const GREEN = [25,  135, 84];

    protected const SLATE = [73,  80,  87];

    protected const GRAY_BG = [241, 245, 249];

    protected const BORDER = [203, 213, 225];

    protected const DARK = [30,  41,  59];

    protected const MUTED = [100, 116, 139];

    protected const WHITE = [255, 255, 255];

    protected const RED_BADGE = [220, 38,  38];

    // Hauteur réservée pour le pied de page (mm)
    protected const FOOTER_H = 24.0;

    public function __construct(
        protected readonly array $data,
        bool $autoPageBreak = false,
        float $autoPageBreakMargin = self::FOOTER_H,
    ) {
        parent::__construct('P', 'mm', 'A4');
        $this->SetMargins(self::ML, 10.0, self::ML);
        $this->AliasNbPages();
        $this->SetAutoPageBreak($autoPageBreak, $autoPageBreakMargin);
        $this->AddPage();
    }

    abstract public function build(): static;

    /**
     * Construit le document et renvoie une réponse HTTP PDF.
     * $disposition : 'attachment' (téléchargement) ou 'inline' (aperçu).
     */
    public function reponse(string $filename, string $disposition = 'attachment'): Response
    {
        ob_start();
        $content = $this->build()->Output('S', '');
        ob_end_clean();

        return response()->make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            'Content-Length' => strlen($content),
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Couleur d'accent du document (titres, bandeaux, séparateurs).
     * Surchargée par chaque document ; bleu par défaut.
     */
    protected function accent(): array
    {
        return self::BLUE;
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS DOMAINE PARTAGÉS
    // ─────────────────────────────────────────────────────────────

    /** Construit le bloc d'en-tête commun à partir d'une collectivité. */
    protected static function societe(?Collectivite $c, array $extra = []): array
    {
        return array_merge([
            'company_name' => $c?->libelle ?? 'Collectivité',
            'company_initials' => $c?->code,
            'company_address' => $c?->adresse,
            'company_phone' => $c?->telephone1,
            'company_email' => $c?->email,
            'generated_date' => now()->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y à H:i'),
        ], $extra);
    }

    /** Libellé d'un contribuable (personne physique ou morale). */
    protected static function nomContribuable($contribuable): string
    {
        if (! $contribuable) {
            return '—';
        }

        return $contribuable->type_personne === 'PP'
            ? (trim(($contribuable->nom ?? '').' '.($contribuable->prenoms ?? '')) ?: '—')
            : ($contribuable->raison_sociale ?? '—');
    }

    /** Formatage monétaire FCFA (montants déjà liquidés, pas de calcul ici). */
    protected function fcfa($valeur): string
    {
        return number_format((float) $valeur, 0, ',', ' ').' FCFA';
    }

    // ── Pied de page (appelé automatiquement par FPDF) ───────────

    public function Footer(): void
    {
        $pageH = $this->GetPageHeight();
        $yLine = $pageH - self::FOOTER_H + 1;
        $yStart = $yLine + 2;
        $qrSize = self::FOOTER_H - 5;          // 19 mm

        // Séparateur
        $this->SetLineWidth(0.4);
        $this->dc($this->accent());
        $this->Line(self::ML, $yLine, self::ML + self::UW, $yLine);

        // QR code (référence du document → vérification d'authenticité)
        $qrContent = $this->data['verification_url']
            ?? $this->data['reference']
            ?? $this->data['company_name']
            ?? 'COLLECTIVITE';
        $this->drawQrCode($qrContent, self::ML, $yStart, $qrSize);

        // Message de vérification
        $msgX = self::ML + $qrSize + 3;
        $this->SetFont('Helvetica', 'B', 7.5);
        $this->tc(self::DARK);
        $this->SetXY($msgX, $yStart + 2);
        $this->Cell(95, 4.5, $this->e('Document à valeur administrative'), 0, 0, 'L');

        $this->SetFont('Helvetica', '', 7);
        $this->tc(self::MUTED);
        $this->SetXY($msgX, $yStart + 7);
        $this->Cell(95, 4, $this->e('Vérifiez l\'authenticité en scannant le QR code.'), 0, 0, 'L');

        // Pagination + édition à droite
        $this->SetFont('Helvetica', 'B', 8);
        $this->tc($this->accent());
        $this->SetXY(self::ML, $yStart);
        $this->Cell(self::UW, 4.5, 'Page '.$this->PageNo().' / {nb}', 0, 0, 'R');

        $this->SetFont('Helvetica', '', 7);
        $this->tc(self::MUTED);
        $edite = $this->data['generated_at'] ?? '';
        if ($edite !== '') {
            $this->SetXY(self::ML, $yStart + 6);
            $this->Cell(self::UW, 4, $this->e('Édité le '.$edite), 0, 0, 'R');
        }
        $phone = $this->data['company_phone'] ?? '';
        if ($phone !== '') {
            $this->SetXY(self::ML, $yStart + ($edite !== '' ? 11 : 6));
            $this->Cell(self::UW, 4, $this->e('Tél : '.$phone), 0, 0, 'R');
        }
    }

    // Dessine un QR code vectoriel via la matrice bacon/bacon-qr-code.
    // Aucune extension image requise — modules tracés avec Rect().
    protected function drawQrCode(string $content, float $x, float $y, float $size): void
    {
        $qr = Encoder::encode($content, ErrorCorrectionLevel::M());
        $matrix = $qr->getMatrix();
        $n = $matrix->getWidth();
        $cell = $size / $n;

        // Zone blanche (quiet zone)
        $this->fc(self::WHITE);
        $this->Rect($x, $y, $size, $size, 'F');

        // Modules sombres
        $this->fc(self::DARK);
        for ($row = 0; $row < $n; $row++) {
            for ($col = 0; $col < $n; $col++) {
                if ($matrix->get($col, $row) & 1) {
                    $this->Rect($x + $col * $cell, $y + $row * $cell, $cell, $cell, 'F');
                }
            }
        }
    }

    // ── En-tête commun ───────────────────────────────────────────
    //
    // La boîte de droite prend deux formes :
    //   - $rightValue fourni  → boîte bordée avec libellé + valeur (n° de pièce)
    //   - $rightValue null    → boîte remplie accent avec titre seul
    protected function drawHeader(string $rightLabel, ?string $rightValue = null): void
    {
        $initials = $this->e($this->resolveInitials());
        $tagline = $this->e($this->resolveTagline());
        $accent = $this->accent();

        // ── Badge initiales collectivité ──────────────────────
        $this->fc($accent);
        $this->roundedRect(self::ML, 10, 12, 12, 2.5, 'F');
        $this->SetFont('Helvetica', 'B', strlen($initials) > 2 ? 7 : 9);
        $this->tc(self::WHITE);
        $this->SetXY(self::ML, 13.5);
        $this->Cell(12, 5, $initials, 0, 0, 'C');

        // ── Raison sociale ────────────────────────────────────
        $this->SetFont('Helvetica', 'B', 17);
        $this->tc(self::DARK);
        $this->SetXY(self::ML + 15, 10);
        $this->Cell(95, 7, $this->e($this->data['company_name']), 0, 0, 'L');

        // ── Adresse & tagline ─────────────────────────────────
        $this->SetFont('Helvetica', '', 8);
        $this->tc(self::MUTED);
        $this->SetXY(self::ML + 15, 18);
        $this->Cell(95, 4, $tagline, 0, 0, 'L');

        // ── Boîte droite ──────────────────────────────────────
        $rx = 148.0;
        if ($rightValue !== null) {
            $this->SetLineWidth(0.45);
            $this->dc($accent);
            $this->roundedRect($rx, 10, 47, 14, 2, 'D');

            $this->SetFont('Helvetica', '', 7);
            $this->tc(self::MUTED);
            $this->SetXY($rx, 12.5);
            $this->Cell(47, 4, $this->e($rightLabel), 0, 0, 'C');

            $this->SetFont('Helvetica', 'B', 12);
            $this->tc($accent);
            $this->SetXY($rx, 17.5);
            $this->Cell(47, 5, $this->e($rightValue), 0, 0, 'C');
        } else {
            $this->fc($accent);
            $this->roundedRect($rx, 10, 47, 14, 2, 'F');
            $this->SetFont('Helvetica', 'B', 10);
            $this->tc(self::WHITE);
            $this->SetXY($rx, 13.5);
            $this->Cell(47, 6, $this->e($rightLabel), 0, 0, 'C');
        }

        // ── Séparateur de fermeture du bloc en-tête ───────────
        $this->SetLineWidth(0.5);
        $this->dc($accent);
        $this->Line(self::ML, 25.5, self::ML + self::UW, 25.5);
    }

    // Initiales du badge : $data['company_initials'] → 2 initiales du nom.
    private function resolveInitials(): string
    {
        if (! empty($this->data['company_initials'])) {
            return mb_strtoupper(mb_substr($this->data['company_initials'], 0, 3));
        }

        $words = preg_split('/\s+/', trim($this->data['company_name'] ?? ''));
        $initials = implode('', array_map(
            fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)),
            array_slice($words, 0, 2)
        ));

        return $initials ?: 'CT';
    }

    // Ligne sous la raison sociale : tagline explicite → adresse → libellé.
    private function resolveTagline(): string
    {
        if (! empty($this->data['company_tagline'])) {
            return $this->data['company_tagline'];
        }

        $address = $this->data['company_address'] ?? '';
        $service = $this->data['service'] ?? 'Régie fiscale';

        return $address !== '' ? $address.' | '.$service : $service;
    }

    // Bandeau coloré sous l'en-tête avec titre et sous-titre optionnel
    protected function drawBanner(string $title, string $subtitle = ''): void
    {
        $y = 28.0;
        $this->fc($this->accent());
        $this->Rect(self::ML, $y, self::UW, 17, 'F');

        $this->SetFont('Helvetica', 'B', 14);
        $this->tc(self::WHITE);
        $this->SetXY(self::ML, $y + 3);
        $this->Cell(self::UW, 6, $this->e($title), 0, 0, 'C');

        if ($subtitle !== '') {
            $this->SetFont('Helvetica', '', 7.5);
            $this->tc(self::WHITE);
            $this->SetXY(self::ML, $y + 10);
            $this->Cell(self::UW, 4, $this->e($subtitle), 0, 0, 'C');
        }
    }

    // Date alignée à droite, sous le bandeau
    protected function drawDate(string $city = 'Abidjan'): void
    {
        $this->SetFont('Helvetica', '', 10);
        $this->tc(self::MUTED);
        $this->SetXY(self::ML, 51);
        $this->Cell(self::UW, 5, $this->e($city.', le '.$this->data['generated_date']), 0, 0, 'R');
    }

    // Titre de section : barre verticale accent + fond gris
    protected function drawSectionTitle(string $title): void
    {
        $y = $this->GetY() + 4;

        $this->fc($this->accent());
        $this->Rect(self::ML, $y, 1.5, 7, 'F');

        $this->fc(self::GRAY_BG);
        $this->Rect(self::ML + 1.5, $y, self::UW - 1.5, 7, 'F');

        $this->SetFont('Helvetica', 'B', 8.5);
        $this->tc(self::SLATE);
        $this->SetXY(self::ML + 5, $y + 1.5);
        $this->Cell(self::UW - 5, 5, $this->e(strtoupper($title)), 0, 0, 'L');

        $this->SetY($y + 8);
    }

    /**
     * Bloc « parties » sur deux colonnes : contribuable/destinataire à gauche,
     * métadonnées (exercice, dates…) à droite. Chaque entrée = [label, valeur].
     */
    protected function drawParties(array $left, array $right): void
    {
        $y = $this->GetY();
        $colW = self::UW / 2;

        $renderCol = function (array $rows, float $x, string $align) {
            $cy = $this->GetY();
            foreach ($rows as $row) {
                [$label, $value] = $row;
                $this->SetFont('Helvetica', '', 7);
                $this->tc(self::MUTED);
                $this->SetXY($x, $cy);
                $this->Cell(self::UW / 2 - 2, 3.6, $this->e(strtoupper($label)), 0, 0, $align);
                $cy += 3.6;

                $this->SetFont('Helvetica', 'B', 9.5);
                $this->tc(self::DARK);
                $this->SetXY($x, $cy);
                $this->Cell(self::UW / 2 - 2, 4.6, $this->e($value), 0, 0, $align);
                $cy += 6.0;
            }

            return $cy;
        };

        $this->SetY($y);
        $endL = $renderCol($left, self::ML, 'L');
        $this->SetY($y);
        $endR = $renderCol($right, self::ML + $colW + 2, 'R');

        $this->SetY(max($endL, $endR) + 2);
    }

    /**
     * Paragraphe de corps justifié (texte narratif des convocations / PV).
     */
    protected function drawParagraphe(string $texte, float $fontSize = 10.5): void
    {
        $table = new \easyTable($this, '{180}',
            'width:180; font-family:Helvetica; font-size:'.$fontSize.'; border:0;');
        $table->easyCell($this->e($texte), 'paddingY:2; min-height:10;');
        $table->printRow();
        $table->endTable(4);
    }

    // Bloc de signature simple, libellé centré + cadre/ligne
    protected function drawSignature(string $label, string $align = 'R'): void
    {
        $y = $this->GetY() + 6;
        $w = 70.0;
        $x = $align === 'R' ? self::ML + self::UW - $w : self::ML;

        $this->SetFont('Helvetica', 'B', 8.5);
        $this->tc(self::SLATE);
        $this->SetXY($x, $y);
        $this->Cell($w, 5, $this->e($label), 0, 0, 'C');

        $this->SetLineWidth(0.3);
        $this->SetDrawColor(160, 160, 160);
        $this->Line($x + 6, $y + 24, $x + $w - 6, $y + 24);

        $this->SetY($y + 26);
    }

    // ── Helpers couleur / fonte ──────────────────────────────────

    protected function fc(array $rgb): void
    {
        $this->SetFillColor(...$rgb);
    }

    protected function tc(array $rgb): void
    {
        $this->SetTextColor(...$rgb);
    }

    protected function dc(array $rgb): void
    {
        $this->SetDrawColor(...$rgb);
    }

    protected function e(string $text): string
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text) ?: $text;
    }

    /** Code hexadécimal #rrggbb d'une couleur de la palette (pour easyTable). */
    protected function hex(array $rgb): string
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    // ── Helpers formes géométriques ──────────────────────────────

    protected function roundedRect(float $x, float $y, float $w, float $h, float $r, string $style = ''): void
    {
        $op = match ($style) {
            'F' => 'f', 'FD', 'DF' => 'B', default => 'S'
        };
        $arc = 4 / 3 * (M_SQRT2 - 1);

        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $this->k, ($this->h - $y) * $this->k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $this->k, ($this->h - $y) * $this->k));
        $this->bezierArc($xc + $r * $arc, $yc - $r, $xc + $r, $yc - $r * $arc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $this->k, ($this->h - $yc) * $this->k));
        $this->bezierArc($xc + $r, $yc + $r * $arc, $xc + $r * $arc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $this->k, ($this->h - ($y + $h)) * $this->k));
        $this->bezierArc($xc - $r * $arc, $yc + $r, $xc - $r, $yc + $r * $arc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $yc) * $this->k));
        $this->bezierArc($xc - $r, $yc - $r * $arc, $xc - $r * $arc, $yc - $r, $xc, $yc - $r);

        $this->_out($op);
    }

    protected function ellipse(float $cx, float $cy, float $rx, float $ry, string $style = ''): void
    {
        $op = match ($style) {
            'F' => 'f', 'FD', 'DF' => 'B', default => 'S'
        };
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
        $k = $this->k;
        $h = $this->h;

        $this->_out(sprintf('%.2F %.2F m', ($cx + $rx) * $k, ($h - $cy) * $k));
        $this->bezierArc($cx + $rx, $cy - $ly, $cx + $lx, $cy - $ry, $cx, $cy - $ry);
        $this->bezierArc($cx - $lx, $cy - $ry, $cx - $rx, $cy - $ly, $cx - $rx, $cy);
        $this->bezierArc($cx - $rx, $cy + $ly, $cx - $lx, $cy + $ry, $cx, $cy + $ry);
        $this->bezierArc($cx + $lx, $cy + $ry, $cx + $rx, $cy + $ly, $cx + $rx, $cy);
        $this->_out($op);
    }

    protected function bezierArc(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): void
    {
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k, ($this->h - $y1) * $this->k,
            $x2 * $this->k, ($this->h - $y2) * $this->k,
            $x3 * $this->k, ($this->h - $y3) * $this->k,
        ));
    }

    protected function SetDash(float $black = 0, float $white = 0): void
    {
        if ($black > 0) {
            $this->_out(sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k));
        } else {
            $this->_out('[] 0 d');
        }
    }
}
