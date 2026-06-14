<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #6f42c1; padding-bottom: 8px; margin-bottom: 14px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #3d0f7c; }
        .titre { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 6px 0 2px; }
        .numero { text-align: center; color: #6f42c1; font-weight: bold; margin-bottom: 14px; }
        .bloc { margin-bottom: 12px; }
        .bloc .label { color: #777; font-size: 10px; text-transform: uppercase; }
        .bloc .valeur { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f1edf9; text-align: left; font-size: 11px; }
        td.montant, th.montant { text-align: right; }
        .total-row td { font-weight: bold; background: #f8f9fa; }
        .paiement td { border: none; padding: 2px 0; }
        .paiement .label { color: #777; width: 140px; }
        .pied { margin-top: 30px; font-size: 10px; color: #888; text-align: right; }
        .signature { margin-top: 40px; }
    </style>
</head>
<body>

@php
    $nomContrib = $contribuable
        ? ($contribuable->type_personne === 'PP'
            ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
            : ($contribuable->raison_sociale ?? '—'))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
    $premier = $reglements->first();
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">Service du recouvrement</div>
</div>

<div class="titre">QUITTANCE DE PAIEMENT</div>
<div class="numero">N° {{ $recouvrement->numero_quittance ?? $recouvrement->numero_reglement }}</div>

<table style="border:none; margin-bottom:12px;">
    <tr>
        <td style="border:none; width:60%;">
            <div class="label">Reçu de</div>
            <div class="valeur">{{ $nomContrib }}</div>
            <div style="font-size:10px;">N° identifiant : {{ $contribuable->numero_identifiant ?? '—' }}</div>
        </td>
        <td style="border:none; text-align:right;">
            <div class="label">Date</div>
            <div class="valeur">{{ optional($premier?->date_reglement)->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>N° Règlement</th>
            <th>Émission</th>
            <th>Nature</th>
            <th class="montant">Montant</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reglements as $r)
            <tr>
                <td>{{ $r->numero_reglement }}</td>
                <td>{{ $r->emissionTaxe?->numero_emission ?? '—' }}</td>
                <td>{{ $r->emissionTaxe?->natureTaxe?->libelle_court ?? $r->emissionTaxe?->natureTaxe?->libelle ?? '—' }}</td>
                <td class="montant">{{ $fcfa($r->montant_impute) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="montant">TOTAL PERÇU</td>
            <td class="montant">{{ $fcfa($total) }}</td>
        </tr>
    </tbody>
</table>

<div class="bloc" style="margin-top:16px;">
    <table class="paiement">
        <tr>
            <td class="label">Mode de règlement</td>
            <td>{{ $premier?->modeReglement?->libelle ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Type de règlement</td>
            <td>{{ $premier?->typeReglement?->libelle ?? '—' }}</td>
        </tr>
        @if ($premier?->banque_id || $premier?->numero_cheque)
            <tr>
                <td class="label">Banque / N° chèque</td>
                <td>{{ $premier?->banque?->libelle ?? '—' }} {{ $premier?->numero_cheque ? '— ' . $premier->numero_cheque : '' }}</td>
            </tr>
        @endif
        @if ($premier?->operateur_mobile || $premier?->reference_transaction)
            <tr>
                <td class="label">Mobile Money</td>
                <td>{{ $premier?->operateur_mobile ?? '—' }} {{ $premier?->reference_transaction ? '— Réf. ' . $premier->reference_transaction : '' }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Recette</td>
            <td>{{ $premier?->recette?->libelle ?? '—' }}</td>
        </tr>
    </table>
</div>

<table style="border:none; margin-top:10px;">
    <tr>
        <td style="border:none; width:60%;"></td>
        <td style="border:none; text-align:center;" class="signature">
            <div style="border-top:1px solid #999; padding-top:4px; font-size:10px;">Le Receveur</div>
        </td>
    </tr>
</table>

<div class="pied">
    Quittance éditée le {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html>
