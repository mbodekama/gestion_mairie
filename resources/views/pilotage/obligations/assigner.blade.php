<x-app-layout title="Obligations fiscales">

@php
    $nomContrib = $contribuable->type_personne === 'PP'
        ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
        : ($contribuable->raison_sociale ?? '—');
@endphp

<x-page-header titre="Obligations fiscales du contribuable"
               sous-titre="Étape 2 — Assigner ou retirer les obligations" />



{{-- ===== Card Contribuable ===== --}}
<div class="card mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <span class="fas fa-user me-2 text-primary"></span>{{ $nomContrib }}
            <span class="badge bg-secondary ms-1">{{ $contribuable->type_personne }}</span>
        </h5>
        <a href="{{ route('contribuables.show', $contribuable) }}" class="btn btn-outline-secondary btn-sm">
            <span class="fas fa-arrow-left me-1"></span>Retour à la fiche
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3 fs-9">
            <div class="col-md-3">
                <div class="text-muted">N° Identifiant</div>
                <div class="fw-bold">{{ $contribuable->numero_identifiant ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">N° Compte</div>
                <div class="fw-bold">{{ $contribuable->numero_compte ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Téléphone</div>
                <div class="fw-bold">{{ $contribuable->telephone ?? $contribuable->cellulaire ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Obligations assignées</div>
                <div class="fw-bold">{{ $obligations->count() }} / {{ $naturesTaxe->count() }}</div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('pilotage.obligations.store') }}" id="form-obligations">
    @csrf
    <input type="hidden" name="code" value="{{ $code }}">

    {{-- ===== Card Obligations ===== --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-tasks me-2 text-primary"></span>
                Natures de taxe
                <span class="badge bg-secondary ms-2">{{ $naturesTaxe->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N°</th>
                            <th>Code nature</th>
                            <th>Libellé taxe</th>
                            <th style="min-width:200px;">Périodicité</th>
                            <th class="text-center" style="width:120px;">
                                Assignée
                                <div class="form-check d-inline-block ms-1">
                                    <input type="checkbox" id="check-all" class="form-check-input" title="Tout cocher / décocher">
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($naturesTaxe as $i => $nature)
                            @php
                                $obligation = $obligations->get($nature->id);
                                $assignee   = $obligation !== null;
                            @endphp
                            <tr class="ligne-obligation {{ $assignee ? 'table-success' : '' }}">
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $nature->code }}</span></td>
                                <td class="fw-semi-bold">{{ $nature->libelle }}</td>
                                <td>
                                    <select name="periodicite[{{ $nature->id }}]"
                                            class="form-select form-select-sm select-periodicite">
                                        <option value="">— Aucune —</option>
                                        @foreach ($periodicites as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (int) ($obligation->periodicite_id ?? 0) === (int) $p->id ? 'selected' : '' }}>
                                                {{ $p->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="obligations[]" value="{{ $nature->id }}"
                                           class="form-check-input ligne-check" {{ $assignee ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted fs-10">
            <span class="fas fa-info-circle me-1"></span>
            Cochez une nature pour assigner l'obligation au contribuable, décochez-la pour la retirer.
            La périodicité s'applique aux natures cochées.
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('contribuables.show', $contribuable) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-success btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer les obligations
        </button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const checkAll = document.getElementById('check-all');
    const lignes   = Array.from(document.querySelectorAll('.ligne-check'));

    // Souligne visuellement les lignes assignées (cochées)
    function rafraichirLigne(check) {
        check.closest('tr')?.classList.toggle('table-success', check.checked);
    }

    checkAll?.addEventListener('change', () => {
        lignes.forEach(c => { c.checked = checkAll.checked; rafraichirLigne(c); });
    });

    lignes.forEach(c => c.addEventListener('change', () => rafraichirLigne(c)));
}());
</script>
@endpush

</x-app-layout>
