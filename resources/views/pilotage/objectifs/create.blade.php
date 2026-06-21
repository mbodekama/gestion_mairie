<x-app-layout title="Nouvel objectif de recouvrement">

<x-page-header titre="Pilotage — Nouvel objectif de recouvrement" />

<form method="POST" action="{{ route('pilotage.objectifs.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-bullseye me-2 text-primary"></span>Objectif de recouvrement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Exercice fiscal --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Exercice fiscal <span class="text-danger">*</span></label>
                    <select name="exercice_fiscal_id" id="exercice-select"
                            class="form-select form-select-lg @error('exercice_fiscal_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($exercices as $ex)
                            <option value="{{ $ex->id }}"
                                    data-debut="{{ $ex->date_debut->format('Y-m-d') }}"
                                    data-fin="{{ $ex->date_fin->format('Y-m-d') }}"
                                    {{ old('exercice_fiscal_id') == $ex->id ? 'selected' : '' }}>
                                {{ $ex->annee }} ({{ $ex->date_debut->format('d/m/Y') }} → {{ $ex->date_fin->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('exercice_fiscal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if ($exercices->isEmpty())
                        <div class="form-text text-danger fs-10">Aucun exercice ouvert : ouvrez un exercice avant de définir un objectif.</div>
                    @endif
                </div>

                {{-- Période couverte --}}
                <x-date-picker name="periode_debut" label="Période — du" col="col-md-4"
                               :required="true" label-class="fs-9" />
                <x-date-picker name="periode_fin" label="Période — au" col="col-md-4"
                               :required="true" label-class="fs-9" />

                {{-- Montants --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant objectif (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant" value="{{ old('montant') }}"
                           class="form-control form-control-lg text-end @error('montant') is-invalid @enderror"
                           min="0" step="0.01" required placeholder="0">
                    @error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant révisé (FCFA)</label>
                    <input type="number" name="montant_revise" value="{{ old('montant_revise') }}"
                           class="form-control form-control-lg text-end @error('montant_revise') is-invalid @enderror"
                           min="0" step="0.01" placeholder="Optionnel">
                    @error('montant_revise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text fs-10">À renseigner en cas de révision de l'objectif en cours d'exercice.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('pilotage.objectifs.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer
        </button>
    </div>
</form>

@push('scripts')
    <x-objectifs.periode-script />
@endpush

</x-app-layout>
