<x-app-layout title="Nouvelle exonération">

<x-page-header titre="Créer une exonération" />

<form method="POST" action="{{ route('exonerations.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-percent me-2 text-primary"></span>Exonération
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Contribuable --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Matricule contribuable <span class="text-danger">*</span></label>
                    @if ($contribuable)
                        <input type="hidden" name="contribuable_id" value="{{ $contribuable->id }}">
                        <input type="text" class="form-control form-control-lg bg-light"
                               value="{{ $contribuable->numero_identifiant }} — {{ $contribuable->type_personne === 'PP' ? trim($contribuable->nom . ' ' . $contribuable->prenoms) : $contribuable->raison_sociale }}"
                               readonly>
                    @else
                        <input type="text" name="numero_identifiant" value="{{ old('numero_identifiant') }}"
                               class="form-control form-control-lg @error('numero_identifiant') is-invalid @enderror @error('contribuable_id') is-invalid @enderror"
                               placeholder="Ex : CI2024000001" maxlength="12" style="text-transform:uppercase">
                        @error('numero_identifiant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @error('contribuable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Type d'exonération --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Type d'exonération <span class="text-danger">*</span></label>
                    <select name="type_exoneration_id" class="form-select form-select-lg @error('type_exoneration_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($typesExoneration as $t)
                            <option value="{{ $t->id }}" {{ old('type_exoneration_id') == $t->id ? 'selected' : '' }}>{{ $t->libelle }}</option>
                        @endforeach
                    </select>
                    @error('type_exoneration_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Référence décret --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Référence du décret</label>
                    <input type="text" name="reference_decret" value="{{ old('reference_decret') }}"
                           class="form-control form-control-lg @error('reference_decret') is-invalid @enderror" maxlength="32">
                    @error('reference_decret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date décret --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Date du décret</label>
                    <input type="date" name="date_decret" value="{{ old('date_decret') }}"
                           class="form-control form-control-lg @error('date_decret') is-invalid @enderror">
                    @error('date_decret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Zone --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Zone</label>
                    <input type="text" name="zone" value="{{ old('zone') }}"
                           class="form-control form-control-lg @error('zone') is-invalid @enderror" maxlength="2"
                           placeholder="Ex : 1">
                    @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date début --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de début</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                           class="form-control form-control-lg @error('date_debut') is-invalid @enderror">
                    @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date fin --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de fin</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                           class="form-control form-control-lg @error('date_fin') is-invalid @enderror">
                    @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $contribuable ? route('contribuables.show', $contribuable) : route('exonerations.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'exonération
        </button>
    </div>

</form>

</x-app-layout>
