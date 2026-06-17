<?php

/**
 * Le paquet matthew-elisha/fpdf-easytable (dev-master) livre ses fichiers avec
 * un espace parasite AVANT la balise <?php. À l'inclusion, cet octet est émis
 * sur la sortie : sans output_buffering actif, PHP considère que le corps de la
 * réponse a commencé et lève « Cannot modify header information » lors de l'envoi
 * des en-têtes du PDF.
 *
 * Ce script normalise ces fichiers (suppression de tout blanc avant <?php). Il
 * est rejoué automatiquement à chaque `composer install`/`dump-autoload` via le
 * hook post-autoload-dump, ce qui le rend résistant à la régénération de vendor.
 */
$fichiers = [
    __DIR__.'/../vendor/matthew-elisha/fpdf-easytable/exfpdf.php',
    __DIR__.'/../vendor/matthew-elisha/fpdf-easytable/easyTable.php',
];

foreach ($fichiers as $fichier) {
    if (! is_file($fichier)) {
        continue;
    }

    $contenu = file_get_contents($fichier);

    // Retire un éventuel BOM UTF-8 puis tout blanc précédant la première balise.
    $nettoye = preg_replace('/^\xEF\xBB\xBF/', '', $contenu);
    $nettoye = preg_replace('/^\s+<\?php/', '<?php', $nettoye, 1);

    if ($nettoye !== null && $nettoye !== $contenu) {
        file_put_contents($fichier, $nettoye);
        fwrite(STDOUT, 'fpdf-easytable normalisé : '.basename($fichier).PHP_EOL);
    }
}
