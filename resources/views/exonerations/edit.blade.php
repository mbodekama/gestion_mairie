<x-app-layout title="Modifier l'exonération">

@php
    $contrib = $exoneration->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
@endphp

<x-page-header titre="Modifier l'exonération {{ $exoneration->numero }}" />

<form method="POST" action="{{ route('exonerations.update', $exoneration) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-percent me-2 text-primary"></span>Exonération
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Contribuable (figé) --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Contribuable</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $contrib?->numero_identifiant }} — {{ $nomContrib }}" readonly>
                </div>

                {{-- Type d'exonération --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Type d'exonération <span class="text-danger">*</span></label>
                    <select name="type_exoneration_id" class="form-select form-select-lg @error('type_exoneration_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($typesExoneration as $t)
                            <option value="{{ $t->id }}" {{ old('type_exoneration_id', $exoneration->type_exoneration_id) == $t->id ? 'selected' : '' }}>{{ $t->libelle }}</option>
                        @endforeach
                    </select>
                    @error('type_exoneration_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Référence du décret</label>
                    <input type="text" name="reference_decret" value="{{ old('reference_decret', $exoneration->reference_decret) }}"
                           class="form-control form-control-lg @error('reference_decret') is-invalid @enderror" maxlength="32">
                    @error('reference_decret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Date du décret</label>
                    <input type="date" name="date_decret" value="{{ old('date_decret', $exoneration->date_decret?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_decret') is-invalid @enderror">
                    @error('date_decret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Zone</label>
                    <input type="text" name="zone" value="{{ old('zone', $exoneration->zone) }}"
                           class="form-control form-control-lg @error('zone') is-invalid @enderror" maxlength="2">
                    @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Date de début</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', $exoneration->date_debut?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_debut') is-invalid @enderror">
                    @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Date de fin</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin', $exoneration->date_fin?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_fin') is-invalid @enderror">
                    @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('exonerations.show', $exoneration) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>

</form>

</x-app-layout>
