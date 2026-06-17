<x-app-layout :title="__('Barèmes de taxe')">

    <x-page-header titre="Gestion des Barèmes de taxe" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('parametrage.baremes-taxe.filtre')" :reset="route('parametrage.baremes-taxe.index')"
        titre="Filtrer les barèmes selon vos critères">
        <x-filtre.select name="nature_taxe_id" label="Nature de taxe"
            :options="$naturesTaxe" option-label="libelle_court" option-label-fallback="libelle" />
        <x-filtre.select name="periodicite_id" label="Périodicité"
            :options="$periodicites" option-label="libelle" />
        <x-filtre.select name="categorie_activite_id" label="Catégorie d'activité"
            :options="$categories" option-label="libelle" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-percent me-2 text-primary"></span>
                Liste des barèmes de taxe
                <span class="badge bg-secondary ms-2">{{ $baremes->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('parametrage.baremes-taxe.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('parametrage.baremes-taxe.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouveau barème
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="nature_taxe_id" label="Nature de taxe"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="categorie_activite_id" label="Catégorie"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="periodicite_id" label="Périodicité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="ca_borne_inf" label="CA borne inf." class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="ca_borne_sup" label="CA borne sup." class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="taux" label="Taux (%)" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($baremes as $bareme)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $bareme->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-bold">{{ $bareme->natureTaxe?->libelle_court ?? $bareme->natureTaxe?->libelle ?? '—' }}</td>
                                <td>{{ $bareme->categorieActivite?->libelle ?? 'Toutes' }}</td>
                                <td>{{ $bareme->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float) $bareme->ca_borne_inf, 0, ',', ' ') }}</td>
                                <td class="text-end">
                                    @if ((float) $bareme->ca_borne_sup === 0.0)
                                        <span class="badge bg-info">et plus</span>
                                    @else
                                        {{ number_format((float) $bareme->ca_borne_sup, 0, ',', ' ') }}
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ rtrim(rtrim(number_format((float) $bareme->taux, 4, ',', ' '), '0'), ',') }} %</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('parametrage.baremes-taxe.show', $bareme) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir la fiche">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('parametrage.baremes-taxe.edit', $bareme) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('parametrage.baremes-taxe.destroy', $bareme) }}"
                                              onsubmit="return confirm('Confirmer la suppression de ce barème ?')">
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
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun barème trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>

        @if ($baremes->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $baremes->firstItem() }} à {{ $baremes->lastItem() }}
                    sur {{ $baremes->total() }} barème(s)
                </small>
                {{ $baremes->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
