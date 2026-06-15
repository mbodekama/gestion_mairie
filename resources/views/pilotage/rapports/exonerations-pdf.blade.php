<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #222; margin: 0; }
        .entete { border-bottom: 2px solid #6c757d; padding-bottom: 8px; margin-bottom: 12px; }
        .entete .collectivite { font-size: 15px; font-weight: bold; color: #495057; }
        .titre { text-align: center; font-size: 17px; font-weight: bold; letter-spacing: 1px; margin: 8px 0 2px; }
        .sous-titre { text-align: center; color: #6c757d; margin-bottom: 14px; }
        h3.exercice { background: #eef0f2; padding: 6px 9px; margin: 14px 0 4px; font-size: 13px; border-left: 4px solid #6c757d; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.detail th, table.detail td { border: 1px solid #ccc; padding: 5px 7px; }
        table.detail th { background: #f1f3f5; text-align: left; font-size: 10px; }
        td.montant, th.montant { text-align: right; }
        .nature-row td { background: #f8f9fa; font-weight: bold; }
        .soustotal td { font-weight: bold; background: #eef0f2; }
        .total-general { margin-top: 16px; border-top: 3px solid #495057; padding-top: 8px; text-align: right; font-size: 15px; font-weight: bold; }
        .pied { margin-top: 24px; font-size: 9px; color: #888; text-align: right; }
        .vide { text-align: center; color: #888; padding: 30px; }
    </style>
</head>
<body>

@php
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
    $nomContrib = function ($c) {
        if (! $c) return '—';
        return $c->type_personne === 'PP'
            ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? ''))
            : ($c->raison_sociale ?? '—');
    };
@endphp

<div class="entete">
    <div class="collectivite">{{ $collectivite?->libelle ?? 'Collectivité' }}</div>
    <div style="font-size:10px; color:#666;">Service du pilotage fiscal</div>
</div>

<div class="titre">ÉTAT DE RESTITUTION DES EXONÉRATIONS</div>
<div class="sous-titre">
    {{ $exercice ? 'Exercice ' . $exercice->annee : 'Tous exercices' }}
    — édité le {{ now()->format('d/m/Y') }}
</div>

@forelse ($parExercice as $annee => $emissions)
    @php
        $totalExercice = $emissions->reduce(fn ($c, $e) => bcadd($c, (string) $e->montant_exonere, 2), '0');
        $parNature = $emissions->groupBy(fn ($e) => $e->natureTaxe?->libelle_court ?? $e->natureTaxe?->libelle ?? '—');
    @endphp

    <h3 class="exercice">Exercice {{ $annee }} — {{ $emissions->count() }} émission(s)</h3>

    <table class="detail">
        <thead>
            <tr>
                <th>Contribuable</th>
                <th>Établissement</th>
                <th>Exonération</th>
                <th class="montant">Montant exonéré</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parNature as $nature => $lignes)
                <tr class="nature-row">
                    <td colspan="3">{{ $nature }}</td>
                    <td class="montant">{{ $fcfa($lignes->sum('montant_exonere')) }}</td>
                </tr>
                @foreach ($lignes as $e)
                    <tr>
                        <td>{{ $nomContrib($e->etablissement?->contribuable) }}</td>
                        <td>{{ $e->etablissement?->numero ?? '—' }}</td>
                        <td>{{ $e->exoneration?->numero }}@if ($e->exoneration?->reference_decret) — {{ $e->exoneration->reference_decret }} @endif</td>
                        <td class="montant">{{ $fcfa($e->montant_exonere) }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="soustotal">
                <td colspan="3" class="montant">Sous-total exercice {{ $annee }}</td>
                <td class="montant">{{ $fcfa($totalExercice) }}</td>
            </tr>
        </tbody>
    </table>
@empty
    <p class="vide">Aucune exonération appliquée sur la période sélectionnée.</p>
@endforelse

@if ($parExercice->isNotEmpty())
    <div class="total-general">TOTAL GÉNÉRAL EXONÉRÉ : {{ $fcfa($totalGeneral) }}</div>
@endif

<div class="pied">
    Document à valeur administrative — montants effectivement exonérés sur les émissions liquidées.
</div>

</body>
</html>
