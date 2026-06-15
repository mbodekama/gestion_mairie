@php
    $val = fn ($champ, $defaut = null) => old($champ, $agent?->{$champ} ?? $defaut);
@endphp

<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-user-tie me-2 text-primary"></span>Identité</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fs-9">Matricule <span class="text-danger">*</span></label>
                <input type="text" name="matricule" value="{{ $val('matricule') }}" maxlength="32"
                       class="form-control form-control-lg @error('matricule') is-invalid @enderror"
                       style="text-transform:uppercase" required>
                @error('matricule') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fs-9">Nom</label>
                <input type="text" name="nom" value="{{ $val('nom') }}" maxlength="64"
                       class="form-control form-control-lg @error('nom') is-invalid @enderror">
                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-5">
                <label class="form-label fs-9">Prénoms</label>
                <input type="text" name="prenoms" value="{{ $val('prenoms') }}" maxlength="128"
                       class="form-control form-control-lg @error('prenoms') is-invalid @enderror">
                @error('prenoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-sitemap me-2 text-primary"></span>Affectation</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fs-9">Fonction</label>
                <select name="fonction_agent_id" class="form-select form-select-lg @error('fonction_agent_id') is-invalid @enderror">
                    <option value="">— Aucune —</option>
                    @foreach ($fonctions as $f)
                        <option value="{{ $f->id }}" {{ $val('fonction_agent_id') == $f->id ? 'selected' : '' }}>{{ $f->libelle }}</option>
                    @endforeach
                </select>
                @error('fonction_agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fs-9">Grade</label>
                <select name="grade_agent_id" class="form-select form-select-lg @error('grade_agent_id') is-invalid @enderror">
                    <option value="">— Aucun —</option>
                    @foreach ($grades as $g)
                        <option value="{{ $g->id }}" {{ $val('grade_agent_id') == $g->id ? 'selected' : '' }}>{{ $g->libelle }}</option>
                    @endforeach
                </select>
                @error('grade_agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fs-9">Service</label>
                <select name="service_id" class="form-select form-select-lg @error('service_id') is-invalid @enderror">
                    <option value="">— Aucun —</option>
                    @foreach ($services as $s)
                        <option value="{{ $s->id }}" {{ $val('service_id') == $s->id ? 'selected' : '' }}>{{ $s->libelle }}</option>
                    @endforeach
                </select>
                @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fs-9">Supérieur hiérarchique</label>
                <select name="superieur_id" class="form-select form-select-lg @error('superieur_id') is-invalid @enderror">
                    <option value="">— Aucun —</option>
                    @foreach ($superieurs as $sup)
                        <option value="{{ $sup->id }}" {{ $val('superieur_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->matricule }} — {{ trim(($sup->nom ?? '') . ' ' . ($sup->prenoms ?? '')) }}
                        </option>
                    @endforeach
                </select>
                @error('superieur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch fs-7">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" name="actif" value="1" id="actif" class="form-check-input"
                           {{ $val('actif', true) ? 'checked' : '' }}>
                    <label for="actif" class="form-check-label fs-9">Agent actif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fs-9">Observation</label>
                <input type="text" name="observation" value="{{ $val('observation') }}" maxlength="255"
                       class="form-control form-control-lg @error('observation') is-invalid @enderror">
                @error('observation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>
