<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 14px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #0a3d91; }
        .titre { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 10px 0 2px; }
        .numero { text-align: center; color: #0d6efd; font-weight: bold; margin-bottom: 16px; }
        .label { color: #777; font-size: 10px; text-transform: uppercase; }
        .valeur { font-weight: bold; }
        table.infos { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.infos td { border: none; padding: 3px 0; vertical-align: top; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.detail th, table.detail td { border: 1px solid #ccc; padding: 7px 9px; }
        table.detail th { background: #eaf1fd; text-align: left; font-size: 11px; }
        td.montant, th.montant { text-align: right; }
        .exo td { color: #198754; }
        .net td { font-weight: bold; font-size: 14px; background: #f8f9fa; }
        .pied { margin-top: 30px; font-size: 10px; color: #888; text-align: right; }
        .signature { margin-top: 36px; text-align: right; }
    </style>
</head>
<body>

@php
    $contrib = $emission->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';

    // Montant net dû = prorata si renseigné, sinon annuel ; brut = net + exonéré
    $net  = (float) $emission->montant_prorata > 0 ? (string) $emission->montant_prorata : (string) $emission->montant_annuel;
    $exo  = (string) $emission->montant_exonere;
    $brut = bcadd($net, $exo, 2);
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">Service des émissions / liquidation</div>
</div>

<div class="titre">AVIS D'IMPOSITION</div>
<div class="numero">N° {{ $emission->numero_emission }} — Article {{ $emission->numero_article }}</div>

<table class="infos">
    <tr>
        <td style="width:60%;">
            <div class="label">Contribuable</div>
            <div class="valeur">{{ $nomContrib }}</div>
            <div style="font-size:10px;">N° identifiant : {{ $contrib->numero_identifiant ?? '—' }}</div>
            <div style="font-size:10px;">Établissement : {{ $emission->etablissement?->numero }}
                @if ($emission->etablissement?->denomination) — {{ $emission->etablissement->denomination }} @endif</div>
        </td>
        <td style="text-align:right;">
            <div class="label">Exercice</div>
            <div class="valeur">{{ $emission->exerciceFiscal?->annee ?? '—' }}</div>
            <div class="label" style="margin-top:6px;">Date de liquidation</div>
            <div class="valeur">{{ $emission->date_liquidation?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

<table class="detail">
    <thead>
        <tr>
            <th>Nature de taxe</th>
            <th>Périodicité</th>
            <th class="montant">Montant brut</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $emission->natureTaxe?->libelle ?? $emission->natureTaxe?->libelle_court ?? '—' }}</td>
            <td>{{ $emission->periodicite?->libelle ?? '—' }}</td>
            <td class="montant">{{ $fcfa($brut) }}</td>
        </tr>
        @if ($emission->exoneration_id && (float) $exo > 0)
            <tr class="exo">
                <td colspan="2">
                    Exonération {{ $emission->exoneration?->numero }}
                    @if ($emission->exoneration?->reference_decret) — réf. {{ $emission->exoneration->reference_decret }} @endif
                </td>
                <td class="montant">− {{ $fcfa($exo) }}</td>
            </tr>
        @endif
        <tr class="net">
            <td colspan="2" class="montant">MONTANT NET À PAYER</td>
            <td class="montant">{{ $fcfa($net) }}</td>
        </tr>
    </tbody>
</table>

@if ((float) $emission->montant_prorata > 0)
    <p style="font-size:10px; color:#666; margin-top:6px;">
        Montant calculé au prorata ({{ $emission->nb_mois_prorata }} mois).
    </p>
@endif

<div class="signature">
    <div class="label">Pour la collectivité</div>
    <div style="margin-top:38px; border-top:1px solid #999; width:200px; display:inline-block;"></div>
</div>

<div class="pied">
    Édité le {{ now()->format('d/m/Y à H:i') }} — Document à valeur administrative.
</div>

</body>
</html>
