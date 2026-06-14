<x-app-layout :title="__('Mon profil')">

    @php
        $user = auth()->user();
        $initiales = collect(explode(' ', $user->name))
            ->filter()
            ->take(2)
            ->map(fn($mot) => strtoupper(substr($mot, 0, 1)))
            ->implode('');
        $roles = $user->getRoleNames();
    @endphp

    {{-- ===== Carte bannière profil ===== --}}
    <div class="card mb-3">
        <div class="card-header position-relative min-vh-25 mb-7">
            {{-- Fond dégradé bannière --}}
            <div class="rounded-3 rounded-bottom-0 position-absolute top-0 start-0 w-100 h-100"
                 style="background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);"></div>
            {{-- Avatar photo de profil --}}
            <div class="avatar avatar-5xl avatar-profile">
                <img class="rounded-circle img-thumbnail shadow-sm"
                     src="{{ asset('img/avatar-defaut.jpg') }}"
                     width="200"
                     alt="{{ $user->name }}">
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="mb-1">
                        {{ $user->name }}
                        @if($user->email_verified_at)
                            <span data-bs-toggle="tooltip" title="Email vérifié">
                                <small class="fa fa-check-circle text-primary"></small>
                            </span>
                        @endif
                    </h4>
                    <h5 class="fs-9 fw-normal text-600">{{ $user->email }}</h5>
                    <div class="mt-2">
                        @forelse($roles as $role)
                            <span class="badge bg-primary me-1">{{ $role }}</span>
                        @empty
                            <span class="badge bg-secondary">Aucun rôle</span>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('logout') }}"
                              onsubmit="return confirm('Confirmer la déconnexion ?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <span class="fas fa-sign-out-alt me-1"></span>Se déconnecter
                            </button>
                        </form>
                    </div>
                    <div class="border-bottom border-dashed my-4 d-lg-none"></div>
                </div>
                <div class="col ps-2 ps-lg-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fas fa-envelope fs-6 me-2 text-700"></span>
                        <div class="flex-1">
                            <h6 class="mb-0 fs-9">{{ $user->email }}</h6>
                            <small class="text-500">Adresse e-mail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="fas fa-clock fs-6 me-2 text-700"></span>
                        <div class="flex-1">
                            <h6 class="mb-0 fs-9">
                                {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                            </h6>
                            <small class="text-500">Membre depuis</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="fas fa-shield-alt fs-6 me-2 text-700"></span>
                        <div class="flex-1">
                            <h6 class="mb-0 fs-9">
                                @if($user->email_verified_at)
                                    <span class="text-success">Compte vérifié</span>
                                @else
                                    <span class="text-warning">Non vérifié</span>
                                @endif
                            </h6>
                            <small class="text-500">Statut du compte</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Layout 2 colonnes ===== --}}
    <div class="row g-0">

        {{-- ===== Colonne principale (8/12) ===== --}}
        <div class="col-lg-8 pe-lg-2">

            {{-- Informations du compte --}}
            <div class="card mb-3">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-user-edit me-2 text-primary"></span>
                        Informations du compte
                    </h5>
                    @if (session('status') === 'profile-updated')
                        <span class="badge bg-success">
                            <span class="fas fa-check me-1"></span>Informations mises à jour
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semi-bold">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semi-bold">
                                    Adresse e-mail <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <span class="fas fa-save me-1"></span>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Activité récente --}}
            @php
                $icones = [
                    'CONNEXION'      => ['icon' => 'fa-sign-in-alt',  'couleur' => 'text-success',   'badge' => 'bg-success',   'label' => 'Connexion'],
                    'DECONNEXION'    => ['icon' => 'fa-sign-out-alt', 'couleur' => 'text-secondary',  'badge' => 'bg-secondary', 'label' => 'Déconnexion'],
                    'VERROUILLAGE'   => ['icon' => 'fa-lock',         'couleur' => 'text-warning',   'badge' => 'bg-warning text-dark', 'label' => 'Verrouillage'],
                    'DEVERROUILLAGE' => ['icon' => 'fa-unlock',       'couleur' => 'text-primary',   'badge' => 'bg-primary',   'label' => 'Déverrouillage'],
                ];
            @endphp

            <div class="card mb-3 mb-lg-0">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-history me-2 text-primary"></span>
                        Activité récente
                    </h5>
                    <a class="font-sans-serif fs-9" href="{{ route('administration.journal.index') }}">
                        Voir tout <span class="fas fa-chevron-right ms-1 fs-11"></span>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 fs-9">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:36px;"></th>
                                    <th>Événement</th>
                                    <th>Date &amp; heure</th>
                                    <th>Adresse IP</th>
                                    <th class="text-center">Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activiteRecente as $journal)
                                    @php
                                        $type = $journal->type_evenement ?? 'CONNEXION';
                                        $meta = $icones[$type] ?? $icones['CONNEXION'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <span class="fas {{ $meta['icon'] }} {{ $meta['couleur'] }}"></span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $meta['badge'] }} fs-11">{{ $meta['label'] }}</span>
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $journal->horodatage?->format('d/m/Y') }}
                                            <span class="text-600 ms-1">{{ $journal->horodatage?->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <code class="fs-9">{{ $journal->adresse_ip ?? '—' }}</code>
                                        </td>
                                        <td class="text-center">
                                            @if($journal->succes)
                                                <span class="fas fa-check-circle text-success" title="Succès"></span>
                                            @else
                                                <span class="fas fa-times-circle text-danger" title="Échec"></span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                            Aucune activité enregistrée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== Colonne latérale (4/12) sticky ===== --}}
        <div class="col-lg-4 ps-lg-2">
            <div class="sticky-sidebar">

                {{-- Changer le mot de passe --}}
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">
                            <span class="fas fa-lock me-2 text-primary"></span>
                            Changer le mot de passe
                        </h5>
                    </div>
                    <div class="card-body fs-10">
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success alert-dismissible py-2 mb-3" role="alert">
                                <span class="fas fa-check me-1"></span>Mot de passe mis à jour.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semi-bold">
                                    Mot de passe actuel
                                </label>
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="form-control form-control-sm @error('current_password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="current-password">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semi-bold">
                                    Nouveau mot de passe
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control form-control-sm @error('password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semi-bold">
                                    Confirmer le mot de passe
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control form-control-sm"
                                       autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <span class="fas fa-key me-1"></span>Mettre à jour le mot de passe
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Rôles & Permissions --}}
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">
                            <span class="fas fa-user-tag me-2 text-primary"></span>
                            Rôles &amp; Permissions
                        </h5>
                    </div>
                    <div class="card-body fs-10">
                        @forelse($roles as $role)
                            <div class="d-flex align-items-center mb-2">
                                <span class="fas fa-shield-alt me-2 text-primary"></span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $role }}
                                </span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucun rôle assigné.</p>
                        @endforelse

                        @php $permissions = $user->getAllPermissions(); @endphp
                        @if($permissions->isNotEmpty())
                            <div class="border-top border-dashed mt-3 pt-3">
                                <p class="mb-2 text-600 fw-semi-bold">Permissions directes</p>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($permissions->take(10) as $permission)
                                        <span class="badge bg-light text-dark border fs-11">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($permissions->count() > 10)
                                        <span class="badge bg-secondary">+{{ $permissions->count() - 10 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Zone de danger --}}
                <div class="card mb-3 mb-lg-0 border-danger">
                    <div class="card-header bg-danger-subtle">
                        <h5 class="mb-0 text-danger">
                            <span class="fas fa-exclamation-triangle me-2"></span>
                            Zone de danger
                        </h5>
                    </div>
                    <div class="card-body fs-10">
                        <p class="text-muted mb-3">
                            La suppression de votre compte est définitive. Toutes vos données seront perdues.
                        </p>
                        <button type="button"
                                class="btn btn-outline-danger btn-sm w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#modalSuppressionCompte">
                            <span class="fas fa-trash me-1"></span>Supprimer mon compte
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Modal confirmation suppression --}}
    <div class="modal fade" id="modalSuppressionCompte" tabindex="-1" aria-labelledby="modalSuppressionLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalSuppressionLabel">
                        <span class="fas fa-exclamation-triangle me-2"></span>Supprimer mon compte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body fs-9">
                    <p>Cette action est <strong>irréversible</strong>. Confirmez votre mot de passe pour continuer.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}" id="formSuppressionCompte">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label for="password_suppression" class="form-label">Mot de passe</label>
                            <input type="password"
                                   id="password_suppression"
                                   name="password"
                                   class="form-control form-control-sm @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="Votre mot de passe actuel">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="formSuppressionCompte" class="btn btn-danger btn-sm">
                        <span class="fas fa-trash me-1"></span>Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modal = new bootstrap.Modal(document.getElementById('modalSuppressionCompte'));
                modal.show();
            });
        </script>
        @endpush
    @endif

</x-app-layout>
