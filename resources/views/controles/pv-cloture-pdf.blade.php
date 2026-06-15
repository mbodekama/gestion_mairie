<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #198754; padding-bottom: 8px; margin-bottom: 14px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #0f5132; }
        .titre { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 10px 0 2px; }
        .numero { text-align: center; color: #198754; font-weight: bold; margin-bottom: 16px; }
        .label { color: #777; font-size: 10px; text-transform: uppercase; }
        .valeur { font-weight: bold; }
        table.infos { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.infos td { border: none; padding: 3px 0; vertical-align: top; }
        .corps { line-height: 1.6; margin: 16px 0; text-align: justify; }
        table.constats { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.constats th, table.constats td { border: 1px solid #ccc; padding: 6px 8px; }
        table.constats th { background: #e8f5ee; text-align: left; font-size: 11px; }
        td.montant, th.montant { text-align: right; }
        .conclusion { border: 1px solid #198754; background: #f0faf4; padding: 12px; margin: 16px 0; }
        .signature { margin-top: 40px; text-align: right; }
        .pied { margin-top: 36px; font-size: 10px; color: #888; text-align: right; }
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
    $etab = $controle->etablissement;
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">Service du contrôle fiscal</div>
</div>

<div class="titre">PROCÈS-VERBAL DE CLÔTURE</div>
<div class="numero">Contrôle N° {{ $controle->numero }}</div>

<table class="infos">
    <tr>
        <td style="width:60%;">
            <div class="label">Contribuable</div>
            <div class="valeur">{{ $nomContrib }}</div>
            <div style="font-size:10px;">N° identifiant : {{ $contribuable->numero_identifiant ?? '—' }}</div>
            <div style="font-size:10px;">Établissement : {{ $etab?->numero }} @if ($etab?->denomination) — {{ $etab->denomination }} @endif</div>
        </td>
        <td style="text-align:right;">
            <div class="label">Période contrôlée</div>
            <div class="valeur">{{ $controle->periode_debut?->format('d/m/Y') ?? '—' }} → {{ $controle->periode_fin?->format('d/m/Y') ?? '—' }}</div>
            <div class="label" style="margin-top:6px;">Clôturé le</div>
            <div class="valeur">{{ $controle->date_cloture?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

<div class="corps">
    À l'issue des opérations de contrôle menées par
    <strong>{{ trim(($controle->agentInstructeur?->nom ?? '') . ' ' . ($controle->agentInstructeur?->prenoms ?? '')) ?: 'le service' }}</strong>,
    le présent procès-verbal constate la <strong>clôture du contrôle sans redressement</strong>.
    La situation fiscale du contribuable est jugée conforme pour la période vérifiée.
</div>

@if ($controle->constats->isNotEmpty())
    <table class="constats">
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

@if ($controle->rapport_synthese)
    <div class="conclusion">
        <div class="label">Synthèse du vérificateur</div>
        <div>{{ $controle->rapport_synthese }}</div>
    </div>
@endif

<div class="signature">
    <div class="label">Le vérificateur</div>
    <div style="margin-top:38px; border-top:1px solid #999; width:200px; display:inline-block;"></div>
</div>

<div class="pied">
    Édité le {{ now()->format('d/m/Y à H:i') }} — Document à valeur administrative.
</div>

</body>
</html>
