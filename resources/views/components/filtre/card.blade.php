@props(['action', 'reset', 'titre' => 'Filtrer les données selon vos critères'])

<div class="card mb-2">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <span class="fas fa-filter me-2 text-primary"></span>{{ $titre }}
        </h5>
        <button type="button"
                class="btn btn-sm btn-outline-secondary border-0 px-2"
                data-bs-toggle="collapse"
                data-bs-target="#filtreBody"
                aria-expanded="true"
                aria-controls="filtreBody"
                title="Enrouler / Dérouler">
            <span class="fas fa-chevron-up filtre-chevron"></span>
        </button>
    </div>

    <div class="collapse show" id="filtreBody">
        <div class="card-body py-2 px-3">
            <form method="POST" action="{{ $action }}" id="formFiltres">
                @csrf
                <div class="row g-2">
                    {{ $slot }}
                </div>
            </form>
        </div>
        <div class="card-footer py-2 d-flex justify-content-end gap-2 bg-body-tertiary border-top">
            <button type="submit" form="formFiltres" class="btn btn-primary btn-sm">
                <span class="fas fa-search me-1"></span>Rechercher les données
            </button>
            <a href="{{ $reset }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-undo me-1"></span>Réinitialiser le filtre
            </a>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .filtre-chevron {
        display: inline-block;
        transition: transform .25s ease;
    }
    [data-bs-target="#filtreBody"].collapsed .filtre-chevron {
        transform: rotate(180deg);
    }
</style>
@endpush
@endonce
