<x-app-layout :title="'Modifier exercice — ' . $exerciceFiscal->annee">

<x-page-header titre="Modifier l'exercice fiscal {{ $exerciceFiscal->annee }}" />

@php
    $aOperations = $exerciceFiscal->aDesOperations();
    $verrouille  = $exerciceFiscal->cloture || $aOperations;
@endphp

@if ($exerciceFiscal->cloture)
    <div class="alert alert-danger py-2 fs-9">
        <span class="fas fa-lock me-1"></span>
        Cet exercice est clôturé et ne peut plus être modifié.
    </div>
@elseif ($aOperations)
    <div class="alert alert-warning py-2 fs-9">
        <span class="fas fa-lock me-1"></span>
        Cet exercice comporte des émissions ou des recouvrements : il ne peut plus être modifié ni supprimé.
    </div>
@endif

<form method="POST" action="{{ route('exercices-fiscaux.update', $exerciceFiscal) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-calendar-alt me-2 text-primary"></span>Exercice fiscal
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fs-9">Année <span class="text-danger">*</span></label>
                    <input type="number" name="annee"
                           value="{{ old('annee', $exerciceFiscal->annee) }}"
                           class="form-control form-control-lg @error('annee') is-invalid @enderror"
                           min="2000" max="2100"
                           {{ $verrouille ? 'disabled' : 'required' }}>
                    @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Date de début <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut"
                           value="{{ old('date_debut', $exerciceFiscal->date_debut?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_debut') is-invalid @enderror"
                           {{ $verrouille ? 'disabled' : 'required' }}>
                    @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Date de fin <span class="text-danger">*</span></label>
                    <input type="date" name="date_fin"
                           value="{{ old('date_fin', $exerciceFiscal->date_fin?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_fin') is-invalid @enderror"
                           {{ $verrouille ? 'disabled' : 'required' }}>
                    @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">État</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $exerciceFiscal->cloture ? 'Clôturé' : 'Ouvert' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('exercices-fiscaux.show', $exerciceFiscal) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        @if (!$verrouille)
            <button type="submit" class="btn btn-primary btn-lg">
                <span class="fas fa-save me-1"></span>Enregistrer
            </button>
        @endif
    </div>

</form>

</x-app-layout>
