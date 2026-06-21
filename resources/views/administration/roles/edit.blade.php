<x-app-layout :title="'Permissions — ' . $role->name">

    <x-page-header titre="Configuration — Permissions du rôle"
                   :sous-titre="$role->name" />

    <form method="POST" action="{{ route('administration.roles.update', $role) }}">
        @csrf @method('PUT')

        <div class="card mb-3">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <span class="fas fa-user-tag me-2 text-primary"></span>{{ $role->name }}
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="tout-cocher">
                        <span class="fas fa-check-double me-1"></span>Tout cocher
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="tout-decocher">
                        <span class="fas fa-times me-1"></span>Tout décocher
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if ($role->name === 'ADMIN')
                    <div class="alert alert-warning fs-9 mb-3">
                        <span class="fas fa-exclamation-triangle me-1"></span>
                        Le rôle <strong>ADMIN</strong> est le rôle d'administration : retirer des permissions ici
                        peut limiter l'accès des administrateurs. À modifier avec prudence.
                    </div>
                @endif

                <div class="row g-3">
                    @foreach ($permissionsParModule as $prefixe => $permissions)
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded h-100">
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light border-bottom">
                                    <span class="fw-semi-bold fs-9">
                                        {{ $modules[$prefixe] ?? $prefixe }}
                                    </span>
                                    <a href="#" class="fs-10 text-decoration-none" data-groupe-toggle>tout</a>
                                </div>
                                <div class="px-3 py-2">
                                    @foreach ($permissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input perm-check" type="checkbox"
                                                   name="permissions[]" value="{{ $permission->name }}"
                                                   id="perm-{{ $permission->id }}"
                                                   {{ in_array($permission->name, $permissionsRole, true) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-10 font-monospace" for="perm-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('administration.roles.index') }}" class="btn btn-outline-secondary btn-lg">
                <span class="fas fa-times me-1"></span>Annuler
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <span class="fas fa-save me-1"></span>Enregistrer les permissions
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toutes = () => document.querySelectorAll('.perm-check');

                document.querySelector('[data-action="tout-cocher"]')
                    ?.addEventListener('click', () => toutes().forEach(c => c.checked = true));
                document.querySelector('[data-action="tout-decocher"]')
                    ?.addEventListener('click', () => toutes().forEach(c => c.checked = false));

                // Bascule par groupe (case « tout » de chaque module).
                document.querySelectorAll('[data-groupe-toggle]').forEach(function (lien) {
                    lien.addEventListener('click', function (e) {
                        e.preventDefault();
                        const cases = lien.closest('.border').querySelectorAll('.perm-check');
                        const toutCoche = [...cases].every(c => c.checked);
                        cases.forEach(c => c.checked = ! toutCoche);
                    });
                });
            });
        </script>
    @endpush

</x-app-layout>
