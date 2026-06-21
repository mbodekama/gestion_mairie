<x-app-layout :title="__('Services')">

    <x-page-header titre="Administration — Services" />


    {{-- ===== Filtres ===== --}}
    <x-filtre.card :action="route('services.filtre')" :reset="route('services.index')"
        titre="Filtrer les services">
        <x-filtre.input name="recherche" label="Recherche" placeholder="Code, libellé ou sigle..." />
        <x-filtre.select name="departement_service_id" label="Département" :options="$departements" option-label="libelle" col="col-md-3" />
    </x-filtre.card>

    {{-- ===== Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-building me-2 text-primary"></span>
                Services <span class="badge bg-secondary ms-2">{{ $services->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('services.export') }}"
                        class="btn btn-success btn-sm" title="Exporter en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('SERVICE_GERER')
                <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
                    <span class="fas fa-plus me-1"></span>Nouveau service
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-tri colonne="code" label="Code"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="libelle" label="Libellé"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="sigle" label="Sigle"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="departement_service_id" label="Département"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Agents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td><span class="badge bg-light text-dark border fw-bold">{{ $service->code }}</span></td>
                                <td class="fw-semi-bold">{{ $service->libelle }}</td>
                                <td>{{ $service->sigle ?? '—' }}</td>
                                <td>{{ $service->departementService?->libelle ?? '—' }}</td>
                                <td class="text-center"><span class="badge bg-info">{{ $service->agents_count }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                            <span class="fas fa-eye"></span>
                                        </a>
                                        @can('SERVICE_GERER')
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit"></span>
                                        </a>
                                        @endcan
                                        @can('SERVICE_GERER')
                                        <form method="POST" action="{{ route('services.destroy', $service) }}"
                                              onsubmit="return confirm('Supprimer le service {{ $service->code }} ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                {{ $service->agents_count > 0 ? 'disabled' : '' }}>
                                                <span class="fas fa-trash"></span>
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
                                    Aucun service trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($services->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $services->firstItem() }} à {{ $services->lastItem() }}
                    sur {{ $services->total() }} service(s)
                </small>
                {{ $services->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
