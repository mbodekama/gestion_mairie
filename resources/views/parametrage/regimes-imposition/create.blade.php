<x-app-layout title="Nouveau régime d'imposition">

<x-page-header titre="Créer un régime d'imposition" />

<form method="POST" action="{{ route('parametrage.regimes-imposition.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-sliders-h me-2 text-primary"></span>Régime d'imposition
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fs-9">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-control form-control-lg @error('code') is-invalid @enderror"
                           maxlength="3" required style="text-transform:uppercase" placeholder="Ex : RSI">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Libellé court</label>
                    <input type="text" name="libelle_court" value="{{ old('libelle_court') }}"
                           class="form-control form-control-lg @error('libelle_court') is-invalid @enderror"
                           maxlength="16" placeholder="Ex : RSI">
                    @error('libelle_court') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fs-9">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                           class="form-control form-control-lg @error('libelle') is-invalid @enderror"
                           maxlength="255" required placeholder="Ex : Régime Simplifié d'Imposition">
                    @error('libelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">CA borne inférieure (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="ca_borne_inf" value="{{ old('ca_borne_inf', '0') }}"
                           class="form-control form-control-lg @error('ca_borne_inf') is-invalid @enderror"
                           min="0" step="1" required>
                    @error('ca_borne_inf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">CA borne supérieure (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="ca_borne_sup" value="{{ old('ca_borne_sup', '0') }}"
                           class="form-control form-control-lg @error('ca_borne_sup') is-invalid @enderror"
                           min="0" step="1" required>
                    <small class="text-muted">0 = tranche ouverte (sans plafond).</small>
                    @error('ca_borne_sup') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('parametrage.regimes-imposition.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer le régime
        </button>
    </div>

</form>

</x-app-layout>
