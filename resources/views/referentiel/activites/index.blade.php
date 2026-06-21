<x-app-layout :title="__('Activités économiques')">

    <x-page-header titre="Référentiel — Activités économiques" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('referentiel.activites.filtre')" :reset="route('referentiel.activites.index')"
        titre="Filtrer les activités économiques selon vos critères">
        <x-filtre.input name="code"    label="Code activité"     placeholder="Ex : ACT001" col="col-md-2" />
        <x-filtre.input name="libelle" label="Libellé"            placeholder="Recherche par libellé..." />
        <x-filtre.select name="secteur_activite_id" label="Secteur d'activité"
            :options="$secteurs" option-label="libelle" />
        <x-filtre.select name="categorie_activite_id" label="Catégorie"
            :options="$categories" option-label="libelle" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-industry me-2 text-primary"></span>
                Liste des activités économiques
                <span class="badge bg-secondary ms-2">{{ $activites->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('referentiel.activites.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('ACTIVITE_GERER')
                <a href="{{ route('referentiel.activites.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle activité
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
                            <x-datatable.th-tri colonne="libelle" label="Activité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="secteur_activite_id" label="Secteur"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="categorie_activite_id" label="Catégorie"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Nb étab.</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activites as $activite)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $activite->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $activite->code }}</span>
                                </td>
                                <td class="fw-semi-bold">{{ $activite->libelle }}</td>
                                <td>{{ $activite->secteurActivite?->libelle ?? '—' }}</td>
                                <td>{{ $activite->categorieActivite?->libelle ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $activite->etablissements_count ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('referentiel.activites.show', $activite) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('ACTIVITE_GERER')
                                        <a href="{{ route('referentiel.activites.edit', $activite) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('ACTIVITE_GERER')
                                        <form method="POST"
                                              action="{{ route('referentiel.activites.destroy', $activite) }}"
                                              onsubmit="return confirm('Confirmer la suppression de l\'activité {{ $activite->code }} ?')">
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
                                    Aucune activité trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($activites->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $activites->firstItem() }} à {{ $activites->lastItem() }}
                    sur {{ $activites->total() }} activité(s)
                </small>
                {{ $activites->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
