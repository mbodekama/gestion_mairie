<x-app-layout title="Obligations fiscales d'un contribuable">

<x-page-header titre="Obligations fiscales d'un contribuable"
               sous-titre="Étape 1 — Identifier le contribuable" />

@if (!empty($erreurCode))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        <span class="fas fa-exclamation-triangle me-1"></span>{{ $erreurCode }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="GET" action="{{ route('pilotage.obligations.create') }}">
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-user-tag me-2 text-primary"></span>Identification du contribuable
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fs-9">
                        Numéro du contribuable <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code" value="{{ $code ?? '' }}"
                           class="form-control form-control-lg"
                           placeholder="Ex : CI2024000001 (n° identifiant) ou n° de compte"
                           autofocus required style="text-transform:uppercase">
                    <small class="text-muted">
                        Numéro identifiant ou numéro de compte du contribuable.
                    </small>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <span class="fas fa-arrow-right me-1"></span>Valider
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

</x-app-layout>
