<x-app-layout title="Obligation fiscale">

@php
    $contrib = $obligation->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
@endphp

<x-page-header titre="Obligation fiscale" />

<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0">
            <span class="fas fa-tasks me-2 text-primary"></span>{{ $nomContrib }}
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('pilotage.obligations.edit', $obligation) }}" class="btn btn-outline-warning btn-sm">
                <span class="fas fa-edit me-1"></span>Modifier
            </a>
            <a href="{{ route('pilotage.obligations.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-1"></span>Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted fs-9">Contribuable</div>
                <div class="fw-bold">
                    @if ($contrib)
                        <a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>
                    @else — @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">N° Identifiant</div>
                <div class="fw-bold">{{ $contrib?->numero_identifiant ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Nature de taxe</div>
                <div class="fw-bold">{{ $obligation->natureTaxe?->libelle ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Périodicité</div>
                <div class="fw-bold">{{ $obligation->periodicite?->libelle ?? '— Aucune —' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Créée le</div>
                <div class="fw-bold">{{ $obligation->created_at?->format('d/m/Y à H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <form method="POST" action="{{ route('pilotage.obligations.destroy', $obligation) }}"
              onsubmit="return confirm('Supprimer cette obligation fiscale ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <span class="fas fa-trash me-1"></span>Supprimer
            </button>
        </form>
    </div>
</div>

</x-app-layout>
