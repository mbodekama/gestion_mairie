<x-app-layout :title="__('Agents & Accès')">

    <x-page-header titre="Administration — Agents & Accès" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('agents.filtre')" :reset="route('agents.index')"
        titre="Filtrer les agents selon vos critères">
        <x-filtre.input name="matricule" label="Matricule" placeholder="Ex : AG001" col="col-md-2" />
        <x-filtre.input name="nom" label="Nom / Prénoms" placeholder="Recherche par nom..." />
        <x-filtre.select name="service_id" label="Service"
            :options="$services" option-label="libelle" />
        <x-filtre.select name="fonction_agent_id" label="Fonction"
            :options="$fonctions" option-label="libelle" col="col-md-2" />
        <x-filtre.select name="grade_agent_id" label="Grade"
            :options="$grades" option-label="libelle" col="col-md-2" />
        <x-filtre.select name="actif" label="Statut"
            :options="collect([['id' => '1', 'libelle' => 'Actif'], ['id' => '0', 'libelle' => 'Inactif']])"
            option-label="libelle" col="col-md-2" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-user-shield me-2 text-primary"></span>
                Agents
                <span class="badge bg-secondary ms-2">{{ $agents->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('agents.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('agents.create') }}" class="btn btn-primary btn-sm">
                    <span class="fas fa-plus me-1"></span>Nouvel agent
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="matricule" label="Matricule" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="nom" label="Nom & Prénoms"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="fonction_agent_id" label="Fonction"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="grade_agent_id" label="Grade"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="service_id" label="Service"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Compte</th>
                            <x-datatable.th-tri colonne="actif" label="Statut" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($agents as $agent)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $agent->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $agent->matricule }}</span>
                                </td>
                                <td class="fw-semi-bold">
                                    {{ trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) ?: '—' }}
                                </td>
                                <td>{{ $agent->fonctionAgent?->libelle ?? '—' }}</td>
                                <td>{{ $agent->gradeAgent?->libelle ?? '—' }}</td>
                                <td>{{ $agent->service?->libelle ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($agent->utilisateurs->isNotEmpty())
                                        <span class="badge bg-success">
                                            <span class="fas fa-check me-1"></span>Actif
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Aucun</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($agent->actif)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('agents.show', $agent) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('agents.edit', $agent) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('agents.destroy', $agent) }}"
                                              onsubmit="return confirm('Supprimer l\'agent {{ $agent->matricule }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun agent trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($agents->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }}
                    sur {{ $agents->total() }} agent(s)
                </small>
                {{ $agents->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
