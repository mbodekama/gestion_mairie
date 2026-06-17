<x-app-layout :title="'Commune — ' . $commune->libelle">

<x-page-header titre="Commune {{ $commune->libelle }}" />


<div class="row g-3 mb-3">
    {{-- Fiche commune --}}
    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-map me-2 text-primary"></span>Commune
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('referentiel.territorial.edit', $commune) }}"
                       class="btn btn-outline-primary btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    <a href="{{ route('referentiel.territorial.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Liste
                    </a>
                </div>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">Code</dt>
                    <dd class="col-7 fw-semi-bold font-monospace">{{ $commune->code }}</dd>

                    <dt class="col-5 text-600">Libellé</dt>
                    <dd class="col-7 fw-bold">{{ $commune->libelle }}</dd>

                    <dt class="col-5 text-600">Sous-préfecture</dt>
                    <dd class="col-7">{{ $commune->sousPrefecture?->libelle ?? '—' }}</dd>

                    @if ($commune->sousPrefecture?->departement)
                        <dt class="col-5 text-600">Département</dt>
                        <dd class="col-7">{{ $commune->sousPrefecture->departement->libelle }}</dd>
                    @endif

                    <dt class="col-5 text-600">Population</dt>
                    <dd class="col-7">
                        {{ $commune->population ? number_format($commune->population, 0, ',', ' ') . ' hab.' : '—' }}
                    </dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $commune->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-chart-bar me-2 text-primary"></span>Utilisation
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-4 text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $nbEtablissements }}</div>
                        <div class="fs-10 text-600 text-uppercase">Établissements</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fs-4 fw-bold text-info">{{ $nbQuartiers }}</div>
                        <div class="fs-10 text-600 text-uppercase">Quartiers</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fs-4 fw-bold text-success">{{ $nbZonesFiscales }}</div>
                        <div class="fs-10 text-600 text-uppercase">Zones fiscales</div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    @if ($nbEtablissements === 0)
                        <form method="POST" action="{{ route('referentiel.territorial.destroy', $commune) }}"
                              onsubmit="return confirm('Supprimer la commune {{ $commune->libelle }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <span class="fas fa-trash me-1"></span>Supprimer cette commune
                            </button>
                        </form>
                    @else
                        <p class="text-500 fs-9 mb-0 text-center">
                            <span class="fas fa-lock me-1"></span>
                            Suppression impossible : {{ $nbEtablissements }} établissement(s) actif(s)
                        </p>
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-store me-1"></span>{{ $nbEtablissements }} établissement(s) rattaché(s)
            </div>
        </div>
    </div>
</div>

</x-app-layout>
