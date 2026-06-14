<x-app-layout title="Fiche barème de taxe">

@php
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
    $taux = rtrim(rtrim(number_format((float) $baremeTaxe->taux, 4, ',', ' '), '0'), ',');
@endphp

<x-page-header titre="Barème de taxe" />

<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0">
            <span class="fas fa-percent me-2 text-primary"></span>
            {{ $baremeTaxe->natureTaxe?->libelle_court ?? $baremeTaxe->natureTaxe?->libelle ?? 'Barème' }}
            <span class="badge bg-secondary ms-2">{{ $baremeTaxe->categorieActivite?->libelle ?? 'Toutes catégories' }}</span>
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('parametrage.baremes-taxe.edit', $baremeTaxe) }}" class="btn btn-outline-warning btn-sm">
                <span class="fas fa-edit me-1"></span>Modifier
            </a>
            <a href="{{ route('parametrage.baremes-taxe.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-1"></span>Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted fs-9">Nature de taxe</div>
                <div class="fw-bold">{{ $baremeTaxe->natureTaxe?->libelle ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Catégorie d'activité</div>
                <div class="fw-bold">{{ $baremeTaxe->categorieActivite?->libelle ?? 'Toutes (barème général)' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Périodicité</div>
                <div class="fw-bold">{{ $baremeTaxe->periodicite?->libelle ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Tranche de chiffre d'affaires</div>
                <div class="fw-bold">
                    {{ $fcfa($baremeTaxe->ca_borne_inf) }}
                    @if ((float) $baremeTaxe->ca_borne_sup === 0.0)
                        <span class="badge bg-info">et plus</span>
                    @else
                        → {{ $fcfa($baremeTaxe->ca_borne_sup) }}
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted fs-9">Taux</div>
                <div class="fw-bold fs-6 text-primary">{{ $taux }} % du CA</div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <form method="POST" action="{{ route('parametrage.baremes-taxe.destroy', $baremeTaxe) }}"
              onsubmit="return confirm('Confirmer la suppression de ce barème ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <span class="fas fa-trash me-1"></span>Supprimer ce barème
            </button>
        </form>
    </div>
</div>

</x-app-layout>
