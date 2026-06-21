<x-app-layout :title="__('Paramétrage fiscal')">

    <x-page-header titre="Référentiel — Natures de taxes" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('referentiel.parametrage.filtre')" :reset="route('referentiel.parametrage.index')"
        titre="Filtrer les natures de taxes selon vos critères">
        <x-filtre.input name="code"    label="Code" placeholder="Ex : PAT" col="col-md-2" />
        <x-filtre.input name="libelle" label="Libellé / Abrégé" placeholder="Recherche par libellé..." />
        <x-filtre.select name="domaine_taxe_id" label="Domaine de taxe"
            :options="$domaines" option-label="libelle" />
        <x-filtre.select name="categorie_impot_taxe_id" label="Catégorie d'impôt"
            :options="$categories" option-label="libelle" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-sliders-h me-2 text-primary"></span>
                Natures de taxes
                <span class="badge bg-secondary ms-2">{{ $naturesTaxe->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('referentiel.parametrage.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('PARAMFISC_GERER')
                <a href="{{ route('referentiel.parametrage.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle nature de taxe
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
                            <x-datatable.th-tri colonne="libelle_court" label="Abrégé" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="libelle" label="Libellé complet"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="domaine_taxe_id" label="Domaine"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="categorie_impot_taxe_id" label="Catégorie"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Barèmes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($naturesTaxe as $nature)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $nature->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $nature->code }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($nature->libelle_court)
                                        <span class="badge bg-light text-dark border">{{ $nature->libelle_court }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-semi-bold">{{ $nature->libelle }}</td>
                                <td>{{ $nature->domaineTaxe?->libelle_court ?? $nature->domaineTaxe?->libelle ?? '—' }}</td>
                                <td>{{ $nature->categorieImpotTaxe?->libelle ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $nature->baremesTaxe_count ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('referentiel.parametrage.show', $nature) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('PARAMFISC_GERER')
                                        <a href="{{ route('referentiel.parametrage.edit', $nature) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('PARAMFISC_GERER')
                                        <form method="POST"
                                              action="{{ route('referentiel.parametrage.destroy', $nature) }}"
                                              onsubmit="return confirm('Confirmer la suppression de {{ $nature->code }} ?')">
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
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune nature de taxe trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($naturesTaxe->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $naturesTaxe->firstItem() }} à {{ $naturesTaxe->lastItem() }}
                    sur {{ $naturesTaxe->total() }} nature(s) de taxe
                </small>
                {{ $naturesTaxe->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
