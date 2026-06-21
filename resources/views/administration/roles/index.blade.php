<x-app-layout :title="__('Configuration des rôles')">

    <x-page-header titre="Configuration — Rôles & permissions" />

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-user-tag me-2 text-primary"></span>
                Rôles
                <span class="badge bg-secondary ms-2">{{ $roles->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>Rôle</th>
                            <th class="text-center">Permissions</th>
                            <th class="text-center">Comptes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td class="fw-bold">
                                    <span class="fas fa-user-tag me-1 text-primary"></span>{{ $role->name }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary border border-primary">
                                        {{ $role->permissions_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $role->users_count }}</span>
                                </td>
                                <td>
                                    @can('SECURITE_GERER_ROLE')
                                    <a href="{{ route('administration.roles.edit', $role) }}"
                                       class="btn btn-sm btn-outline-warning" title="Gérer les permissions">
                                        <span class="fas fa-key me-1"></span>Gérer les permissions
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun rôle défini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $roles->count() }} rôle(s) configurable(s)</small>
            <small class="text-muted">
                <span class="fas fa-info-circle me-1"></span>
                Les rôles sont définis par le système ; seules leurs permissions sont modifiables.
            </small>
        </div>
    </div>

</x-app-layout>
