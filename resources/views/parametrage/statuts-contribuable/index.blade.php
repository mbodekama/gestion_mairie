<x-app-layout :title="__('Statuts contribuable')">

    <x-page-header titre="Paramétrage — Statuts contribuable" />

    <x-filtre.card :action="route('parametrage.statuts-contribuable.index')" :reset="route('parametrage.statuts-contribuable.index')"
        titre="Filtrer les statuts contribuable selon vos critères">
        <x-filtre.input name="libelle" label="Libellé" placeholder="Recherche par libellé..." />
    </x-filtre.card>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-toggle-on me-2 text-primary"></span>
                Statuts contribuable
                <span class="badge bg-secondary ms-2">{{ $statuts->total() }}</span>
            </h5>
            @can('PARAMFISC_GERER')
            <a href="{{ route('parametrage.statuts-contribuable.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-1"></span>Nouveau statut
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($statuts as $statut)
                            <tr>
                                <td class="fw-bold">{{ $statut->code }}</td>
                                <td>{{ $statut->libelle }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @can('PARAMFISC_GERER')
                                        <a href="{{ route('parametrage.statuts-contribuable.edit', $statut) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('PARAMFISC_GERER')
                                        <form method="POST"
                                              action="{{ route('parametrage.statuts-contribuable.destroy', $statut) }}"
                                              onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun statut trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                @if ($statuts->hasPages())
                    Affichage de {{ $statuts->firstItem() }} à {{ $statuts->lastItem() }}
                    sur {{ $statuts->total() }} statut(s)
                @else
                    {{ $statuts->total() }} statut(s) contribuable
                @endif
            </small>
            @if ($statuts->hasPages())
                {{ $statuts->links() }}
            @endif
        </div>
    </div>

</x-app-layout>
