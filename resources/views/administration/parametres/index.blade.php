<x-app-layout :title="__('Paramètres application')">

    <x-page-header titre="Administration — Paramètres application" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('administration.parametres.filtre')" :reset="route('administration.parametres.index')"
        titre="Filtrer les paramètres selon vos critères">
        <x-filtre.input name="cle" label="Clé" placeholder="Ex : TAUX_PENALITE" col="col-md-3" />
        <x-filtre.input name="valeur" label="Valeur" placeholder="Recherche dans la valeur..." col="col-md-4" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-cog me-2 text-primary"></span>
                Paramètres application
                <span class="badge bg-secondary ms-2">{{ $parametres->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('administration.parametres.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('PARAMETRE_GERER')
                <a href="{{ route('administration.parametres.create') }}" class="btn btn-primary btn-sm">
                    <span class="fas fa-plus me-1"></span>Nouveau paramètre
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
                            <x-datatable.th-tri colonne="cle" label="Clé"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="valeur" label="Valeur"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Description</th>
                            <th>Collectivité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($parametres as $parametre)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $parametre->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td>
                                    <code class="badge bg-light text-dark border fw-bold fs-9">{{ $parametre->cle }}</code>
                                </td>
                                <td class="fw-semi-bold">
                                    {{ $parametre->valeur ?? '<span class="text-muted">null</span>' }}
                                </td>
                                <td class="text-muted">{{ $parametre->description ?? '—' }}</td>
                                <td>
                                    @if ($parametre->collectivite)
                                        {{ $parametre->collectivite->libelle }}
                                    @else
                                        <span class="badge bg-secondary">Global</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @can('PARAMETRE_GERER')
                                        <a href="{{ route('administration.parametres.edit', $parametre) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('PARAMETRE_GERER')
                                        <form method="POST"
                                              action="{{ route('administration.parametres.destroy', $parametre) }}"
                                              onsubmit="return confirm('Supprimer le paramètre {{ $parametre->cle }} ?')">
                                            @csrf
                                            @method('DELETE')
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun paramètre trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($parametres->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $parametres->firstItem() }} à {{ $parametres->lastItem() }}
                    sur {{ $parametres->total() }} paramètre(s)
                </small>
                {{ $parametres->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
