<x-app-layout :title="__('Audit des données')">

    <x-page-header titre="Administration — Audit des données" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('administration.audit.filtre')" :reset="route('administration.audit.index')"
        titre="Filtrer les entrées d'audit selon vos critères">
        <x-filtre.input name="table_cible" label="Table" placeholder="Ex : emission_taxe" col="col-md-3" />
        <x-filtre.select name="action" label="Action"
            :options="collect([
                ['id' => 'INSERT', 'libelle' => 'INSERT — Création'],
                ['id' => 'UPDATE', 'libelle' => 'UPDATE — Modification'],
                ['id' => 'DELETE', 'libelle' => 'DELETE — Suppression'],
            ])"
            option-label="libelle" col="col-md-3" />
        <x-filtre.input name="date_du" label="Du" placeholder="jj/mm/aaaa" col="col-md-2" />
        <x-filtre.input name="date_au" label="Au" placeholder="jj/mm/aaaa" col="col-md-2" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-history me-2 text-primary"></span>
                Audit des données
                <span class="badge bg-secondary ms-2">{{ $audits->total() }}</span>
            </h5>
            <div class="d-flex gap-2">
                <button type="submit" form="formFiltres" formaction="{{ route('administration.audit.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-tri colonne="horodatage" label="Horodatage"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="table_cible" label="Table"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="cle_ligne" label="Clé"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="action" label="Action" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="utilisateur_id" label="Utilisateur"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $audit)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $audit->horodatage?->format('d/m/Y H:i:s') ?? '—' }}
                                </td>
                                <td>
                                    <code class="fs-9">{{ $audit->table_cible }}</code>
                                </td>
                                <td>
                                    <code class="fs-9">{{ $audit->cle_ligne }}</code>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badge = match($audit->action) {
                                            'INSERT' => 'bg-success',
                                            'UPDATE' => 'bg-warning text-dark',
                                            'DELETE' => 'bg-danger',
                                            default  => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $audit->action }}</span>
                                </td>
                                <td>{{ $audit->utilisateur?->login ?? '—' }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="tooltip"
                                            title="{{ $audit->donnees_apres ? json_encode($audit->donnees_apres, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '—' }}">
                                        <span class="fas fa-search-plus"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune entrée d'audit trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($audits->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $audits->firstItem() }} à {{ $audits->lastItem() }}
                    sur {{ $audits->total() }} entrée(s)
                </small>
                {{ $audits->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
