<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #6c5ce7; padding-bottom: 8px; margin-bottom: 14px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #341f97; }
        .titre { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 10px 0 2px; }
        .numero { text-align: center; color: #6c5ce7; font-weight: bold; margin-bottom: 16px; }
        .bloc { margin-bottom: 10px; }
        .label { color: #777; font-size: 10px; text-transform: uppercase; }
        .valeur { font-weight: bold; }
        table.infos { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.infos td { border: none; padding: 3px 0; vertical-align: top; }
        .corps { line-height: 1.6; margin: 16px 0; text-align: justify; }
        .encadre { border: 1px solid #ccc; padding: 10px 12px; margin: 12px 0; background: #f7f6fd; }
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
    $etab = $controle->etablissement;
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">
        {{ $convocation->service?->libelle ?? 'Service du contrôle fiscal' }}
    </div>
</div>

<div class="titre">AVIS DE CONVOCATION</div>
<div class="numero">N° {{ $convocation->numero }}</div>

<table class="infos">
    <tr>
        <td style="width:60%;">
            <div class="label">Destinataire</div>
            <div class="valeur">{{ $nomContrib }}</div>
            <div style="font-size:10px;">N° identifiant : {{ $contribuable->numero_identifiant ?? '—' }}</div>
            <div style="font-size:10px;">Établissement : {{ $etab?->numero }} @if ($etab?->denomination) — {{ $etab->denomination }} @endif</div>
        </td>
        <td style="text-align:right;">
            <div class="label">Date de convocation</div>
            <div class="valeur">{{ $convocation->date_convocation?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
            <div class="label" style="margin-top:6px;">Contrôle</div>
            <div class="valeur">{{ $controle->numero }}</div>
        </td>
    </tr>
</table>

<div class="corps">
    Dans le cadre d'un contrôle fiscal portant sur la période du
    <strong>{{ $controle->periode_debut?->format('d/m/Y') ?? '—' }}</strong> au
    <strong>{{ $controle->periode_fin?->format('d/m/Y') ?? '—' }}</strong>,
    vous êtes prié(e) de bien vouloir vous présenter, ou de désigner un représentant dûment mandaté,
    muni(e) de l'ensemble des pièces justificatives relatives à votre activité.
    @if ($controle->motif)
        <br><br><strong>Motif :</strong> {{ $controle->motif }}.
    @endif
</div>

<div class="encadre">
    <table style="width:100%; border:none;">
        <tr>
            <td style="border:none;">
                <div class="label">Délai de réponse</div>
                <div class="valeur">{{ $convocation->delai_reponse ? $convocation->delai_reponse . ' jours' : '—' }}</div>
            </td>
            <td style="border:none;">
                <div class="label">Date limite</div>
                <div class="valeur">{{ $convocation->date_limite?->format('d/m/Y') ?? '—' }}</div>
            </td>
            <td style="border:none;">
                <div class="label">Agent chargé</div>
                <div class="valeur">{{ trim(($convocation->agent?->nom ?? '') . ' ' . ($convocation->agent?->prenoms ?? '')) ?: '—' }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="signature">
    <div class="label">Pour la collectivité</div>
    <div style="margin-top:38px; border-top:1px solid #999; width:200px; display:inline-block;"></div>
</div>

<div class="pied">
    Édité le {{ now()->format('d/m/Y à H:i') }} — Document à valeur administrative.
</div>

</body>
</html>
