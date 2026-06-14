<x-app-layout title="Nouvel établissement">

<x-page-header titre="Créer un établissement" />

<form method="POST" action="{{ route('etablissements.store') }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-store me-2 text-primary"></span>Informations générales
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
                               value="{{ $contribuable->numero_identifiant }} — {{ $contribuable->type_personne === 'PP' ? $contribuable->nom . ' ' . $contribuable->prenoms : $contribuable->raison_sociale }}"
                               readonly>
                    @else
                        <input type="text" name="numero_identifiant"
                               value="{{ old('numero_identifiant') }}"
                               class="form-control form-control-lg @error('numero_identifiant') is-invalid @enderror"
                               placeholder="Ex : CI2026000001"
                               maxlength="12"
                               style="text-transform:uppercase">
                        @error('numero_identifiant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @error('contribuable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Type --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Type <span class="text-danger">*</span></label>
                    <select name="type_etablissement" class="form-select form-select-lg @error('type_etablissement') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        <option value="PRINCIPAL"   {{ old('type_etablissement') === 'PRINCIPAL'   ? 'selected' : '' }}>Principal</option>
                        <option value="SECONDAIRE"  {{ old('type_etablissement') === 'SECONDAIRE'  ? 'selected' : '' }}>Secondaire</option>
                    </select>
                    @error('type_etablissement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Statut --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Statut <span class="text-danger">*</span></label>
                    <select name="statut" class="form-select form-select-lg @error('statut') is-invalid @enderror" required>
                        <option value="ACTIF"     {{ old('statut', 'ACTIF') === 'ACTIF'     ? 'selected' : '' }}>Actif</option>
                        <option value="CESSE"     {{ old('statut') === 'CESSE'     ? 'selected' : '' }}>Cessé</option>
                        <option value="TRANSFERE" {{ old('statut') === 'TRANSFERE' ? 'selected' : '' }}>Transféré</option>
                        <option value="SOMMEIL"   {{ old('statut') === 'SOMMEIL'   ? 'selected' : '' }}>En sommeil</option>
                    </select>
                    @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Dénomination --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Dénomination</label>
                    <input type="text" name="denomination" value="{{ old('denomination') }}"
                           class="form-control form-control-lg @error('denomination') is-invalid @enderror" maxlength="255">
                    @error('denomination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Activité --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Activité <span class="text-danger">*</span></label>
                    <select name="activite_id" class="form-select form-select-lg @error('activite_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($activites as $a)
                            <option value="{{ $a->id }}" {{ old('activite_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('activite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Date début --}}
                <div class="col-md-3">
                    <label class="form-label fs-9">Date début d'activité <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut_activite" value="{{ old('date_debut_activite') }}"
                           class="form-control form-control-lg @error('date_debut_activite') is-invalid @enderror" required>
                    @error('date_debut_activite') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <select name="commune_id" id="commune_id"
                            class="form-select form-select-lg @error('commune_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($communes as $c)
                            <option value="{{ $c->id }}" {{ old('commune_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('commune_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Zone fiscale <span class="text-danger">*</span></label>
                    <select name="zone_fiscale_id" id="zone_fiscale_id"
                            class="form-select form-select-lg @error('zone_fiscale_id') is-invalid @enderror"
                            data-url-template="{{ route('etablissements.zones-fiscales', ['commune' => '__COMMUNE__']) }}"
                            data-old="{{ old('zone_fiscale_id') }}"
                            {{ old('commune_id') ? '' : 'disabled' }} required>
                        <option value="">{{ old('commune_id') ? '— Choisir —' : '— Choisir une commune d\'abord —' }}</option>
                        @foreach ($zonesFiscales as $z)
                            <option value="{{ $z->id }}" {{ old('zone_fiscale_id') == $z->id ? 'selected' : '' }}>
                                {{ $z->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_fiscale_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse') }}"
                           class="form-control form-control-lg" maxlength="64">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                           class="form-control form-control-lg" maxlength="32">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-lg" maxlength="128">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Boîte postale</label>
                    <input type="text" name="boite_postale" value="{{ old('boite_postale') }}"
                           class="form-control form-control-lg" maxlength="32">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $contribuable ? route('contribuables.show', $contribuable) : route('etablissements.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'établissement
        </button>
    </div>

</form>

@push('scripts')
<script>
(function () {
    const communeSelect = document.getElementById('commune_id');
    const zoneSelect    = document.getElementById('zone_fiscale_id');
    if (!communeSelect || !zoneSelect) return;

    const urlTemplate = zoneSelect.dataset.urlTemplate;

    async function chargerZones(communeId, zoneAReselectionner = null) {
        // Réinitialise le select tant qu'aucune commune n'est choisie
        if (!communeId) {
            zoneSelect.innerHTML = '<option value="">— Choisir une commune d\'abord —</option>';
            zoneSelect.disabled = true;
            return;
        }

        zoneSelect.disabled = true;
        zoneSelect.innerHTML = '<option value="">Chargement…</option>';

        try {
            const reponse = await fetch(urlTemplate.replace('__COMMUNE__', communeId), {
                headers: { 'Accept': 'application/json' },
            });
            const zones = await reponse.json();

            let html = '<option value="">— Choisir —</option>';
            for (const zone of zones) {
                const selected = String(zone.id) === String(zoneAReselectionner) ? ' selected' : '';
                html += `<option value="${zone.id}"${selected}>${zone.libelle}</option>`;
            }
            zoneSelect.innerHTML = html;
            zoneSelect.disabled = false;
        } catch (e) {
            zoneSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            zoneSelect.disabled = false;
        }
    }

    communeSelect.addEventListener('change', () => chargerZones(communeSelect.value));

    // Repopulation après erreur de validation : les zones de la commune choisie
    // sont déjà rendues côté serveur, on ne refait un fetch que si ce n'est pas le cas.
    if (communeSelect.value && zoneSelect.options.length <= 1) {
        chargerZones(communeSelect.value, zoneSelect.dataset.old);
    }
}());
</script>
@endpush

</x-app-layout>
