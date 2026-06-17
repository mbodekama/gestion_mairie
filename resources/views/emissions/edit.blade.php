<x-app-layout :title="'Modifier émission — ' . $emission->numero_emission">

<x-page-header titre="Modifier l'émission {{ $emission->numero_emission }}" />

@php $figer = $emission->exerciceFiscal?->cloture || $champsFiges; @endphp

@if ($emission->exerciceFiscal?->cloture)
    <div class="alert alert-danger py-2 fs-9">
        <span class="fas fa-lock me-1"></span>
        L'exercice {{ $emission->exerciceFiscal->annee }} est clôturé — modification impossible.
    </div>
@elseif ($champsFiges)
    <div class="alert alert-warning py-2 fs-9">
        <span class="fas fa-lock me-1"></span>
        Cette émission a fait l'objet d'un recouvrement : les champs de calcul (nature, périodicité, CA, montants) sont figés. Seules les dates restent modifiables.
    </div>
@endif


<form method="POST" action="{{ route('emissions.update', $emission) }}" novalidate>
    @csrf @method('PUT')

    {{-- Rattachement (readonly) --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-link me-2 text-primary"></span>Rattachement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">N° Émission</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $emission->numero_emission }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Établissement</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $emission->etablissement?->numero ?? '—' }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Exercice fiscal</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $emission->exerciceFiscal?->annee ?? '—' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    {{-- Nature et montants --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-coins me-2 text-primary"></span>Nature et montants
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id"
                            class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror"
                            {{ $figer ? 'disabled' : 'required' }}>
                        <option value="">— Choisir —</option>
                        @foreach ($naturesTaxe as $nt)
                            <option value="{{ $nt->id }}"
                                    {{ old('nature_taxe_id', $emission->nature_taxe_id) == $nt->id ? 'selected' : '' }}>
                                {{ $nt->libelle_court }}
                            </option>
                        @endforeach
                    </select>
                    @error('nature_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Périodicité <span class="text-danger">*</span></label>
                    <select name="periodicite_id"
                            class="form-select form-select-lg @error('periodicite_id') is-invalid @enderror"
                            {{ $figer ? 'disabled' : 'required' }}>
                        <option value="">— Choisir —</option>
                        @foreach ($periodicites as $p)
                            <option value="{{ $p->id }}"
                                    {{ old('periodicite_id', $emission->periodicite_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodicite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">N° Fiche</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $emission->numero_fiche ?? '—' }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">CA annuel (FCFA)</label>
                    <input type="number" name="ca_annuel"
                           value="{{ old('ca_annuel', $emission->ca_annuel) }}"
                           class="form-control form-control-lg" min="0" step="1"
                           {{ $figer ? 'disabled' : '' }}>
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Montant annuel (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant_annuel"
                           value="{{ old('montant_annuel', $emission->montant_annuel) }}"
                           class="form-control form-control-lg @error('montant_annuel') is-invalid @enderror"
                           min="0" step="1"
                           {{ $figer ? 'disabled' : 'required' }}>
                    @error('montant_annuel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Montant par période (FCFA)</label>
                    <input type="number" name="montant_periode"
                           value="{{ old('montant_periode', $emission->montant_periode) }}"
                           class="form-control form-control-lg" min="0" step="1"
                           {{ $figer ? 'disabled' : '' }}>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Nb mois prorata</label>
                    <input type="number" name="nb_mois_prorata"
                           value="{{ old('nb_mois_prorata', $emission->nb_mois_prorata) }}"
                           class="form-control form-control-lg" min="1" max="12"
                           {{ $figer ? 'disabled' : '' }}>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Montant prorata (FCFA)</label>
                    <input type="number" name="montant_prorata"
                           value="{{ old('montant_prorata', $emission->montant_prorata) }}"
                           class="form-control form-control-lg" min="0" step="1"
                           {{ $figer ? 'disabled' : '' }}>
                </div>
            </div>
        </div>
    </div>

    {{-- Dates --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-calendar me-2 text-primary"></span>Dates
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de déclaration</label>
                    <input type="date" name="date_declaration"
                           value="{{ old('date_declaration', $emission->date_declaration?->format('Y-m-d')) }}"
                           class="form-control form-control-lg"
                           {{ $emission->exerciceFiscal?->cloture ? 'disabled' : '' }}>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de liquidation</label>
                    <input type="date" name="date_liquidation"
                           value="{{ old('date_liquidation', $emission->date_liquidation?->format('Y-m-d')) }}"
                           class="form-control form-control-lg"
                           {{ $emission->exerciceFiscal?->cloture ? 'disabled' : '' }}>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('emissions.show', $emission) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        @if (!$emission->exerciceFiscal?->cloture)
            <button type="submit" class="btn btn-primary btn-lg">
                <span class="fas fa-save me-1"></span>Enregistrer
            </button>
        @endif
    </div>

</form>

<x-documents.panneau :model="$emission" :editable="true" />

</x-app-layout>
