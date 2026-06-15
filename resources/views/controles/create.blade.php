<x-app-layout title="Nouveau contrôle fiscal">

<x-page-header titre="Ouvrir un contrôle fiscal" sous-titre="Étape d'instruction" />

<form method="POST" action="{{ route('controles.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0"><span class="fas fa-search-dollar me-2 text-primary"></span>Instruction du contrôle</h5>
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
                        <input type="text" class="form-control form-control-lg bg-light" readonly
                               value="{{ $etablissement->numero }} — {{ $etablissement->denomination ?? $nomContrib }}">
                    @else
                        <select name="etablissement_id"
                                class="form-select form-select-lg @error('etablissement_id') is-invalid @enderror" required>
                            <option value="">— Choisir un établissement —</option>
                            @foreach ($etablissements as $etab)
                                @php
                                    $c = $etab->contribuable;
                                    $nom = $c
                                        ? ($c->type_personne === 'PP'
                                            ? trim(($c->nom ?? '') . ' ' . ($c->prenoms ?? ''))
                                            : ($c->raison_sociale ?? ''))
                                        : '';
                                @endphp
                                <option value="{{ $etab->id }}" {{ old('etablissement_id') == $etab->id ? 'selected' : '' }}>
                                    {{ $etab->numero }} — {{ $etab->denomination ?? $nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('etablissement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Agent instructeur --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Agent instructeur</label>
                    <select name="agent_instructeur_id"
                            class="form-select form-select-lg @error('agent_instructeur_id') is-invalid @enderror">
                        <option value="">— À affecter —</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_instructeur_id') == $agent->id ? 'selected' : '' }}>
                                {{ trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_instructeur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Période contrôlée --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Période — début</label>
                    <input type="date" name="periode_debut" value="{{ old('periode_debut') }}"
                           class="form-control form-control-lg @error('periode_debut') is-invalid @enderror">
                    @error('periode_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Période — fin</label>
                    <input type="date" name="periode_fin" value="{{ old('periode_fin') }}"
                           class="form-control form-control-lg @error('periode_fin') is-invalid @enderror">
                    @error('periode_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Motif --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Motif du contrôle</label>
                    <input type="text" name="motif" value="{{ old('motif') }}" maxlength="512"
                           class="form-control form-control-lg @error('motif') is-invalid @enderror"
                           placeholder="Ex : contrôle sur pièces, soupçon de minoration...">
                    @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $etablissement ? route('etablissements.show', $etablissement) : route('controles.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Ouvrir le contrôle
        </button>
    </div>

</form>

</x-app-layout>
