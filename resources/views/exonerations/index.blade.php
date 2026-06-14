<x-app-layout :title="__('Exonérations')">

    <x-page-header titre="Gestion des Exonérations" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('exonerations.filtre')" :reset="route('exonerations.index')"
        titre="Filtrer les exonérations selon vos critères">
        <x-filtre.input name="numero"       label="N° Exonération"   placeholder="Ex : EXO2024000001" />
        <x-filtre.input name="contribuable" label="Contribuable"      placeholder="Nom, raison sociale, N° identifiant..." />
        <x-filtre.select name="type_exoneration_id" label="Type d'exonération"
            :options="$typesExoneration" option-label="libelle" />
        <x-filtre.date name="date_debut_du" label="Début à partir du" />
        <x-filtre.date name="date_debut_au" label="Début jusqu'au" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-percent me-2 text-primary"></span>
                Liste des exonérations
                <span class="badge bg-secondary ms-2">{{ $exonerations->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('exonerations.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('exonerations.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle exonération
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero" label="N° Exonération"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="contribuable_id" label="Contribuable"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="type_exoneration_id" label="Type"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Réf. décret</th>
                            <x-datatable.th-tri colonne="date_decret" label="Date décret" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_debut" label="Date début" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_fin" label="Date fin" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-center">État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exonerations as $exoneration)
                            @php
                                $contrib = $exoneration->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? $contrib->nom . ' ' . $contrib->prenoms
                                        : $contrib->raison_sociale)
                                    : null;
                                $active = (!$exoneration->date_fin || $exoneration->date_fin->isFuture())
                                       && ($exoneration->date_debut && $exoneration->date_debut->isPast());
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $exoneration->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $exoneration->numero }}</td>
                                <td>
                                    @if ($nomContrib)
                                        {{ $nomContrib }}
                                        <br><small class="text-muted">{{ $contrib->numero_identifiant }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $exoneration->typeExoneration?->libelle ?? '—' }}</td>
                                <td>{{ $exoneration->reference_decret ?? '—' }}</td>
                                <td class="text-center">{{ $exoneration->date_decret?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">{{ $exoneration->date_debut?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">{{ $exoneration->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($active)
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($exoneration->date_fin && $exoneration->date_fin->isPast())
                                        <span class="badge bg-secondary">Expirée</span>
                                    @else
                                        <span class="badge bg-info">À venir</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('exonerations.show', $exoneration) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('exonerations.edit', $exoneration) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('exonerations.destroy', $exoneration) }}"
                                              onsubmit="return confirm('Confirmer la suppression de l\'exonération {{ $exoneration->numero }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune exonération trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($exonerations->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $exonerations->firstItem() }} à {{ $exonerations->lastItem() }}
                    sur {{ $exonerations->total() }} exonération(s)
                </small>
                {{ $exonerations->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
