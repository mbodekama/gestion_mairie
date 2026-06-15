<x-app-layout :title="__('Contrôles fiscaux')">

    <x-page-header titre="Gestion du Contrôle — Contrôles fiscaux" />

    @if (session('success'))
        <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== Filtres ===== --}}
    <x-filtre.card :action="route('controles.filtre')" :reset="route('controles.index')"
        titre="Filtrer les contrôles selon vos critères">
        <x-filtre.input name="numero" label="N° Contrôle" placeholder="CTRL..." col="col-md-2" />
        <x-filtre.input name="etablissement" label="Établissement / Contribuable" placeholder="Nom, raison sociale ou N°..." />
        <x-filtre.select name="etat_controle_id" label="État" :options="$etats" option-label="libelle" col="col-md-2" />
        <x-filtre.date name="date_du" label="Instruit du" col="col-md-2" />
        <x-filtre.date name="date_au" label="au" col="col-md-2" />
    </x-filtre.card>

    {{-- ===== Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-search-dollar me-2 text-primary"></span>
                Contrôles <span class="badge bg-secondary ms-2">{{ $controles->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('controles.export') }}"
                        class="btn btn-success btn-sm" title="Exporter en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('CONTROLE_INSTRUIRE')
                    <a href="{{ route('controles.create') }}" class="btn btn-primary btn-sm">
                        <span class="fas fa-plus me-1"></span>Nouveau contrôle
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-tri colonne="numero" label="N° Contrôle"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Contribuable / Établissement</th>
                            <x-datatable.th-tri colonne="etat_controle_id" label="État"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_instruction" label="Instruit le"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($controles as $controle)
                            @php
                                $contrib = $controle->etablissement?->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                        : ($contrib->raison_sociale ?? ''))
                                    : '—';
                                $couleur = [
                                    'INSTRUCTION' => 'secondary', 'VALIDE' => 'info',
                                    'EXECUTE' => 'primary', 'CLOTURE' => 'success', 'REDRESSE' => 'danger',
                                ][$controle->etatControle?->code] ?? 'secondary';
                            @endphp
                            <tr>
                                <td class="fw-semi-bold">{{ $controle->numero }}</td>
                                <td>
                                    {{ $nomContrib }}
                                    <div class="text-muted">
                                        {{ $controle->etablissement?->numero ?? '—' }}
                                        @if ($controle->etablissement?->denomination)
                                            — {{ $controle->etablissement->denomination }}
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge bg-{{ $couleur }}">{{ $controle->etatControle?->libelle ?? '—' }}</span></td>
                                <td class="text-muted">{{ $controle->date_instruction?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ trim(($controle->agentInstructeur?->nom ?? '') . ' ' . ($controle->agentInstructeur?->prenoms ?? '')) ?: '—' }}</td>
                                <td>
                                    <a href="{{ route('controles.show', $controle) }}"
                                       class="btn btn-sm btn-outline-info" title="Voir le détail">
                                        <span class="fas fa-eye me-1"></span>Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun contrôle trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($controles->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $controles->firstItem() }} à {{ $controles->lastItem() }}
                    sur {{ $controles->total() }} contrôle(s)
                </small>
                {{ $controles->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
