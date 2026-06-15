<x-app-layout :title="'Modifier — ' . $controle->numero">

@php
    $contrib = $controle->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '';
@endphp

<x-page-header :titre="'Modifier le contrôle ' . $controle->numero" sous-titre="En instruction" />

<form method="POST" action="{{ route('controles.update', $controle) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0"><span class="fas fa-edit me-2 text-primary"></span>Instruction du contrôle</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fs-9">Établissement</label>
                    <input type="hidden" name="etablissement_id" value="{{ $controle->etablissement_id }}">
                    <input type="text" class="form-control form-control-lg bg-light" readonly
                           value="{{ $controle->etablissement?->numero }} — {{ $controle->etablissement?->denomination ?? $nomContrib }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fs-9">Agent instructeur</label>
                    <select name="agent_instructeur_id" class="form-select form-select-lg">
                        <option value="">— À affecter —</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}"
                                {{ old('agent_instructeur_id', $controle->agent_instructeur_id) == $agent->id ? 'selected' : '' }}>
                                {{ trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9">Période — début</label>
                    <input type="date" name="periode_debut"
                           value="{{ old('periode_debut', $controle->periode_debut?->toDateString()) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Période — fin</label>
                    <input type="date" name="periode_fin"
                           value="{{ old('periode_fin', $controle->periode_fin?->toDateString()) }}"
                           class="form-control form-control-lg @error('periode_fin') is-invalid @enderror">
                    @error('periode_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fs-9">Motif du contrôle</label>
                    <input type="text" name="motif" value="{{ old('motif', $controle->motif) }}" maxlength="512"
                           class="form-control form-control-lg">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('controles.show', $controle) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

</x-app-layout>
