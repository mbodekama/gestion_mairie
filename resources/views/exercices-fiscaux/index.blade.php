<x-app-layout :title="__('Exercices fiscaux')">

    <x-page-header titre="Gestion des Exercices fiscaux" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('exercices-fiscaux.filtre')" :reset="route('exercices-fiscaux.index')"
        titre="Filtrer les exercices fiscaux selon vos critères">
        <x-filtre.input name="annee" label="Année" placeholder="Ex : 2024" type="number" min="2000" max="2099" />
        <x-filtre.select name="cloture" label="État">
            <option value="0" @selected(request('cloture') === '0')>Ouvert</option>
            <option value="1" @selected(request('cloture') === '1')>Clôturé</option>
        </x-filtre.select>
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-calendar-alt me-2 text-primary"></span>
                Liste des exercices fiscaux
                <span class="badge bg-secondary ms-2">{{ $exercices->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('exercices-fiscaux.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('exercices-fiscaux.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvel exercice
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="annee" label="Année"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="collectivite_id" label="Collectivité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_debut" label="Date début" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_fin" label="Date fin" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="cloture" label="État" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exercices as $exercice)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $exercice->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-bold fs-8">{{ $exercice->annee }}</td>
                                <td>{{ $exercice->collectivite?->libelle ?? '—' }}</td>
                                <td class="text-center">{{ $exercice->date_debut?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">{{ $exercice->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($exercice->cloture)
                                        <span class="badge bg-danger">Clôturé</span>
                                    @else
                                        <span class="badge bg-success">Ouvert</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('exercices-fiscaux.show', $exercice) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir la fiche">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @unless ($exercice->cloture)
                                            <a href="{{ route('exercices-fiscaux.edit', $exercice) }}"
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <span class="fas fa-edit me-1"></span>Modifier
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('exercices-fiscaux.destroy', $exercice) }}"
                                                  onsubmit="return confirm('Confirmer la suppression de l\'exercice {{ $exercice->annee }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <span class="fas fa-trash me-1"></span>Supprimer
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun exercice fiscal trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($exercices->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $exercices->firstItem() }} à {{ $exercices->lastItem() }}
                    sur {{ $exercices->total() }} exercice(s)
                </small>
                {{ $exercices->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
