<x-app-layout title="Nouveau règlement">

<x-page-header titre="Enregistrer un règlement" sous-titre="Étape 1 — Identifier le redevable" />

@if (!empty($erreurCode))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        <span class="fas fa-exclamation-triangle me-1"></span>{{ $erreurCode }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="GET" action="{{ route('recouvrements.create') }}">
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-user-tag me-2 text-primary"></span>Identification du redevable
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fs-9">
                        Code contribuable ou établissement <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code" value="{{ $code ?? '' }}"
                           class="form-control form-control-lg"
                           placeholder="Ex : CI2024000001 (contribuable) ou ET20260001 (établissement)"
                           autofocus required style="text-transform:uppercase">
                    <small class="text-muted">
                        Numéro identifiant ou numéro de compte du contribuable, ou numéro d'établissement.
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
