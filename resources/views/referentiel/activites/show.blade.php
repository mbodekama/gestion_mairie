<x-app-layout :title="'Activité — ' . $activite->libelle">

<x-page-header titre="Activité : {{ $activite->libelle }}" />


<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-industry me-2 text-primary"></span>Activité économique
                </h5>
                <div class="d-flex gap-2">
                    @can('ACTIVITE_GERER')
                    <a href="{{ route('referentiel.activites.edit', $activite) }}"
                       class="btn btn-outline-primary btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    @endcan
                    <a href="{{ route('referentiel.activites.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Liste
                    </a>
                </div>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">Code</dt>
                    <dd class="col-7 fw-semi-bold font-monospace">{{ $activite->code }}</dd>

                    <dt class="col-5 text-600">Libellé</dt>
                    <dd class="col-7 fw-bold">{{ $activite->libelle }}</dd>

                    <dt class="col-5 text-600">Secteur d'activité</dt>
                    <dd class="col-7">{{ $activite->secteurActivite?->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">Catégorie d'activité</dt>
                    <dd class="col-7">{{ $activite->categorieActivite?->libelle ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $activite->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-chart-bar me-2 text-primary"></span>Utilisation
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-2">
                    <div class="fs-3 fw-bold text-primary">{{ $nbEtablissements }}</div>
                    <div class="fs-9 text-600">établissement(s) actif(s) rattaché(s) à cette activité</div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    @if ($nbEtablissements === 0)
                        @can('ACTIVITE_GERER')
                        <form method="POST" action="{{ route('referentiel.activites.destroy', $activite) }}"
                              onsubmit="return confirm('Supprimer l\'activité {{ addslashes($activite->libelle) }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <span class="fas fa-trash me-1"></span>Supprimer cette activité
                            </button>
                        </form>
                        @endcan
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
