<x-app-layout :title="__('Contrôle fiscal')">

    <x-page-header titre="Contrôle fiscal &amp; Convocations" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('controle-fiscal.filtre')" :reset="route('controle-fiscal.index')"
        titre="Filtrer les contrôles fiscaux selon vos critères">
        <x-filtre.input name="numero"        label="N° Convocation"    placeholder="Ex : CV2024000001" />
        <x-filtre.input name="etablissement" label="Établissement / Contribuable" placeholder="Dénomination, nom, N° identifiant..." />
        <x-filtre.input name="annee"         label="Année fiscale"     placeholder="Ex : 2024" type="number" min="2000" max="2099" />
        <x-filtre.date  name="date_du"       label="Convoqué à partir du" />
        <x-filtre.date  name="date_au"       label="Convoqué jusqu'au" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-shield-alt me-2 text-primary"></span>
                Liste des contrôles fiscaux
                <span class="badge bg-secondary ms-2">{{ $convocations->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('controle-fiscal.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                @can('CONTROLE_GERER')
                <a href="{{ route('controle-fiscal.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouvelle convocation
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
                            <x-datatable.th-tri colonne="numero" label="N° Convocation"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="etablissement_id" label="Établissement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Contribuable</th>
                            <x-datatable.th-tri colonne="annee" label="Année" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Motif</th>
                            <x-datatable.th-tri colonne="montant_du" label="Montant dû" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_convocation" label="Date convocation" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_reponse" label="Date réponse" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalMontantDu = '0'; @endphp
                        @forelse ($convocations as $convocation)
                            @php
                                $contrib = $convocation->etablissement?->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? $contrib->nom . ' ' . $contrib->prenoms
                                        : $contrib->raison_sociale)
                                    : null;
                                $repondu = filled($convocation->date_reponse);
                                $totalMontantDu = bcadd($totalMontantDu, (string) ($convocation->montant_du ?? 0), 2);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $convocation->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $convocation->numero }}</td>
                                <td>
                                    {{ $convocation->etablissement?->denomination ?? $convocation->etablissement?->numero ?? '—' }}
                                </td>
                                <td>
                                    @if ($nomContrib)
                                        {{ $nomContrib }}
                                        <br><small class="text-muted">{{ $contrib->numero_identifiant }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $convocation->annee }}</td>
                                <td>
                                    <span title="{{ $convocation->motif }}">
                                        {{ Str::limit($convocation->motif ?? '—', 40) }}
                                    </span>
                                </td>
                                <td class="text-end fw-semi-bold {{ $convocation->montant_du > 0 ? 'text-danger' : '' }}">
                                    @if ($convocation->montant_du > 0)
                                        {{ number_format((float) $convocation->montant_du, 0, ',', ' ') }} F
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $convocation->date_convocation?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($repondu)
                                        <span class="badge bg-success">{{ $convocation->date_reponse->format('d/m/Y') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('controle-fiscal.show', $convocation) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        @can('CONTROLE_GERER')
                                        <a href="{{ route('controle-fiscal.edit', $convocation) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        @endcan
                                        @can('CONTROLE_GERER')
                                        <form method="POST"
                                              action="{{ route('controle-fiscal.destroy', $convocation) }}"
                                              onsubmit="return confirm('Confirmer la suppression de la convocation {{ $convocation->numero }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <span class="fas fa-trash me-1"></span>Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun contrôle fiscal trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($convocations->count())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">Total de la page</td>
                                <td class="text-end text-danger">{{ number_format((float) $totalMontantDu, 0, ',', ' ') }} F</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if ($convocations->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $convocations->firstItem() }} à {{ $convocations->lastItem() }}
                    sur {{ $convocations->total() }} convocation(s)
                </small>
                {{ $convocations->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
