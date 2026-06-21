<x-app-layout :title="__('Types de personne')">

    <x-page-header titre="Paramétrage — Types de personne" />

    <x-filtre.card :action="route('parametrage.types-personne.index')" :reset="route('parametrage.types-personne.index')"
        titre="Filtrer les types de personne selon vos critères">
        <x-filtre.input name="libelle" label="Libellé" placeholder="Recherche par libellé..." />
    </x-filtre.card>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-id-card me-2 text-primary"></span>
                Types de personne
                <span class="badge bg-secondary ms-2">{{ $types->total() }}</span>
            </h5>
            @can('PARAMFISC_GERER')
            <a href="{{ route('parametrage.types-personne.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-1"></span>Nouveau type
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
                        @forelse ($types as $type)
                            <tr>
                                <td class="fw-bold">{{ $type->code }}</td>
                                <td>{{ $type->libelle }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @can('PARAMFISC_GERER')
                                        <a href="{{ route('parametrage.types-personne.edit', $type) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('PARAMFISC_GERER')
                                        <form method="POST"
                                              action="{{ route('parametrage.types-personne.destroy', $type) }}"
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
                                    Aucun type de personne trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                @if ($types->hasPages())
                    Affichage de {{ $types->firstItem() }} à {{ $types->lastItem() }}
                    sur {{ $types->total() }} type(s) de personne
                @else
                    {{ $types->total() }} type(s) de personne
                @endif
            </small>
            @if ($types->hasPages())
                {{ $types->links() }}
            @endif
        </div>
    </div>

</x-app-layout>
