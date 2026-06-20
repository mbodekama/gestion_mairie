<x-app-layout :title="__('Objectifs de recouvrement')">

    <x-page-header titre="Pilotage — Objectifs de recouvrement" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('pilotage.objectifs.filtre')" :reset="route('pilotage.objectifs.index')"
        titre="Filtrer les objectifs selon vos critères">
        <x-filtre.input name="annee" label="Année" placeholder="Ex : 2025" col="col-md-2" />
        <x-filtre.input name="montant_min" label="Montant min (FCFA)" placeholder="0" col="col-md-3" />
        <x-filtre.input name="montant_max" label="Montant max (FCFA)" placeholder="999999999" col="col-md-3" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-bullseye me-2 text-primary"></span>
                Objectifs de recouvrement
                <span class="badge bg-secondary ms-2">{{ $objectifs->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('pilotage.objectifs.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('pilotage.objectifs.create') }}" class="btn btn-primary btn-sm">
                    <span class="fas fa-plus me-1"></span>Nouvel objectif
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="annee" label="Année" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Période couverte</th>
                            <x-datatable.th-tri colonne="montant" label="Objectif (FCFA)" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="montant_revise" label="Révisé (FCFA)" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">Taux révision</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalObjectif = '0'; $totalRevise = '0'; @endphp
                        @forelse ($objectifs as $objectif)
                            @php
                                $tauxRevision = ($objectif->montant_revise && $objectif->montant > 0)
                                    ? round((($objectif->montant_revise - $objectif->montant) / $objectif->montant) * 100, 1)
                                    : null;
                                $totalObjectif = bcadd($totalObjectif, (string) $objectif->montant, 2);
                                $totalRevise   = bcadd($totalRevise, (string) ($objectif->montant_revise ?? 0), 2);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $objectif->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $objectif->annee }}</span>
                                </td>
                                <td>
                                    @if ($objectif->periode_debut && $objectif->periode_fin)
                                        <span class="fs-9">{{ $objectif->periode_debut->format('d/m/Y') }}
                                            <span class="text-400">→</span>
                                            {{ $objectif->periode_fin->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semi-bold">
                                    {{ number_format((float) $objectif->montant, 0, ',', ' ') }}
                                </td>
                                <td class="text-end">
                                    @if ($objectif->montant_revise !== null)
                                        {{ number_format((float) $objectif->montant_revise, 0, ',', ' ') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($tauxRevision !== null)
                                        <span class="badge {{ $tauxRevision >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $tauxRevision >= 0 ? '+' : '' }}{{ $tauxRevision }} %
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('pilotage.objectifs.show', $objectif) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('pilotage.objectifs.edit', $objectif) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('pilotage.objectifs.destroy', $objectif) }}"
                                              onsubmit="return confirm('Supprimer l\'objectif {{ $objectif->annee }} ?')">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun objectif trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($objectifs->count())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">Total de la page</td>
                                <td class="text-end">{{ number_format((float) $totalObjectif, 0, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format((float) $totalRevise, 0, ',', ' ') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if ($objectifs->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $objectifs->firstItem() }} à {{ $objectifs->lastItem() }}
                    sur {{ $objectifs->total() }} objectif(s)
                </small>
                {{ $objectifs->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
