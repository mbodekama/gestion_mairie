<x-app-layout :title="__('Contribuables')">

    <x-page-header titre="Gestion des Contribuables" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('contribuables.filtre')" :reset="route('contribuables.index')"
        titre="Filtrer les contribuables selon vos critères">
        <x-filtre.input name="numero_identifiant" label="N° Identifiant" placeholder="Ex : CI2024000123" />
        <x-filtre.input name="nom" label="Nom / Raison sociale" placeholder="Recherche par nom..." />
        <x-filtre.select name="type_personne" label="Type de personne"
            :options="$typesPersonne" option-label="libelle" option-value="code" />
        <x-filtre.select name="statut" label="Statut"
            :options="$statuts" option-label="libelle" option-value="code" />
        <x-filtre.select name="regime_imposition_id" label="Régime d'imposition"
            :options="$regimes" option-label="libelle_court" option-label-fallback="libelle" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-users me-2 text-primary"></span>
                Liste des contribuables
                <span class="badge bg-secondary ms-2">{{ $contribuables->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('contribuables.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('contribuables.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouveau contribuable
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero_identifiant" label="N° Identifiant"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="numero_compte" label="N° Compte"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="type_personne" label="Type" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="nom" label="Nom / Raison sociale"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="cellulaire" label="Téléphone"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="regime_imposition_id" label="Régime"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="statut" label="Statut" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contribuables as $contribuable)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $contribuable->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $contribuable->numero_identifiant }}</td>
                                <td>{{ $contribuable->numero_compte }}</td>
                                <td class="text-center">
                                    @if ($contribuable->type_personne === 'PP')
                                        <span class="badge bg-info">PP</span>
                                    @else
                                        <span class="badge bg-warning text-dark">PM</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($contribuable->type_personne === 'PP')
                                        {{ $contribuable->nom }} {{ $contribuable->prenoms }}
                                    @else
                                        {{ $contribuable->raison_sociale }}
                                        @if ($contribuable->sigle)
                                            <span class="text-muted">({{ $contribuable->sigle }})</span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $contribuable->cellulaire ?? $contribuable->telephone ?? '—' }}</td>
                                <td>{{ $contribuable->regimeImposition?->libelle_court ?? '—' }}</td>
                                <td class="text-center">
                                    @php
                                        $statutClasse = match($contribuable->statut) {
                                            'ACTIF'    => 'success',
                                            'SUSPENDU' => 'warning',
                                            'RADIE'    => 'danger',
                                            default    => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statutClasse }}">{{ $contribuable->statut }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('contribuables.show', $contribuable) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir la fiche">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('contribuables.edit', $contribuable) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('contribuables.destroy', $contribuable) }}"
                                              onsubmit="return confirm('Confirmer la suppression de ce contribuable ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger" title="Supprimer">
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
                                    Aucun contribuable trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($contribuables->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $contribuables->firstItem() }} à {{ $contribuables->lastItem() }}
                    sur {{ $contribuables->total() }} contribuable(s)
                </small>
                {{ $contribuables->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
