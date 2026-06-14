<x-app-layout title="Nouvelle activité">

<x-page-header titre="Créer une activité" />

<form method="POST" action="{{ route('referentiel.activites.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-industry me-2 text-primary"></span>Activité économique
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fs-9">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-control form-control-lg @error('code') is-invalid @enderror text-uppercase"
                           maxlength="5" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-9">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                           class="form-control form-control-lg @error('libelle') is-invalid @enderror"
                           maxlength="1000" required>
                    @error('libelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Secteur d'activité <span class="text-danger">*</span></label>
                    <select name="secteur_activite_id"
                            class="form-select form-select-lg @error('secteur_activite_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($secteurs as $s)
                            <option value="{{ $s->id }}"
                                    {{ old('secteur_activite_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('secteur_activite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Catégorie d'activité <span class="text-danger">*</span></label>
                    <select name="categorie_activite_id"
                            class="form-select form-select-lg @error('categorie_activite_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}"
                                    {{ old('categorie_activite_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_activite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('referentiel.activites.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer
        </button>
    </div>
</form>

</x-app-layout>
