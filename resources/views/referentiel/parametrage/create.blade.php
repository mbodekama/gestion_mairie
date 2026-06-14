<x-app-layout title="Nouvelle nature de taxe">

<x-page-header titre="Créer une nature de taxe" />

<form method="POST" action="{{ route('referentiel.parametrage.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-balance-scale me-2 text-primary"></span>Nature de taxe
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fs-9">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-control form-control-lg @error('code') is-invalid @enderror text-uppercase"
                           maxlength="3" required placeholder="PAT">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Abrégé</label>
                    <input type="text" name="libelle_court" value="{{ old('libelle_court') }}"
                           class="form-control form-control-lg" maxlength="16">
                </div>
                <div class="col-md-5">
                    <label class="form-label fs-9">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                           class="form-control form-control-lg" maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Domaine de taxe <span class="text-danger">*</span></label>
                    <select name="domaine_taxe_id"
                            class="form-select form-select-lg @error('domaine_taxe_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($domaines as $d)
                            <option value="{{ $d->id }}"
                                    {{ old('domaine_taxe_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('domaine_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Catégorie impôt/taxe <span class="text-danger">*</span></label>
                    <select name="categorie_impot_taxe_id"
                            class="form-select form-select-lg @error('categorie_impot_taxe_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}"
                                    {{ old('categorie_impot_taxe_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_impot_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('referentiel.parametrage.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer
        </button>
    </div>
</form>

</x-app-layout>
