<x-app-layout title="Nouveau contrôle fiscal">

<x-page-header titre="Créer un contrôle fiscal" />

@if (session('error'))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('controle-fiscal.store') }}" novalidate>
    @csrf

    {{-- Rattachement --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-link me-2 text-primary"></span>Rattachement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Établissement --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Établissement <span class="text-danger">*</span></label>
                    @if ($etablissement)
                        <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                        @php
                            $contrib = $etablissement->contribuable;
                            $nomContrib = $contrib
                                ? ($contrib->type_personne === 'PP'
                                    ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                    : ($contrib->raison_sociale ?? ''))
                                : '';
                        @endphp
                        <input type="text" class="form-control form-control-lg bg-light"
                               value="{{ $etablissement->numero }} — {{ $etablissement->denomination ?? $nomContrib }}"
                               readonly>
                    @else
                        <input type="number" name="etablissement_id"
                               value="{{ old('etablissement_id') }}"
                               class="form-control form-control-lg @error('etablissement_id') is-invalid @enderror"
                               placeholder="ID de l'établissement" required>
                        @error('etablissement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Année --}}
                <div class="col-md-2">
                    <label class="form-label fs-9">Année <span class="text-danger">*</span></label>
                    <input type="number" name="annee"
                           value="{{ old('annee', date('Y')) }}"
                           class="form-control form-control-lg @error('annee') is-invalid @enderror"
                           min="2000" max="2100" required>
                    @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Service --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Service <span class="text-danger">*</span></label>
                    <select name="service_id"
                            class="form-select form-select-lg @error('service_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($services as $s)
                            <option value="{{ $s->id }}"
                                    {{ old('service_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Agent --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Agent chargé <span class="text-danger">*</span></label>
                    <select name="agent_id"
                            class="form-select form-select-lg @error('agent_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($agents as $a)
                            <option value="{{ $a->id }}"
                                    {{ old('agent_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->nom }} {{ $a->prenoms ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Motif --}}
                <div class="col-12">
                    <label class="form-label fs-9">Motif du contrôle</label>
                    <textarea name="motif" rows="2"
                              class="form-control form-control-lg @error('motif') is-invalid @enderror"
                              maxlength="512">{{ old('motif') }}</textarea>
                    @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Calendrier --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-calendar me-2 text-primary"></span>Calendrier de contrôle
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fs-9">Date de convocation</label>
                    <input type="date" name="date_convocation"
                           value="{{ old('date_convocation') }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Délai réponse (jours)</label>
                    <input type="number" name="delai_reponse"
                           value="{{ old('delai_reponse') }}"
                           class="form-control form-control-lg" min="1" placeholder="30">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Date limite de réponse</label>
                    <input type="date" name="date_limite"
                           value="{{ old('date_limite') }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Période début</label>
                    <input type="date" name="periode_due_debut"
                           value="{{ old('periode_due_debut') }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Période fin</label>
                    <input type="date" name="periode_due_fin"
                           value="{{ old('periode_due_fin') }}"
                           class="form-control form-control-lg">
                </div>
            </div>
        </div>
    </div>

    {{-- Montants --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-coins me-2 text-primary"></span>Rappel fiscal
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fs-9">Nb mois dus</label>
                    <input type="number" name="nb_mois_du"
                           value="{{ old('nb_mois_du') }}"
                           class="form-control form-control-lg" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Nb jours dus</label>
                    <input type="number" name="nb_jours_du"
                           value="{{ old('nb_jours_du') }}"
                           class="form-control form-control-lg" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant dû (FCFA)</label>
                    <input type="number" name="montant_du"
                           value="{{ old('montant_du', '0') }}"
                           class="form-control form-control-lg" min="0" step="1">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $etablissement ? route('etablissements.show', $etablissement) : route('controle-fiscal.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer le contrôle
        </button>
    </div>

</form>

</x-app-layout>
