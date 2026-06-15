@php
    $val = fn ($champ, $defaut = null) => old($champ, $convocation?->{$champ} ?? $defaut);
    $valDate = fn ($champ) => old($champ, optional($convocation?->{$champ})->toDateString());
@endphp

{{-- Rattachement --}}
<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-link me-2 text-primary"></span>Rattachement</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Établissement --}}
            <div class="col-md-6">
                <label class="form-label fs-9">Établissement <span class="text-danger">*</span></label>
                @if ($etablissement)
                    <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                    @php
                        $c = $etablissement->contribuable;
                        $nom = $c ? ($c->type_personne === 'PP' ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? '')) : ($c->raison_sociale ?? '')) : '';
                    @endphp
                    <input type="text" class="form-control form-control-lg bg-light" readonly
                           value="{{ $etablissement->numero }} — {{ $etablissement->denomination ?? $nom }}">
                @else
                    <select name="etablissement_id" class="form-select form-select-lg @error('etablissement_id') is-invalid @enderror" required>
                        <option value="">— Choisir un établissement —</option>
                        @foreach ($etablissements as $etab)
                            @php
                                $c = $etab->contribuable;
                                $nom = $c ? ($c->type_personne === 'PP' ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? '')) : ($c->raison_sociale ?? '')) : '';
                            @endphp
                            <option value="{{ $etab->id }}" {{ $val('etablissement_id') == $etab->id ? 'selected' : '' }}>
                                {{ $etab->numero }} — {{ $etab->denomination ?? $nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('etablissement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @endif
            </div>

            <div class="col-md-2">
                <label class="form-label fs-9">Année <span class="text-danger">*</span></label>
                <input type="number" name="annee" value="{{ $val('annee', date('Y')) }}" min="2000" max="2100"
                       class="form-control form-control-lg @error('annee') is-invalid @enderror" required>
                @error('annee') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fs-9">Service <span class="text-danger">*</span></label>
                <select name="service_id" class="form-select form-select-lg @error('service_id') is-invalid @enderror" required>
                    <option value="">— Choisir —</option>
                    @foreach ($services as $s)
                        <option value="{{ $s->id }}" {{ $val('service_id') == $s->id ? 'selected' : '' }}>{{ $s->libelle }}</option>
                    @endforeach
                </select>
                @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fs-9">Agent chargé <span class="text-danger">*</span></label>
                <select name="agent_id" class="form-select form-select-lg @error('agent_id') is-invalid @enderror" required>
                    <option value="">— Choisir —</option>
                    @foreach ($agents as $a)
                        <option value="{{ $a->id }}" {{ $val('agent_id') == $a->id ? 'selected' : '' }}>
                            {{ trim(($a->nom ?? '') . ' ' . ($a->prenoms ?? '')) }}
                        </option>
                    @endforeach
                </select>
                @error('agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fs-9">Motif</label>
                <textarea name="motif" rows="2" maxlength="512"
                          class="form-control form-control-lg @error('motif') is-invalid @enderror">{{ $val('motif') }}</textarea>
                @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- Calendrier --}}
<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-calendar me-2 text-primary"></span>Calendrier</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fs-9">Date de convocation</label>
                <input type="date" name="date_convocation" value="{{ $valDate('date_convocation') ?? now()->toDateString() }}" class="form-control form-control-lg">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9">Délai réponse (jours)</label>
                <input type="number" name="delai_reponse" value="{{ $val('delai_reponse') }}" min="1" placeholder="30" class="form-control form-control-lg">
            </div>
            <div class="col-md-3">
                <label class="form-label fs-9">Date limite</label>
                <input type="date" name="date_limite" value="{{ $valDate('date_limite') }}" class="form-control form-control-lg">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9">Période début</label>
                <input type="date" name="periode_due_debut" value="{{ $valDate('periode_due_debut') }}" class="form-control form-control-lg">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9">Période fin</label>
                <input type="date" name="periode_due_fin" value="{{ $valDate('periode_due_fin') }}"
                       class="form-control form-control-lg @error('periode_due_fin') is-invalid @enderror">
                @error('periode_due_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- Rappel fiscal --}}
<div class="card mb-3">
    <div class="card-header py-3">
        <h5 class="mb-0"><span class="fas fa-coins me-2 text-primary"></span>Rappel fiscal</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label fs-9">Nb mois dus</label>
                <input type="number" name="nb_mois_du" value="{{ $val('nb_mois_du') }}" min="0" class="form-control form-control-lg">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9">Nb jours dus</label>
                <input type="number" name="nb_jours_du" value="{{ $val('nb_jours_du') }}" min="0" class="form-control form-control-lg">
            </div>
            <div class="col-md-4">
                <label class="form-label fs-9">Montant dû (FCFA)</label>
                <input type="number" name="montant_du" value="{{ $val('montant_du', '0') }}" min="0" step="1" class="form-control form-control-lg">
            </div>
        </div>
    </div>
</div>
