<x-app-layout title="Nouveau barème de taxe">

<x-page-header titre="Créer un barème de taxe" />

<form method="POST" action="{{ route('parametrage.baremes-taxe.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-percent me-2 text-primary"></span>Barème de taxe
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Nature de taxe --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id" class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($naturesTaxe as $n)
                            <option value="{{ $n->id }}" {{ old('nature_taxe_id') == $n->id ? 'selected' : '' }}>
                                {{ $n->libelle_court ?? $n->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('nature_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Catégorie d'activité --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Catégorie d'activité</label>
                    <select name="categorie_activite_id" class="form-select form-select-lg @error('categorie_activite_id') is-invalid @enderror">
                        <option value="">— Toutes (barème général) —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" {{ old('categorie_activite_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_activite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Périodicité --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Périodicité <span class="text-danger">*</span></label>
                    <select name="periodicite_id" class="form-select form-select-lg @error('periodicite_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($periodicites as $p)
                            <option value="{{ $p->id }}" {{ old('periodicite_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodicite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Borne inf --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">CA borne inférieure (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="ca_borne_inf" value="{{ old('ca_borne_inf', '0') }}"
                           class="form-control form-control-lg @error('ca_borne_inf') is-invalid @enderror"
                           min="0" step="1" required>
                    @error('ca_borne_inf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Borne sup --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">CA borne supérieure (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="ca_borne_sup" value="{{ old('ca_borne_sup', '0') }}"
                           class="form-control form-control-lg @error('ca_borne_sup') is-invalid @enderror"
                           min="0" step="1" required>
                    <small class="text-muted">0 = tranche ouverte (au-delà de la borne inférieure).</small>
                    @error('ca_borne_sup') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Taux --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Taux (% du CA) <span class="text-danger">*</span></label>
                    <input type="number" name="taux" value="{{ old('taux') }}"
                           class="form-control form-control-lg @error('taux') is-invalid @enderror"
                           min="0" max="100" step="0.0001" placeholder="Ex : 0.5" required>
                    @error('taux') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('parametrage.baremes-taxe.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer le barème
        </button>
    </div>

</form>

</x-app-layout>
