<x-app-layout title="Nouvelle commune">

<x-page-header titre="Créer une commune" />

<form method="POST" action="{{ route('referentiel.territorial.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-map me-2 text-primary"></span>Commune
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fs-9">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-control form-control-lg @error('code') is-invalid @enderror text-uppercase"
                           maxlength="3" required placeholder="ABJ">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label fs-9">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                           class="form-control form-control-lg @error('libelle') is-invalid @enderror"
                           maxlength="255" required>
                    @error('libelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Sous-préfecture</label>
                    <select name="sous_prefecture_id" class="form-select form-select-lg">
                        <option value="">— Choisir —</option>
                        @foreach ($sousPrefectures as $sp)
                            <option value="{{ $sp->id }}"
                                    {{ old('sous_prefecture_id') == $sp->id ? 'selected' : '' }}>
                                {{ $sp->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Population</label>
                    <input type="number" name="population" value="{{ old('population') }}"
                           class="form-control form-control-lg" min="0">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('referentiel.territorial.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer
        </button>
    </div>
</form>

</x-app-layout>
