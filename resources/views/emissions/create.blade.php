<x-app-layout title="Nouvelle émission de taxe">

<x-page-header titre="Créer une émission de taxe"
               sous-titre="Étape 1 — Identifier l'établissement" />

@if (!empty($erreurCode))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        <span class="fas fa-exclamation-triangle me-1"></span>{{ $erreurCode }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="GET" action="{{ route('emissions.create') }}">
    @if (!empty($exerciceFiscalId))
        <input type="hidden" name="exercice_fiscal_id" value="{{ $exerciceFiscalId }}">
    @endif

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-store me-2 text-primary"></span>Identification de l'établissement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fs-9">
                        Identification (saisissez le Num établissement) <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code" value="{{ $code ?? '' }}"
                           class="form-control form-control-lg"
                           placeholder="Ex : ET20260001"
                           autofocus required style="text-transform:uppercase">
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
