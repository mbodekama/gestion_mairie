{{-- Formulaire partagé création / édition d'un compte utilisateur.
     Variables attendues :
       $agent, $roles (collection de noms), $rolesSelectionnes (array),
       $estCreation (bool), $compte (User|null), $nomDefaut, $emailDefaut, $actifDefaut --}}

<div class="row g-3">
    {{-- Nom affiché --}}
    <div class="col-md-6">
        <label class="form-label fs-9">Nom affiché <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $nomDefaut) }}"
               class="form-control form-control-lg @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- E-mail (identifiant de connexion) --}}
    <div class="col-md-6">
        <label class="form-label fs-9">Adresse e-mail (identifiant de connexion) <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email', $emailDefaut) }}"
               class="form-control form-control-lg @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text fs-10">
            <span class="fas fa-paper-plane me-1"></span>
            @if ($estCreation)
                Les identifiants de connexion seront envoyés à cette adresse.
            @else
                En cas de nouveau mot de passe, les identifiants seront renvoyés à cette adresse.
            @endif
        </div>
    </div>

    {{-- Mot de passe --}}
    <div class="col-md-6">
        <label class="form-label fs-9">
            Mot de passe @if ($estCreation) <span class="text-danger">*</span> @endif
        </label>
        <input type="password" name="password" autocomplete="new-password"
               class="form-control form-control-lg @error('password') is-invalid @enderror"
               {{ $estCreation ? 'required' : '' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text fs-10">
            @if ($estCreation)
                8 caractères minimum.
            @else
                Laisser vide pour conserver le mot de passe actuel.
            @endif
        </div>
    </div>

    {{-- Confirmation --}}
    <div class="col-md-6">
        <label class="form-label fs-9">
            Confirmer le mot de passe @if ($estCreation) <span class="text-danger">*</span> @endif
        </label>
        <input type="password" name="password_confirmation" autocomplete="new-password"
               class="form-control form-control-lg" {{ $estCreation ? 'required' : '' }}>
    </div>

    {{-- Rôles --}}
    <div class="col-12">
        <label class="form-label fs-9">Rôles (permissions)</label>
        @error('roles.*') <div class="text-danger fs-10 mb-1">{{ $message }}</div> @enderror
        <div class="row g-2">
            @forelse ($roles as $role)
                <div class="col-md-4 col-lg-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="roles[]" value="{{ $role }}"
                               id="role-{{ $role }}"
                               {{ in_array($role, old('roles', $rolesSelectionnes), true) ? 'checked' : '' }}>
                        <label class="form-check-label fs-9" for="role-{{ $role }}">{{ $role }}</label>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted fs-9">Aucun rôle défini dans le système.</div>
            @endforelse
        </div>
        <div class="form-text fs-10">Sans rôle, le compte peut se connecter mais n'a accès à aucune action.</div>
    </div>

    {{-- Activation --}}
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="actif" value="1"
                   id="compte-actif" {{ old('actif', $actifDefaut) ? 'checked' : '' }}>
            <label class="form-check-label fs-9" for="compte-actif">
                Compte actif (autorisé à se connecter)
            </label>
        </div>
    </div>
</div>
