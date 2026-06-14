<x-app-layout :title="'Modifier règlement — ' . $recouvrement->numero_reglement">

<x-page-header titre="Modifier le règlement {{ $recouvrement->numero_reglement }}" />

<form method="POST" action="{{ route('recouvrements.update', $recouvrement) }}" novalidate>
    @csrf @method('PUT')

    {{-- Rappel émission --}}
    @if ($recouvrement->emissionTaxe)
        @php
            $emission   = $recouvrement->emissionTaxe;
            $contrib    = $emission->etablissement?->contribuable;
            $nomContrib = $contrib
                ? ($contrib->type_personne === 'PP'
                    ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                    : ($contrib->raison_sociale ?? ''))
                : '—';
        @endphp
        <div class="alert alert-info py-2 fs-9 mb-3">
            <strong>Émission :</strong> {{ $emission->numero_emission }}
            — <strong>{{ $emission->natureTaxe?->libelle_court ?? '—' }}</strong>
            — {{ $nomContrib }}
            — Étab. {{ $emission->etablissement?->numero ?? '—' }}
        </div>
    @endif

    {{-- Dates et montants --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-coins me-2 text-primary"></span>Montants et date
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de règlement <span class="text-danger">*</span></label>
                    <input type="date" name="date_reglement"
                           value="{{ old('date_reglement', $recouvrement->date_reglement?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_reglement') is-invalid @enderror"
                           required>
                    @error('date_reglement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Montant versé (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant"
                           value="{{ old('montant', $recouvrement->montant) }}"
                           class="form-control form-control-lg @error('montant') is-invalid @enderror"
                           min="0" step="1" required>
                    @error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Montant imputé (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant_impute"
                           value="{{ old('montant_impute', $recouvrement->montant_impute) }}"
                           class="form-control form-control-lg @error('montant_impute') is-invalid @enderror"
                           min="0" step="1" required>
                    @error('montant_impute') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Mois imputé</label>
                    <input type="number" name="mois_impute"
                           value="{{ old('mois_impute', $recouvrement->mois_impute) }}"
                           class="form-control form-control-lg" min="1" max="12">
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">N° Quittance</label>
                    <input type="text" name="numero_quittance"
                           value="{{ old('numero_quittance', $recouvrement->numero_quittance) }}"
                           class="form-control form-control-lg" maxlength="64">
                </div>
            </div>
        </div>
    </div>

    {{-- Mode de paiement --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-credit-card me-2 text-primary"></span>Mode de paiement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Mode de règlement <span class="text-danger">*</span></label>
                    <select name="mode_reglement_id"
                            class="form-select form-select-lg @error('mode_reglement_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($modes as $m)
                            <option value="{{ $m->id }}"
                                    {{ old('mode_reglement_id', $recouvrement->mode_reglement_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('mode_reglement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Type de règlement <span class="text-danger">*</span></label>
                    <select name="type_reglement_id"
                            class="form-select form-select-lg @error('type_reglement_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($types as $t)
                            <option value="{{ $t->id }}"
                                    {{ old('type_reglement_id', $recouvrement->type_reglement_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_reglement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Banque</label>
                    <select name="banque_id" class="form-select form-select-lg">
                        <option value="">— Choisir —</option>
                        @foreach ($banques as $b)
                            <option value="{{ $b->id }}"
                                    {{ old('banque_id', $recouvrement->banque_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fs-9">N° Chèque</label>
                    <input type="text" name="numero_cheque"
                           value="{{ old('numero_cheque', $recouvrement->numero_cheque) }}"
                           class="form-control form-control-lg" maxlength="64">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('recouvrements.show', $recouvrement) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>

</form>

</x-app-layout>
