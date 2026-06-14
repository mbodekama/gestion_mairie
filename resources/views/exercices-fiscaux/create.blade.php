<x-app-layout title="Nouvel exercice fiscal">

<x-page-header titre="Créer un exercice fiscal" />

<form method="POST" action="{{ route('exercices-fiscaux.store') }}" novalidate>
    @csrf

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
                    <input type="number" name="annee" value="{{ old('annee', date('Y')) }}"
                           class="form-control form-control-lg @error('annee') is-invalid @enderror"
                           min="2000" max="2100" required>
                    @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Date de début <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut"
                           value="{{ old('date_debut', date('Y') . '-01-01') }}"
                           class="form-control form-control-lg @error('date_debut') is-invalid @enderror"
                           required>
                    @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Date de fin <span class="text-danger">*</span></label>
                    <input type="date" name="date_fin"
                           value="{{ old('date_fin', date('Y') . '-12-31') }}"
                           class="form-control form-control-lg @error('date_fin') is-invalid @enderror"
                           required>
                    @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('exercices-fiscaux.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'exercice
        </button>
    </div>

</form>

</x-app-layout>
