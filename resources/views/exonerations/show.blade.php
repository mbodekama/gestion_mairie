<x-app-layout :title="'Exonération — ' . $exoneration->numero">

@php
    $contrib = $exoneration->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
    $active = (!$exoneration->date_fin || $exoneration->date_fin->isFuture())
           && ($exoneration->date_debut && $exoneration->date_debut->isPast());
@endphp

<x-page-header titre="Exonération {{ $exoneration->numero }}" />

@if (session('success'))
    <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0">
            <span class="fas fa-percent me-2 text-primary"></span>{{ $exoneration->numero }}
            @if ($active)
                <span class="badge bg-success ms-1">Active</span>
            @elseif ($exoneration->date_fin && $exoneration->date_fin->isPast())
                <span class="badge bg-secondary ms-1">Expirée</span>
            @else
                <span class="badge bg-info ms-1">À venir</span>
            @endif
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('exonerations.edit', $exoneration) }}" class="btn btn-outline-warning btn-sm">
                <span class="fas fa-edit me-1"></span>Modifier
            </a>
            <a href="{{ route('exonerations.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-1"></span>Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted fs-9">Contribuable</div>
                <div class="fw-bold">
                    @if ($contrib)<a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>@else — @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">N° Identifiant</div>
                <div class="fw-bold">{{ $contrib?->numero_identifiant ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Type d'exonération</div>
                <div class="fw-bold">{{ $exoneration->typeExoneration?->libelle ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Référence du décret</div>
                <div class="fw-bold">{{ $exoneration->reference_decret ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Date du décret</div>
                <div class="fw-bold">{{ $exoneration->date_decret?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Zone</div>
                <div class="fw-bold">{{ $exoneration->zone ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Période</div>
                <div class="fw-bold">
                    {{ $exoneration->date_debut?->format('d/m/Y') ?? '—' }} → {{ $exoneration->date_fin?->format('d/m/Y') ?? '—' }}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <form method="POST" action="{{ route('exonerations.destroy', $exoneration) }}"
              onsubmit="return confirm('Supprimer l\'exonération {{ $exoneration->numero }} ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <span class="fas fa-trash me-1"></span>Supprimer
            </button>
        </form>
    </div>
</div>

</x-app-layout>
