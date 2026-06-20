<x-app-layout :title="'Objectif ' . $objectif->annee">

@php
    $tauxRevision = ($objectif->montant_revise && $objectif->montant > 0)
        ? round((($objectif->montant_revise - $objectif->montant) / $objectif->montant) * 100, 1)
        : null;
@endphp

<x-page-header titre="Pilotage — Objectif de recouvrement {{ $objectif->annee }}" />

<div class="row g-3 mb-3">
    {{-- Fiche objectif --}}
    <div class="col-lg-7">
        <div class="card h-100 card-section">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-bullseye me-2 text-primary"></span>Objectif {{ $objectif->annee }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('pilotage.objectifs.edit', $objectif) }}"
                       class="btn btn-outline-primary btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    <a href="{{ route('pilotage.objectifs.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Liste
                    </a>
                </div>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">Exercice fiscal</dt>
                    <dd class="col-7 fw-bold">
                        {{ $objectif->exerciceFiscal?->annee ?? $objectif->annee }}
                        @if ($objectif->exerciceFiscal?->cloture)
                            <span class="badge bg-secondary ms-1">Clôturé</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-600">Période couverte</dt>
                    <dd class="col-7">
                        @if ($objectif->periode_debut && $objectif->periode_fin)
                            du {{ $objectif->periode_debut->format('d/m/Y') }}
                            au {{ $objectif->periode_fin->format('d/m/Y') }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-600">Collectivité</dt>
                    <dd class="col-7">{{ $objectif->collectivite?->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">Montant objectif</dt>
                    <dd class="col-7 fw-semi-bold">{{ number_format((float) $objectif->montant, 0, ',', ' ') }} FCFA</dd>

                    <dt class="col-5 text-600">Montant révisé</dt>
                    <dd class="col-7">
                        @if ($objectif->montant_revise !== null)
                            {{ number_format((float) $objectif->montant_revise, 0, ',', ' ') }} FCFA
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-600">Taux de révision</dt>
                    <dd class="col-7">
                        @if ($tauxRevision !== null)
                            <span class="badge {{ $tauxRevision >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $tauxRevision >= 0 ? '+' : '' }}{{ $tauxRevision }} %
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Créé le {{ $objectif->created_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="col-lg-5">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-cog me-2 text-primary"></span>Actions
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pilotage.objectifs.destroy', $objectif) }}"
                      onsubmit="return confirm('Supprimer l\'objectif {{ $objectif->annee }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <span class="fas fa-trash me-1"></span>Supprimer cet objectif
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
