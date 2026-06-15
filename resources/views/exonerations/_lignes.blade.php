@php
    $lignesExistantes = old('lignes', isset($exoneration)
        ? $exoneration->lignes->map(fn ($l) => [
            'nature_taxe_id'    => $l->nature_taxe_id,
            'annee_application' => $l->annee_application,
            'taux'              => (float) $l->taux,
        ])->toArray()
        : []);
@endphp

<div class="card mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><span class="fas fa-list-ul me-2 text-primary"></span>Taxes exonérées</h5>
        <button type="button" id="btn-ajouter-ligne" class="btn btn-outline-primary btn-sm">
            <span class="fas fa-plus me-1"></span>Ajouter une taxe
        </button>
    </div>
    <div class="card-body p-0">
        @error('lignes') <div class="alert alert-danger m-3 mb-0 py-2 fs-9">{{ $message }}</div> @enderror
        <div class="table-responsive">
            <table class="table table-sm mb-0 fs-9 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:220px;">Nature de taxe</th>
                        <th style="min-width:140px;">Année d'application</th>
                        <th style="min-width:160px;">Taux d'exonération (%)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="lignes-exo"></tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted fs-10">
        Taux 100 % = exonération totale ; un taux inférieur applique un abattement partiel sur la taxe.
    </div>
</div>

<template id="tpl-ligne-exo">
    <tr>
        <td>
            <select name="lignes[__i__][nature_taxe_id]" class="form-select form-select-sm" required>
                <option value="">— Choisir —</option>
                @foreach ($naturesTaxe as $nt)
                    <option value="{{ $nt->id }}">{{ $nt->code }} — {{ $nt->libelle_court ?? $nt->libelle }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="lignes[__i__][annee_application]" min="2000" max="2100" value="{{ date('Y') }}" class="form-control form-control-sm" required></td>
        <td><input type="number" name="lignes[__i__][taux]" min="0" max="100" step="0.01" value="100" class="form-control form-control-sm text-end" required></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-suppr-ligne"><span class="fas fa-trash"></span></button></td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    const tbody = document.getElementById('lignes-exo');
    const tpl   = document.getElementById('tpl-ligne-exo').innerHTML;
    const existantes = @json(array_values($lignesExistantes));
    let index = 0;

    function ajouter(data = {}) {
        const tr = document.createElement('tr');
        tr.innerHTML = tpl.replace(/__i__/g, index).replace(/^\s*<tr>|<\/tr>\s*$/g, '');
        const set = (suffixe, val) => { const el = tr.querySelector(`[name$="[${suffixe}]"]`); if (el && val != null && val !== '') el.value = val; };
        set('nature_taxe_id', data.nature_taxe_id);
        set('annee_application', data.annee_application);
        set('taux', data.taux);
        tr.querySelector('.btn-suppr-ligne').addEventListener('click', () => tr.remove());
        tbody.appendChild(tr);
        index++;
    }

    document.getElementById('btn-ajouter-ligne').addEventListener('click', () => ajouter());

    if (existantes.length) { existantes.forEach(l => ajouter(l)); }
    else { ajouter(); }
}());
</script>
@endpush
