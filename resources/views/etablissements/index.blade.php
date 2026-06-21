<x-app-layout :title="__('Établissements')">

    <x-page-header titre="Gestion des Établissements" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('etablissements.filtre')" :reset="route('etablissements.index')"
        titre="Filtrer les établissements selon vos critères">
        <x-filtre.input name="numero"       label="N° Établissement" placeholder="Ex : ET0001234" />
        <x-filtre.input name="denomination" label="Dénomination"     placeholder="Nom de l'établissement..." />
        <x-filtre.input name="contribuable" label="Contribuable"      placeholder="Nom, raison sociale ou N° identifiant..." />
        <x-filtre.select name="type_etablissement" label="Type">
            <option value="PRINCIPAL"  @selected(request('type_etablissement') === 'PRINCIPAL')>Principal</option>
            <option value="SECONDAIRE" @selected(request('type_etablissement') === 'SECONDAIRE')>Secondaire</option>
        </x-filtre.select>
        <x-filtre.select name="statut" label="Statut">
            <option value="ACTIF"     @selected(request('statut') === 'ACTIF')>Actif</option>
            <option value="CESSE"     @selected(request('statut') === 'CESSE')>Cessé</option>
            <option value="TRANSFERE" @selected(request('statut') === 'TRANSFERE')>Transféré</option>
            <option value="SOMMEIL"   @selected(request('statut') === 'SOMMEIL')>En sommeil</option>
        </x-filtre.select>
        <x-filtre.select name="commune_id"  label="Commune"  :options="$communes"  placeholder="— Toutes —" />
        <x-filtre.select name="activite_id" label="Activité" :options="$activites" placeholder="— Toutes —" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-store me-2 text-primary"></span>
                Liste des établissements
                <span class="badge bg-secondary ms-2">{{ $etablissements->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('etablissements.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('ETAB_CREER')
                <a href="{{ route('etablissements.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvel établissement
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero" label="N° Établissement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="contribuable_id" label="Contribuable"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="denomination" label="Dénomination"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="type_etablissement" label="Type" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="activite_id" label="Activité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="commune_id" label="Commune"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="telephone" label="Tél."
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="statut" label="Statut" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($etablissements as $etablissement)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $etablissement->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $etablissement->numero }}</td>
                                <td>
                                    @if ($etablissement->contribuable)
                                        @if ($etablissement->contribuable->type_personne === 'PP')
                                            {{ $etablissement->contribuable->nom }} {{ $etablissement->contribuable->prenoms }}
                                        @else
                                            {{ $etablissement->contribuable->raison_sociale }}
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $etablissement->contribuable->numero_identifiant }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $etablissement->denomination ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($etablissement->type_etablissement === 'PRINCIPAL')
                                        <span class="badge bg-primary">Principal</span>
                                    @else
                                        <span class="badge bg-secondary">Secondaire</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $etablissement->activite?->libelle }}">
                                        {{ Str::limit($etablissement->activite?->libelle ?? '—', 35) }}
                                    </span>
                                </td>
                                <td>{{ $etablissement->commune?->libelle ?? '—' }}</td>
                                <td>{{ $etablissement->telephone ?? '—' }}</td>
                                <td class="text-center">
                                    @php
                                        $statutClasse = match($etablissement->statut) {
                                            'ACTIF'     => 'success',
                                            'CESSE'     => 'danger',
                                            'TRANSFERE' => 'info',
                                            'SOMMEIL'   => 'warning',
                                            default     => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statutClasse }}">{{ $etablissement->statut }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('etablissements.show', $etablissement) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir la fiche">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('ETAB_MODIFIER')
                                        <a href="{{ route('etablissements.edit', $etablissement) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun établissement trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($etablissements->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $etablissements->firstItem() }} à {{ $etablissements->lastItem() }}
                    sur {{ $etablissements->total() }} établissement(s)
                </small>
                {{ $etablissements->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
