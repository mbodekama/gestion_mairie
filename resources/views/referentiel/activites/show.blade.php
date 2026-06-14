<x-app-layout :title="'Activité — ' . $activite->libelle">

<x-page-header titre="Activité : {{ $activite->libelle }}" />

@if (session('success'))
    <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <span class="fas fa-industry me-2 text-primary"></span>Activité économique
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('referentiel.activites.edit', $activite) }}"
                       class="btn btn-outline-primary btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
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
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0">
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
                        <form method="POST" action="{{ route('referentiel.activites.destroy', $activite) }}"
                              onsubmit="return confirm('Supprimer l\'activité {{ addslashes($activite->libelle) }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <span class="fas fa-trash me-1"></span>Supprimer cette activité
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
        </div>
    </div>
</div>

</x-app-layout>
