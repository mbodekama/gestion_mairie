<x-app-layout :title="__('Obligations fiscales')">

    <x-page-header titre="Pilotage — Obligations fiscales des contribuables" />


    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('pilotage.obligations.filtre')" :reset="route('pilotage.obligations.index')"
        titre="Filtrer les obligations selon vos critères">
        <x-filtre.input name="contribuable" label="Contribuable" placeholder="Nom, raison sociale ou N° identifiant..." />
        <x-filtre.select name="nature_taxe_id" label="Nature de taxe"
            :options="$naturesTaxe" option-label="libelle" />
        <x-filtre.select name="periodicite_id" label="Périodicité"
            :options="$periodicites" option-label="libelle" col="col-md-2" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-tasks me-2 text-primary"></span>
                Obligations fiscales
                <span class="badge bg-secondary ms-2">{{ $obligations->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('pilotage.obligations.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('PILOTAGE_GERER')
                <a href="{{ route('pilotage.obligations.create') }}" class="btn btn-primary btn-sm">
                    <span class="fas fa-plus me-1"></span>Nouvelle obligation
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
                            <x-datatable.th-tri colonne="contribuable_id" label="Contribuable"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>N° Identifiant</th>
                            <x-datatable.th-tri colonne="nature_taxe_id" label="Nature de taxe"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="periodicite_id" label="Périodicité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="created_at" label="Créé le"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($obligations as $obligation)
                            @php
                                $contrib = $obligation->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                        : ($contrib->raison_sociale ?? ''))
                                    : '—';
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $obligation->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $nomContrib }}</td>
                                <td>
                                    <span class="text-muted">{{ $contrib?->numero_identifiant ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $obligation->natureTaxe?->libelle_court ?? $obligation->natureTaxe?->libelle ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $obligation->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-muted">
                                    {{ $obligation->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('pilotage.obligations.show', $obligation) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('PILOTAGE_GERER')
                                        <a href="{{ route('pilotage.obligations.edit', $obligation) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('PILOTAGE_GERER')
                                        <form method="POST"
                                              action="{{ route('pilotage.obligations.destroy', $obligation) }}"
                                              onsubmit="return confirm('Supprimer cette obligation fiscale ?')">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune obligation trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($obligations->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $obligations->firstItem() }} à {{ $obligations->lastItem() }}
                    sur {{ $obligations->total() }} obligation(s)
                </small>
                {{ $obligations->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
