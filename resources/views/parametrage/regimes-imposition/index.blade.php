<x-app-layout :title="__('Régimes d\'imposition')">

    <x-page-header titre="Paramétrage — Régimes d'imposition" />

    <x-filtre.card :action="route('parametrage.regimes-imposition.index')" :reset="route('parametrage.regimes-imposition.index')"
        titre="Filtrer les régimes d'imposition selon vos critères">
        <x-filtre.input name="libelle" label="Libellé / Code" placeholder="Recherche par libellé ou code..." />
    </x-filtre.card>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-sliders-h me-2 text-primary"></span>
                Régimes d'imposition
                <span class="badge bg-secondary ms-2">{{ $regimes->total() }}</span>
            </h5>
            <a href="{{ route('parametrage.regimes-imposition.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-1"></span>Nouveau régime
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé court</th>
                            <th>Libellé</th>
                            <th class="text-end">CA borne inf. (F)</th>
                            <th class="text-end">CA borne sup. (F)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($regimes as $regime)
                            <tr>
                                <td class="fw-bold">{{ $regime->code }}</td>
                                <td>{{ $regime->libelle_court ?? '—' }}</td>
                                <td>{{ $regime->libelle ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float) $regime->ca_borne_inf, 0, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format((float) $regime->ca_borne_sup, 0, ',', ' ') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('parametrage.regimes-imposition.edit', $regime) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('parametrage.regimes-imposition.destroy', $regime) }}"
                                              onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun régime d'imposition trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($regimes->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $regimes->firstItem() }} à {{ $regimes->lastItem() }}
                    sur {{ $regimes->total() }} régime(s)
                </small>
                {{ $regimes->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
