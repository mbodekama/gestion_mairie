<x-app-layout :title="__('Émissions de taxes')">

    <x-page-header titre="Gestion des Émissions de taxes" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('emissions.filtre')" :reset="route('emissions.index')"
        titre="Filtrer les émissions de taxes selon vos critères">
        <x-filtre.input name="numero_emission" label="N° Émission"    placeholder="Ex : EM2024000001" />
        <x-filtre.input name="etablissement"   label="Établissement"  placeholder="N° ou dénomination..." />
        <x-filtre.select name="nature_taxe_id"    label="Nature de taxe"
            :options="$naturesTaxe"  option-label="libelle_court" placeholder="— Toutes —" />
        <x-filtre.select name="periodicite_id"    label="Périodicité"
            :options="$periodicites" option-label="libelle_court" option-label-fallback="libelle" placeholder="— Toutes —" />
        <x-filtre.select name="exercice_fiscal_id" label="Exercice fiscal"
            :options="$exercices" option-label="annee" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-file-invoice me-2 text-primary"></span>
                Liste des émissions de taxes
                <span class="badge bg-secondary ms-2">{{ $emissions->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('emissions.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('emissions.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle émission
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero_emission" label="N° Émission"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="etablissement_id" label="Établissement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="etablissement_id" label="Contribuable"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="nature_taxe_id" label="Nature taxe"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="exercice_fiscal_id" label="Exercice" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="periodicite_id" label="Périodicité"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="montant_annuel" label="Montant dû" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th class="text-end">Solde dû</th>
                            <x-datatable.th-tri colonne="date_liquidation" label="Date liquidation" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($emissions as $emission)
                            @php
                                $montantDu  = $emission->montant_prorata > 0
                                    ? $emission->montant_prorata
                                    : $emission->montant_annuel;
                                $soldeDu    = $emission->soldeDu();
                                $soldeSolde = bccomp($soldeDu, '0', 2) <= 0;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $emission->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $emission->numero_emission }}</td>
                                <td>
                                    {{ $emission->etablissement?->numero }}
                                    @if ($emission->etablissement?->denomination)
                                        <br><small class="text-muted">{{ $emission->etablissement->denomination }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php $contrib = $emission->etablissement?->contribuable; @endphp
                                    @if ($contrib)
                                        {{ $contrib->type_personne === 'PP'
                                            ? $contrib->nom . ' ' . $contrib->prenoms
                                            : $contrib->raison_sociale }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $emission->natureTaxe?->libelle_court ?? '—' }}</td>
                                <td class="text-center">{{ $emission->exerciceFiscal?->annee ?? '—' }}</td>
                                <td>{{ $emission->periodicite?->libelle_court ?? $emission->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-end fw-semi-bold">
                                    {{ number_format((float) $montantDu, 0, ',', ' ') }} F
                                </td>
                                <td class="text-end fw-bold {{ $soldeSolde ? 'text-success' : 'text-danger' }}">
                                    {{ number_format((float) $soldeDu, 0, ',', ' ') }} F
                                </td>
                                <td class="text-center">
                                    {{ $emission->date_liquidation?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('emissions.show', $emission) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir la fiche">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('emissions.edit', $emission) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune émission trouvée pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($emissions->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $emissions->firstItem() }} à {{ $emissions->lastItem() }}
                    sur {{ $emissions->total() }} émission(s)
                </small>
                {{ $emissions->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
