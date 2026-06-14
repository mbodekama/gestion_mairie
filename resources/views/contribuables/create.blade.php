<x-app-layout title="Nouveau contribuable">

<x-page-header titre="Nouveau contribuable" />

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible py-2 mb-3" role="alert">
        <strong>Veuillez corriger les erreurs ci-dessous.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('contribuables.store') }}" novalidate id="formContribuable">
    @csrf

    {{-- ── Type de personne ─────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-id-card me-2 text-primary"></span>Identification
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @error('numero_identifiant')
                <div class="col-12">
                    <div class="alert alert-danger py-2 mb-0">{{ $message }}</div>
                </div>
                @enderror
                <div class="col-md-4">
                    <label class="form-label fs-9">Type de personne <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type_personne" id="typePP"
                                   value="PP" {{ old('type_personne', 'PP') === 'PP' ? 'checked' : '' }}>
                            <label class="form-check-label fs-9" for="typePP">Personne physique</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type_personne" id="typePM"
                                   value="PM" {{ old('type_personne') === 'PM' ? 'checked' : '' }}>
                            <label class="form-check-label fs-9" for="typePM">Personne morale</label>
                        </div>
                    </div>
                    @error('type_personne') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">N° Compte</label>
                    <input type="text" name="numero_compte"
                           value="{{ old('numero_compte') }}"
                           class="form-control form-control-lg @error('numero_compte') is-invalid @enderror"
                           maxlength="50">
                    @error('numero_compte') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Statut <span class="text-danger">*</span></label>
                    <select name="statut" class="form-select form-select-lg @error('statut') is-invalid @enderror" required>
                        @foreach ($statuts as $s)
                            <option value="{{ $s->code }}" {{ old('statut') === $s->code ? 'selected' : '' }}>
                                {{ $s->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Régime d'imposition</label>
                    <select name="regime_imposition_id" class="form-select form-select-lg @error('regime_imposition_id') is-invalid @enderror">
                        <option value="">— Aucun —</option>
                        @foreach ($regimes as $r)
                            <option value="{{ $r->id }}" {{ old('regime_imposition_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->libelle_court }}
                            </option>
                        @endforeach
                    </select>
                    @error('regime_imposition_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Données PP ────────────────────────────────────────────────── --}}
    <div id="sectionPP" class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-user me-2 text-primary"></span>État civil
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           class="form-control form-control-lg @error('nom') is-invalid @enderror" maxlength="100">
                    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Prénoms</label>
                    <input type="text" name="prenoms" value="{{ old('prenoms') }}"
                           class="form-control form-control-lg @error('prenoms') is-invalid @enderror" maxlength="150">
                    @error('prenoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Sexe</label>
                    <select name="sexe" class="form-select form-select-lg @error('sexe') is-invalid @enderror">
                        <option value="">—</option>
                        <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Date de naissance</label>
                    <input type="date" name="date_naissance"
                           value="{{ old('date_naissance') }}"
                           class="form-control form-control-lg @error('date_naissance') is-invalid @enderror">
                    @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance"
                           value="{{ old('lieu_naissance') }}"
                           class="form-control form-control-lg @error('lieu_naissance') is-invalid @enderror" maxlength="100">
                    @error('lieu_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Nationalité</label>
                    <select name="nationalite_id" class="form-select form-select-lg @error('nationalite_id') is-invalid @enderror">
                        <option value="">— Choisir —</option>
                        @foreach ($nationalites as $n)
                            <option value="{{ $n->id }}" {{ old('nationalite_id') == $n->id ? 'selected' : '' }}>
                                {{ $n->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('nationalite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Nature pièce</label>
                    <input type="text" name="nature_piece"
                           value="{{ old('nature_piece') }}"
                           class="form-control form-control-lg @error('nature_piece') is-invalid @enderror"
                           placeholder="CNI, Passeport…" maxlength="50">
                    @error('nature_piece') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Numéro pièce</label>
                    <input type="text" name="numero_piece"
                           value="{{ old('numero_piece') }}"
                           class="form-control form-control-lg @error('numero_piece') is-invalid @enderror" maxlength="50">
                    @error('numero_piece') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Filiation --}}
            <hr class="my-3">
            <p class="fs-9 text-600 mb-2">Filiation</p>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fs-9">Prénoms du père</label>
                    <input type="text" name="prenoms_pere" value="{{ old('prenoms_pere') }}"
                           class="form-control form-control-lg" maxlength="150">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Nom du père</label>
                    <input type="text" name="nom_pere" value="{{ old('nom_pere') }}"
                           class="form-control form-control-lg" maxlength="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Prénoms de la mère</label>
                    <input type="text" name="prenoms_mere" value="{{ old('prenoms_mere') }}"
                           class="form-control form-control-lg" maxlength="150">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Nom de la mère</label>
                    <input type="text" name="nom_mere" value="{{ old('nom_mere') }}"
                           class="form-control form-control-lg" maxlength="100">
                </div>
            </div>
        </div>
    </div>

    {{-- ── Données PM ────────────────────────────────────────────────── --}}
    <div id="sectionPM" class="card mb-3" style="display:none">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-building me-2 text-primary"></span>Identification juridique
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fs-9">Raison sociale <span class="text-danger">*</span></label>
                    <input type="text" name="raison_sociale"
                           value="{{ old('raison_sociale') }}"
                           class="form-control form-control-lg @error('raison_sociale') is-invalid @enderror" maxlength="200">
                    @error('raison_sociale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Sigle</label>
                    <input type="text" name="sigle" value="{{ old('sigle') }}"
                           class="form-control form-control-lg" maxlength="20">
                </div>
                <div class="col-md-5">
                    <label class="form-label fs-9">Dénomination commerciale</label>
                    <input type="text" name="denomination_commerciale"
                           value="{{ old('denomination_commerciale') }}"
                           class="form-control form-control-lg" maxlength="200">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Forme juridique</label>
                    <select name="forme_juridique_id" class="form-select form-select-lg @error('forme_juridique_id') is-invalid @enderror">
                        <option value="">— Choisir —</option>
                        @foreach ($formesJurid as $fj)
                            <option value="{{ $fj->id }}" {{ old('forme_juridique_id') == $fj->id ? 'selected' : '' }}>
                                {{ $fj->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('forme_juridique_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Registre du commerce</label>
                    <input type="text" name="registre_commerce"
                           value="{{ old('registre_commerce') }}"
                           class="form-control form-control-lg" maxlength="50">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Date immatriculation</label>
                    <input type="date" name="date_registre_commerce"
                           value="{{ old('date_registre_commerce') }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Ville RC</label>
                    <input type="text" name="ville_registre_commerce"
                           value="{{ old('ville_registre_commerce') }}"
                           class="form-control form-control-lg" maxlength="100">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-9">Nombre d'associés</label>
                    <input type="number" name="nombre_associes"
                           value="{{ old('nombre_associes') }}"
                           class="form-control form-control-lg @error('nombre_associes') is-invalid @enderror" min="0">
                    @error('nombre_associes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Capital social (FCFA)</label>
                    <input type="number" name="capital_social"
                           value="{{ old('capital_social') }}"
                           class="form-control form-control-lg @error('capital_social') is-invalid @enderror" min="0" step="1">
                    @error('capital_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Contacts ──────────────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-address-book me-2 text-primary"></span>Contacts
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fs-9">Mobile</label>
                    <input type="text" name="cellulaire" value="{{ old('cellulaire') }}"
                           class="form-control form-control-lg @error('cellulaire') is-invalid @enderror" maxlength="30">
                    @error('cellulaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Téléphone fixe</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                           class="form-control form-control-lg" maxlength="30">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Fax</label>
                    <input type="text" name="fax" value="{{ old('fax') }}"
                           class="form-control form-control-lg" maxlength="30">
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-lg @error('email') is-invalid @enderror" maxlength="150">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-9">Boîte postale</label>
                    <input type="text" name="boite_postale" value="{{ old('boite_postale') }}"
                           class="form-control form-control-lg" maxlength="50">
                </div>
            </div>
        </div>
    </div>

    {{-- ── Boutons ───────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('contribuables.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-plus me-1"></span>Créer le contribuable
        </button>
    </div>

</form>

@push('scripts')
<script>
(function () {
    const radios = document.querySelectorAll('input[name="type_personne"]');
    const sectionPP = document.getElementById('sectionPP');
    const sectionPM = document.getElementById('sectionPM');

    function basculer(type) {
        sectionPP.style.display = type === 'PP' ? '' : 'none';
        sectionPM.style.display = type === 'PM' ? '' : 'none';
    }

    radios.forEach(r => r.addEventListener('change', () => basculer(r.value)));

    // Initialisation (gestion du rechargement après erreur de validation)
    const checked = document.querySelector('input[name="type_personne"]:checked');
    if (checked) basculer(checked.value);
}());
</script>
@endpush

</x-app-layout>
