<x-app-layout :title="__('Journal des connexions')">

    <x-page-header titre="Administration — Journal des connexions" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('administration.journal.filtre')" :reset="route('administration.journal.index')"
        titre="Filtrer les connexions selon vos critères">
        <x-filtre.input name="login" label="Login" placeholder="Ex : agent01" col="col-md-2" />
        <x-filtre.select name="succes" label="Résultat"
            :options="collect([['id' => '1', 'libelle' => 'Succès'], ['id' => '0', 'libelle' => 'Échec']])"
            option-label="libelle" col="col-md-2" />
        <x-filtre.input name="date_du" label="Du" placeholder="jj/mm/aaaa" col="col-md-2" />
        <x-filtre.input name="date_au" label="Au" placeholder="jj/mm/aaaa" col="col-md-2" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-sign-in-alt me-2 text-primary"></span>
                Journal des connexions
                <span class="badge bg-secondary ms-2">{{ $journaux->total() }}</span>
            </h5>
            <div class="d-flex gap-2">
                <button type="submit" form="formFiltres" formaction="{{ route('administration.journal.export') }}"
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
                            <x-datatable.th-tri colonne="login" label="Login"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Application</th>
                            <x-datatable.th-tri colonne="succes" label="Résultat" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Adresse IP</th>
                            <th>User-Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($journaux as $journal)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $journal->horodatage?->format('d/m/Y H:i:s') ?? '—' }}
                                </td>
                                <td class="fw-semi-bold">{{ $journal->login ?? '—' }}</td>
                                <td>{{ $journal->application ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($journal->succes)
                                        <span class="badge bg-success">
                                            <span class="fas fa-check me-1"></span>Succès
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <span class="fas fa-times me-1"></span>Échec
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <code class="fs-9">{{ $journal->adresse_ip ?? '—' }}</code>
                                </td>
                                <td class="text-muted text-truncate" style="max-width: 250px;" title="{{ $journal->user_agent }}">
                                    {{ $journal->user_agent ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune connexion trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($journaux->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $journaux->firstItem() }} à {{ $journaux->lastItem() }}
                    sur {{ $journaux->total() }} entrée(s)
                </small>
                {{ $journaux->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
