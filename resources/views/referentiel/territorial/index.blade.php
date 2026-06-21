<x-app-layout :title="__('Référentiel territorial')">

    <x-page-header titre="Référentiel — Communes" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('referentiel.territorial.filtre')" :reset="route('referentiel.territorial.index')"
        titre="Filtrer les communes selon vos critères">
        <x-filtre.input name="code"    label="Code" placeholder="Ex : ABJ" col="col-md-2" />
        <x-filtre.input name="libelle" label="Libellé" placeholder="Nom de la commune..." />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-map-marker-alt me-2 text-primary"></span>
                Liste des communes
                <span class="badge bg-secondary ms-2">{{ $communes->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('referentiel.territorial.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('TERRITOIRE_GERER')
                <a href="{{ route('referentiel.territorial.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle commune
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="code" label="Code" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="libelle" label="Commune"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="sous_prefecture_id" label="Sous-préfecture"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-end">Population</th>
                            <th class="text-center">Nb étab.</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($communes as $commune)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $commune->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $commune->code }}</span>
                                </td>
                                <td class="fw-semi-bold">{{ $commune->libelle }}</td>
                                <td>{{ $commune->sousPrefecture?->libelle ?? '—' }}</td>
                                <td class="text-end">
                                    {{ $commune->population ? number_format($commune->population, 0, ',', ' ') : '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $commune->etablissements_count ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('referentiel.territorial.show', $commune) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('TERRITOIRE_GERER')
                                        <a href="{{ route('referentiel.territorial.edit', $commune) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('TERRITOIRE_GERER')
                                        <form method="POST"
                                              action="{{ route('referentiel.territorial.destroy', $commune) }}"
                                              onsubmit="return confirm('Confirmer la suppression de la commune {{ $commune->libelle }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune commune trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($communes->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $communes->firstItem() }} à {{ $communes->lastItem() }}
                    sur {{ $communes->total() }} commune(s)
                </small>
                {{ $communes->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
