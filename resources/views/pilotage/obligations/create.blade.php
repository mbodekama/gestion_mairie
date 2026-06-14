<x-app-layout title="Nouvelle obligation fiscale">

<x-page-header titre="Créer une obligation fiscale" />

<form method="POST" action="{{ route('pilotage.obligations.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-tasks me-2 text-primary"></span>Obligation fiscale
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Contribuable --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Matricule contribuable <span class="text-danger">*</span></label>
                    @if ($contribuable)
                        <input type="hidden" name="contribuable_id" value="{{ $contribuable->id }}">
                        <input type="text" class="form-control form-control-lg bg-light"
                               value="{{ $contribuable->numero_identifiant }} — {{ $contribuable->type_personne === 'PP' ? trim($contribuable->nom . ' ' . $contribuable->prenoms) : $contribuable->raison_sociale }}"
                               readonly>
                    @else
                        <input type="text" name="numero_identifiant" value="{{ old('numero_identifiant') }}"
                               class="form-control form-control-lg @error('numero_identifiant') is-invalid @enderror @error('contribuable_id') is-invalid @enderror"
                               placeholder="Ex : CI2024000001" maxlength="12" style="text-transform:uppercase">
                        @error('numero_identifiant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @error('contribuable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Nature de taxe --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id" class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($naturesTaxe as $nt)
                            <option value="{{ $nt->id }}" {{ old('nature_taxe_id') == $nt->id ? 'selected' : '' }}>
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
                            <option value="{{ $p->id }}" {{ old('periodicite_id') == $p->id ? 'selected' : '' }}>
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
        <a href="{{ $contribuable ? route('contribuables.show', $contribuable) : route('pilotage.obligations.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'obligation
        </button>
    </div>

</form>

</x-app-layout>
