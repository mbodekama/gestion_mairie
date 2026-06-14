<x-auth-card-layout :title="__('Session verrouillée')">

    <div class="row justify-content-center">
        <div class="col-auto">

            {{-- Avatar + nom --}}
            <div class="d-md-flex align-items-center text-center text-md-start">
                <div class="avatar avatar-4xl me-md-4 mb-3 mb-md-0 mx-auto mx-md-0">
                    <div class="avatar-name rounded-circle bg-primary-subtle text-primary"
                         style="width:80px;height:80px;font-size:2rem;font-weight:700;display:flex;align-items:center;justify-content:center;">
                        {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                    </div>
                </div>
                <div class="flex-1">
                    <h4>Bonjour, {{ Str::of(auth()->user()->name)->before(' ') ?: auth()->user()->name }} !</h4>
                    <p class="mb-0">Entrez votre mot de passe<br>pour reprendre la session.</p>
                </div>
            </div>

            {{-- Formulaire --}}
            <form class="mt-4"
                  method="POST"
                  action="{{ route('verrouillage.deverrouiller') }}"
                  autocomplete="off">
                @csrf

                {{-- Champ leurre : empêche le navigateur d'autofill le vrai champ --}}
                <input type="password" name="_trap" style="display:none;" tabindex="-1" aria-hidden="true">

                <div class="mb-3">
                    <input class="form-control @error('password') is-invalid @enderror"
                           type="password"
                           name="password"
                           placeholder="Mot de passe"
                           autocomplete="new-password"
                           aria-label="Mot de passe utilisateur"
                           autofocus>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary d-block w-100" type="submit">
                    Déverrouiller
                </button>
            </form>

            {{-- Lien déconnexion --}}
            <div class="text-center mt-4 pt-2 border-top">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-600 fs-10 p-0">
                        <span class="fas fa-sign-out-alt me-1"></span>Se déconnecter
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-auth-card-layout>
