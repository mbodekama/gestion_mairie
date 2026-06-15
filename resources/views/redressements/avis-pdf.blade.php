<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #dc3545; padding-bottom: 8px; margin-bottom: 14px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #7c1520; }
        .titre { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 10px 0 2px; }
        .numero { text-align: center; color: #dc3545; font-weight: bold; margin-bottom: 16px; }
        .label { color: #777; font-size: 10px; text-transform: uppercase; }
        .valeur { font-weight: bold; }
        table.infos { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.infos td { border: none; padding: 3px 0; vertical-align: top; }
        .corps { line-height: 1.6; margin: 16px 0; text-align: justify; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.detail th, table.detail td { border: 1px solid #ccc; padding: 6px 8px; }
        table.detail th { background: #fdecee; text-align: left; font-size: 11px; }
        td.montant, th.montant { text-align: right; }
        .total-row td { font-weight: bold; background: #f8f9fa; }
        .recap { width: 50%; margin-left: 50%; border-collapse: collapse; margin-top: 10px; }
        .recap td { padding: 5px 8px; }
        .recap .total { border-top: 2px solid #dc3545; font-size: 14px; font-weight: bold; color: #dc3545; }
        .signature { margin-top: 36px; text-align: right; }
        .pied { margin-top: 30px; font-size: 10px; color: #888; text-align: right; }
    </style>
</head>
<body>

@php
    $etab = $controle?->etablissement;
    $nomContrib = $contribuable
        ? ($contribuable->type_personne === 'PP'
            ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
            : ($contribuable->raison_sociale ?? '—'))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">Service du contrôle fiscal</div>
</div>

<div class="titre">AVIS DE REDRESSEMENT</div>
<div class="numero">N° {{ $redressement->numero }}</div>

<table class="infos">
    <tr>
        <td style="width:60%;">
            <div class="label">Contribuable</div>
            <div class="valeur">{{ $nomContrib }}</div>
            <div style="font-size:10px;">N° identifiant : {{ $contribuable->numero_identifiant ?? '—' }}</div>
            <div style="font-size:10px;">Établissement : {{ $etab?->numero }} @if ($etab?->denomination) — {{ $etab->denomination }} @endif</div>
        </td>
        <td style="text-align:right;">
            <div class="label">Date</div>
            <div class="valeur">{{ $redressement->date_redressement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
            <div class="label" style="margin-top:6px;">Contrôle d'origine</div>
            <div class="valeur">{{ $controle?->numero ?? '—' }}</div>
        </td>
    </tr>
</table>

<div class="corps">
    À la suite du contrôle fiscal portant sur la période du
    <strong>{{ $controle?->periode_debut?->format('d/m/Y') ?? '—' }}</strong> au
    <strong>{{ $controle?->periode_fin?->format('d/m/Y') ?? '—' }}</strong>,
    des insuffisances ont été constatées. En conséquence, il est procédé au redressement suivant.
</div>

{{-- Constats du contrôle --}}
@if ($controle && $controle->constats->isNotEmpty())
    <table class="detail">
        <thead>
            <tr>
                <th>Nature de taxe</th>
                <th class="montant">Déclaré</th>
                <th class="montant">Vérifié</th>
                <th class="montant">Écart</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($controle->constats as $c)
                <tr>
                    <td>{{ $c->natureTaxe?->libelle_court ?? $c->natureTaxe?->libelle ?? '—' }}</td>
                    <td class="montant">{{ $fcfa($c->montant_declare) }}</td>
                    <td class="montant">{{ $fcfa($c->montant_verifie) }}</td>
                    <td class="montant">{{ $fcfa($c->ecart) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- Émissions complémentaires --}}
@if ($redressement->emissionsTaxe->isNotEmpty())
    <div style="margin-top:14px;" class="label">Émissions complémentaires émises</div>
    <table class="detail">
        <thead>
            <tr>
                <th>N° Émission</th><th>Nature</th><th>Exercice</th><th class="montant">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($redressement->emissionsTaxe as $em)
                <tr>
                    <td>{{ $em->numero_emission }}</td>
                    <td>{{ $em->natureTaxe?->libelle_court ?? $em->natureTaxe?->libelle ?? '—' }}</td>
                    <td>{{ $em->exerciceFiscal?->annee ?? '—' }}</td>
                    <td class="montant">{{ $fcfa($em->montant_annuel) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- Récapitulatif --}}
<table class="recap">
    <tr><td class="label">Droits</td><td class="montant">{{ $fcfa($redressement->montant_droits) }}</td></tr>
    <tr><td class="label">Pénalités</td><td class="montant">{{ $fcfa($redressement->montant_penalites) }}</td></tr>
    <tr class="total"><td>TOTAL À RÉGLER</td><td class="montant">{{ $fcfa($redressement->montant_total) }}</td></tr>
</table>

<div class="signature">
    <div class="label">Le receveur</div>
    <div style="margin-top:38px; border-top:1px solid #999; width:200px; display:inline-block;"></div>
</div>

<div class="pied">
    Édité le {{ now()->format('d/m/Y à H:i') }} — Document à valeur administrative. Sommes recouvrables selon la réglementation en vigueur.
</div>

</body>
</html>
