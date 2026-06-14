<x-app-layout :title="'Modifier contrôle — ' . $controleFiscal->numero">

<x-page-header titre="Modifier le contrôle fiscal {{ $controleFiscal->numero }}" />

<form method="POST" action="{{ route('controle-fiscal.update', $controleFiscal) }}" novalidate>
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
                    <label class="form-label fs-9">N° Contrôle</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $controleFiscal->numero }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Établissement</label>
                    <input type="text" class="form-control form-control-lg bg-light"
                           value="{{ $controleFiscal->etablissement?->numero ?? '—' }}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Année <span class="text-danger">*</span></label>
                    <input type="number" name="annee"
                           value="{{ old('annee', $controleFiscal->annee) }}"
                           class="form-control form-control-lg @error('annee') is-invalid @enderror"
                           min="2000" max="2100" required>
                    @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Affectation --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-users me-2 text-primary"></span>Affectation
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Service <span class="text-danger">*</span></label>
                    <select name="service_id"
                            class="form-select form-select-lg @error('service_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($services as $s)
                            <option value="{{ $s->id }}"
                                    {{ old('service_id', $controleFiscal->service_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Agent chargé <span class="text-danger">*</span></label>
                    <select name="agent_id"
                            class="form-select form-select-lg @error('agent_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($agents as $a)
                            <option value="{{ $a->id }}"
                                    {{ old('agent_id', $controleFiscal->agent_id) == $a->id ? 'selected' : '' }}>
                                {{ $a->nom }} {{ $a->prenoms ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fs-9">Motif du contrôle</label>
                    <textarea name="motif" rows="2"
                              class="form-control form-control-lg @error('motif') is-invalid @enderror"
                              maxlength="512">{{ old('motif', $controleFiscal->motif) }}</textarea>
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
                           value="{{ old('date_convocation', $controleFiscal->date_convocation?->format('Y-m-d')) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Délai réponse (jours)</label>
                    <input type="number" name="delai_reponse"
                           value="{{ old('delai_reponse', $controleFiscal->delai_reponse) }}"
                           class="form-control form-control-lg" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Date limite de réponse</label>
                    <input type="date" name="date_limite"
                           value="{{ old('date_limite', $controleFiscal->date_limite?->format('Y-m-d')) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Date de réponse effective</label>
                    <input type="date" name="date_reponse"
                           value="{{ old('date_reponse', $controleFiscal->date_reponse?->format('Y-m-d')) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Heure réponse</label>
                    <input type="time" name="heure_reponse"
                           value="{{ old('heure_reponse', $controleFiscal->heure_reponse) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Période début</label>
                    <input type="date" name="periode_due_debut"
                           value="{{ old('periode_due_debut', $controleFiscal->periode_due_debut?->format('Y-m-d')) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Période fin</label>
                    <input type="date" name="periode_due_fin"
                           value="{{ old('periode_due_fin', $controleFiscal->periode_due_fin?->format('Y-m-d')) }}"
                           class="form-control form-control-lg">
                </div>
            </div>
        </div>
    </div>

    {{-- Rappel fiscal --}}
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
                           value="{{ old('nb_mois_du', $controleFiscal->nb_mois_du) }}"
                           class="form-control form-control-lg" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Nb jours dus</label>
                    <input type="number" name="nb_jours_du"
                           value="{{ old('nb_jours_du', $controleFiscal->nb_jours_du) }}"
                           class="form-control form-control-lg" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant dû (FCFA)</label>
                    <input type="number" name="montant_du"
                           value="{{ old('montant_du', $controleFiscal->montant_du) }}"
                           class="form-control form-control-lg" min="0" step="1">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('controle-fiscal.show', $controleFiscal) }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>

</form>

<x-documents.panneau :model="$controleFiscal" :editable="true" />

</x-app-layout>
