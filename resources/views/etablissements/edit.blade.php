<x-app-layout :title="'Modifier établissement — ' . ($etablissement->denomination ?? $etablissement->numero)">

<x-page-header titre="Modifier l'établissement" />

@if (session('success'))
    <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('etablissements.update', $etablissement) }}" novalidate>
    @csrf @method('PUT')

    {{-- Informations générales --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-store me-2 text-primary"></span>Informations générales
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Contribuable (readonly) --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Matricule contribuable</label>
                    @php
                        $c = $etablissement->contribuable;
                        $nomContrib = $c
                            ? $c->numero_identifiant . ' — ' . ($c->type_personne === 'PP'
                                ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? ''))
                                : $c->raison_sociale)
                            : '—';
                    @endphp
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $nomContrib }}" readonly disabled>
                </div>

                {{-- N° Établissement (readonly) --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">N° Établissement</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $etablissement->numero ?? '—' }}" readonly>
                </div>

                {{-- Type --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Type <span class="text-danger">*</span></label>
                    <select name="type_etablissement"
                            class="form-select form-select-lg @error('type_etablissement') is-invalid @enderror"
                            required>
                        <option value="PRINCIPAL"  {{ old('type_etablissement', $etablissement->type_etablissement) === 'PRINCIPAL'  ? 'selected' : '' }}>Principal</option>
                        <option value="SECONDAIRE" {{ old('type_etablissement', $etablissement->type_etablissement) === 'SECONDAIRE' ? 'selected' : '' }}>Secondaire</option>
                    </select>
                    @error('type_etablissement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Dénomination --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Dénomination</label>
                    <input type="text" name="denomination"
                           value="{{ old('denomination', $etablissement->denomination) }}"
                           class="form-control form-control-lg @error('denomination') is-invalid @enderror"
                           maxlength="255">
                    @error('denomination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Activité --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Activité <span class="text-danger">*</span></label>
                    <select name="activite_id"
                            class="form-select form-select-lg @error('activite_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($activites as $a)
                            <option value="{{ $a->id }}"
                                    {{ old('activite_id', $etablissement->activite_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('activite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date début activité --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Date début activité <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut_activite"
                           value="{{ old('date_debut_activite', $etablissement->date_debut_activite?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_debut_activite') is-invalid @enderror"
                           required>
                    @error('date_debut_activite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date cessation --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de cessation</label>
                    <input type="date" name="date_cessation"
                           value="{{ old('date_cessation', $etablissement->date_cessation?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('date_cessation') is-invalid @enderror">
                    @error('date_cessation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- CA de référence --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">CA de référence (FCFA)</label>
                    <input type="number" name="ca_reference"
                           value="{{ old('ca_reference', $etablissement->ca_reference !== null ? (int) $etablissement->ca_reference : '') }}"
                           class="form-control form-control-lg @error('ca_reference') is-invalid @enderror"
                           min="0" step="1" placeholder="Dernier CA connu">
                    <small class="text-muted">Indicatif : sert de défaut au CA des émissions.</small>
                    @error('ca_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Statut --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Statut <span class="text-danger">*</span></label>
                    <select name="statut"
                            class="form-select form-select-lg @error('statut') is-invalid @enderror"
                            required>
                        <option value="ACTIF"     {{ old('statut', $etablissement->statut) === 'ACTIF'     ? 'selected' : '' }}>Actif</option>
                        <option value="CESSE"     {{ old('statut', $etablissement->statut) === 'CESSE'     ? 'selected' : '' }}>Cessé</option>
                        <option value="TRANSFERE" {{ old('statut', $etablissement->statut) === 'TRANSFERE' ? 'selected' : '' }}>Transféré</option>
                        <option value="SOMMEIL"   {{ old('statut', $etablissement->statut) === 'SOMMEIL'   ? 'selected' : '' }}>En sommeil</option>
                    </select>
                    @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Localisation --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-map-marker-alt me-2 text-primary"></span>Localisation
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Commune <span class="text-danger">*</span></label>
                    <select name="commune_id"
                            class="form-select form-select-lg @error('commune_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($communes as $c)
                            <option value="{{ $c->id }}"
                                    {{ old('commune_id', $etablissement->commune_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('commune_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Zone fiscale <span class="text-danger">*</span></label>
                    <select name="zone_fiscale_id"
                            class="form-select form-select-lg @error('zone_fiscale_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($zonesFiscales as $z)
                            <option value="{{ $z->id }}"
                                    {{ old('zone_fiscale_id', $etablissement->zone_fiscale_id) == $z->id ? 'selected' : '' }}>
                                {{ $z->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_fiscale_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Adresse</label>
                    <input type="text" name="adresse"
                           value="{{ old('adresse', $etablissement->adresse) }}"
                           class="form-control form-control-lg" maxlength="64">
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Téléphone</label>
                    <input type="text" name="telephone"
                           value="{{ old('telephone', $etablissement->telephone) }}"
                           class="form-control form-control-lg" maxlength="32">
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $etablissement->email) }}"
                           class="form-control form-control-lg" maxlength="128">
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Boîte postale</label>
                    <input type="text" name="boite_postale"
                           value="{{ old('boite_postale', $etablissement->boite_postale) }}"
                           class="form-control form-control-lg" maxlength="32">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('etablissements.show', $etablissement) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>

</form>

<x-documents.panneau :model="$etablissement" :editable="true" />

</x-app-layout>
