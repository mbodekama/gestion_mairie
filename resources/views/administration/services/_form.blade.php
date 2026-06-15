@php
    $val = fn ($champ, $defaut = null) => old($champ, $service?->{$champ} ?? $defaut);
@endphp

<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-building me-2 text-primary"></span>Service</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fs-9">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" value="{{ $val('code') }}" maxlength="6"
                       class="form-control form-control-lg @error('code') is-invalid @enderror"
                       style="text-transform:uppercase" required>
                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fs-9">Libellé <span class="text-danger">*</span></label>
                <input type="text" name="libelle" value="{{ $val('libelle') }}" maxlength="128"
                       class="form-control form-control-lg @error('libelle') is-invalid @enderror" required>
                @error('libelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fs-9">Sigle</label>
                <input type="text" name="sigle" value="{{ $val('sigle') }}" maxlength="64"
                       class="form-control form-control-lg @error('sigle') is-invalid @enderror">
                @error('sigle') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fs-9">Département</label>
                <select name="departement_service_id" class="form-select form-select-lg @error('departement_service_id') is-invalid @enderror">
                    <option value="">— Aucun —</option>
                    @foreach ($departements as $d)
                        <option value="{{ $d->id }}" {{ $val('departement_service_id') == $d->id ? 'selected' : '' }}>{{ $d->libelle }}</option>
                    @endforeach
                </select>
                @error('departement_service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>
