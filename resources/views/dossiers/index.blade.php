<x-app-layout :title="__('Dossiers administratifs')">

    <x-page-header titre="Gestion des Dossiers administratifs" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('dossiers.filtre')" :reset="route('dossiers.index')"
        titre="Filtrer les dossiers selon vos critères">
        <x-filtre.input name="numero"        label="N° Dossier"              placeholder="Ex : DS000001" />
        <x-filtre.input name="etablissement" label="Établissement / Contribuable" placeholder="Dénomination, nom, N° identifiant..." />
        <x-filtre.select name="famille_etat_dossier_id" label="Famille d'état"
            :options="$famillesEtat" option-label="libelle" />
        <x-filtre.select name="categorie_etat_dossier_id" label="Catégorie d'état"
            :options="$categoriesEtat" option-label="libelle" />
        <x-filtre.select name="archive" label="Archivé">
            <option value="0" @selected(request('archive') === '0')>Non archivé</option>
            <option value="1" @selected(request('archive') === '1')>Archivé</option>
        </x-filtre.select>
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-folder-open me-2 text-primary"></span>
                Liste des dossiers administratifs
                <span class="badge bg-secondary ms-2">{{ $dossiers->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('dossiers.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('dossiers.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouveau dossier
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero" label="N° Dossier"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="etablissement_id" label="Établissement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Contribuable</th>
                            <x-datatable.th-tri colonne="famille_etat_dossier_id" label="Famille état"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="categorie_etat_dossier_id" label="Catégorie état"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_creation" label="Date création" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_sortie" label="Date sortie" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="archive" label="Archivé" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dossiers as $dossier)
                            @php
                                $contrib = $dossier->etablissement?->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? $contrib->nom . ' ' . $contrib->prenoms
                                        : $contrib->raison_sociale)
                                    : null;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $dossier->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $dossier->numero }}</td>
                                <td>
                                    {{ $dossier->etablissement?->denomination ?? $dossier->etablissement?->numero ?? '—' }}
                                </td>
                                <td>
                                    @if ($nomContrib)
                                        {{ $nomContrib }}
                                        <br><small class="text-muted">{{ $contrib->numero_identifiant }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $dossier->familleEtatDossier?->libelle ?? '—' }}</td>
                                <td>{{ $dossier->categorieEtatDossier?->libelle ?? '—' }}</td>
                                <td class="text-center">{{ $dossier->date_creation?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">{{ $dossier->date_sortie?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($dossier->archive)
                                        <span class="badge bg-secondary"><span class="fas fa-archive me-1"></span>Archivé</span>
                                    @else
                                        <span class="badge bg-success">Actif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('dossiers.show', $dossier) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le dossier">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('dossiers.edit', $dossier) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('dossiers.destroy', $dossier) }}"
                                              onsubmit="return confirm('Confirmer la suppression du dossier {{ $dossier->numero }} ?')">
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
                                    Aucun dossier trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($dossiers->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $dossiers->firstItem() }} à {{ $dossiers->lastItem() }}
                    sur {{ $dossiers->total() }} dossier(s)
                </small>
                {{ $dossiers->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
