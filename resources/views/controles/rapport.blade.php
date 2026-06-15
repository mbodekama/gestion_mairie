<x-app-layout :title="'Rapport — ' . $controle->numero">

@php
    $contrib = $controle->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';
    $constatsExistants = old('constats', $controle->constats->map(fn($c) => [
        'nature_taxe_id' => $c->nature_taxe_id,
        'exercice_fiscal_id' => $c->exercice_fiscal_id,
        'montant_declare' => (int) $c->montant_declare,
        'montant_verifie' => (int) $c->montant_verifie,
        'sanction_fiscale_id' => $c->sanction_fiscale_id,
        'observation' => $c->observation,
    ])->toArray());
@endphp

<x-page-header :titre="'Rapport de contrôle — ' . $controle->numero"
               :sous-titre="$nomContrib" />

@if ($errors->any())
    <div class="alert alert-danger fs-9"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('controles.rapport.store', $controle) }}" id="form-rapport">
    @csrf

    {{-- Constats --}}
    <div class="card mb-3">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><span class="fas fa-clipboard-check me-2 text-primary"></span>Constats par nature de taxe</h5>
            <button type="button" id="btn-ajouter" class="btn btn-outline-primary btn-sm">
                <span class="fas fa-plus me-1"></span>Ajouter une ligne
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:160px;">Nature</th>
                            <th style="min-width:110px;">Exercice</th>
                            <th class="text-end" style="min-width:120px;">Déclaré</th>
                            <th class="text-end" style="min-width:120px;">Vérifié</th>
                            <th class="text-end" style="min-width:120px;">Écart</th>
                            <th style="min-width:140px;">Sanction</th>
                            <th style="min-width:160px;">Observation</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="lignes-constats"></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted fs-10">
            L'écart (vérifié − déclaré) est calculé automatiquement à l'enregistrement.
        </div>
    </div>

    {{-- Synthèse --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0"><span class="fas fa-file-alt me-2 text-primary"></span>Synthèse</h5>
        </div>
        <div class="card-body">
            <textarea name="rapport_synthese" rows="4" maxlength="5000"
                      class="form-control" placeholder="Synthèse des constats, conclusions du vérificateur...">{{ old('rapport_synthese', $controle->rapport_synthese) }}</textarea>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('controles.show', $controle) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-success btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer le rapport
        </button>
    </div>
</form>

{{-- Gabarit d'une ligne --}}
<template id="tpl-ligne">
    <tr>
        <td>
            <select name="constats[__i__][nature_taxe_id]" class="form-select form-select-sm" required>
                <option value="">—</option>
                @foreach ($naturesTaxe as $nt)
                    <option value="{{ $nt->id }}">{{ $nt->libelle_court ?? $nt->libelle }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="constats[__i__][exercice_fiscal_id]" class="form-select form-select-sm">
                <option value="">—</option>
                @foreach ($exercices as $ex)
                    <option value="{{ $ex->id }}">{{ $ex->annee }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="constats[__i__][montant_declare]" min="0" step="1" value="0" class="form-control form-control-sm text-end"></td>
        <td><input type="number" name="constats[__i__][montant_verifie]" min="0" step="1" value="0" class="form-control form-control-sm text-end"></td>
        <td class="text-end fw-bold ecart text-muted">0</td>
        <td>
            <select name="constats[__i__][sanction_fiscale_id]" class="form-select form-select-sm">
                <option value="">—</option>
                @foreach ($sanctions as $s)
                    <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="constats[__i__][observation]" maxlength="255" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-suppr"><span class="fas fa-trash"></span></button></td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    const tbody = document.getElementById('lignes-constats');
    const tpl   = document.getElementById('tpl-ligne').innerHTML;
    const existants = @json(array_values($constatsExistants));
    let index = 0;

    function fcfa(n) { return (parseInt(n, 10) || 0).toLocaleString('fr-FR'); }

    function majEcart(tr) {
        const d = parseInt(tr.querySelector('[name$="[montant_declare]"]').value, 10) || 0;
        const v = parseInt(tr.querySelector('[name$="[montant_verifie]"]').value, 10) || 0;
        const cell = tr.querySelector('.ecart');
        const ecart = v - d;
        cell.textContent = fcfa(ecart);
        cell.classList.toggle('text-danger', ecart > 0);
        cell.classList.toggle('text-muted', ecart <= 0);
    }

    function ajouterLigne(data = {}) {
        const html = tpl.replace(/__i__/g, index);
        const tr = document.createElement('tr');
        tr.innerHTML = html.replace(/^\s*<tr>|<\/tr>\s*$/g, '');
        // Pré-remplissage
        const set = (suffixe, val) => { const el = tr.querySelector(`[name$="[${suffixe}]"]`); if (el && val != null) el.value = val; };
        set('nature_taxe_id', data.nature_taxe_id);
        set('exercice_fiscal_id', data.exercice_fiscal_id);
        set('montant_declare', data.montant_declare ?? 0);
        set('montant_verifie', data.montant_verifie ?? 0);
        set('sanction_fiscale_id', data.sanction_fiscale_id);
        set('observation', data.observation);

        tr.querySelector('.btn-suppr').addEventListener('click', () => tr.remove());
        tr.querySelectorAll('[name$="[montant_declare]"],[name$="[montant_verifie]"]').forEach(i =>
            i.addEventListener('input', () => majEcart(tr)));
        tbody.appendChild(tr);
        majEcart(tr);
        index++;
    }

    document.getElementById('btn-ajouter').addEventListener('click', () => ajouterLigne());

    if (existants.length) { existants.forEach(c => ajouterLigne(c)); }
    else { ajouterLigne(); }
}());
</script>
@endpush

</x-app-layout>
