<x-app-layout :title="'Modifier nature taxe — ' . $natureTaxe->code">

<x-page-header titre="Modifier : {{ $natureTaxe->libelle_court ?? $natureTaxe->code }}" />

<form method="POST" action="{{ route('referentiel.parametrage.update', $natureTaxe) }}" novalidate>
    @csrf @method('PUT')

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
                    <input type="text" name="code" value="{{ old('code', $natureTaxe->code) }}"
                           class="form-control form-control-lg @error('code') is-invalid @enderror text-uppercase"
                           maxlength="3" required placeholder="PAT">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Abrégé</label>
                    <input type="text" name="libelle_court" value="{{ old('libelle_court', $natureTaxe->libelle_court) }}"
                           class="form-control form-control-lg" maxlength="16">
                </div>
                <div class="col-md-5">
                    <label class="form-label fs-9">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle', $natureTaxe->libelle) }}"
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
                                    {{ old('domaine_taxe_id', $natureTaxe->domaine_taxe_id) == $d->id ? 'selected' : '' }}>
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
                                    {{ old('categorie_impot_taxe_id', $natureTaxe->categorie_impot_taxe_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_impot_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('referentiel.parametrage.show', $natureTaxe) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-arrow-left me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

</x-app-layout>
