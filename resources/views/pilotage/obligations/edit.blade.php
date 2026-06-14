<x-app-layout title="Modifier une obligation fiscale">

@php
    $contrib = $obligation->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
@endphp

<x-page-header titre="Modifier l'obligation fiscale" />

<form method="POST" action="{{ route('pilotage.obligations.update', $obligation) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-tasks me-2 text-primary"></span>Obligation fiscale
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

                {{-- Nature de taxe --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id" class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($naturesTaxe as $nt)
                            <option value="{{ $nt->id }}" {{ old('nature_taxe_id', $obligation->nature_taxe_id) == $nt->id ? 'selected' : '' }}>
                                {{ $nt->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('nature_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Périodicité --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Périodicité</label>
                    <select name="periodicite_id" class="form-select form-select-lg @error('periodicite_id') is-invalid @enderror">
                        <option value="">— Aucune —</option>
                        @foreach ($periodicites as $p)
                            <option value="{{ $p->id }}" {{ old('periodicite_id', $obligation->periodicite_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodicite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('pilotage.obligations.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>

</form>

</x-app-layout>
