<x-app-layout :title="__('Redressements')">

    <x-page-header titre="Gestion du Contrôle — Redressements" />


    {{-- ===== Filtres ===== --}}
    <x-filtre.card :action="route('redressements.filtre')" :reset="route('redressements.index')"
        titre="Filtrer les redressements">
        <x-filtre.input name="numero" label="N° Redressement" placeholder="REDR..." col="col-md-3" />
        <x-filtre.input name="etablissement" label="Établissement / Contribuable" placeholder="Nom, raison sociale ou N°..." />
        <x-filtre.select name="etat" label="État" col="col-md-3">
            <option value="ouvert"  @selected(request('etat') === 'ouvert')>Ouvert</option>
            <option value="notifie" @selected(request('etat') === 'notifie')>Notifié</option>
            <option value="solde"   @selected(request('etat') === 'solde')>Soldé</option>
            <option value="annule"  @selected(request('etat') === 'annule')>Annulé</option>
        </x-filtre.select>
    </x-filtre.card>

    {{-- ===== Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-gavel me-2 text-primary"></span>
                Redressements <span class="badge bg-secondary ms-2">{{ $redressements->total() }}</span>
            </h5>
            <button type="submit" form="formFiltres" formaction="{{ route('redressements.export') }}"
                    class="btn btn-success btn-sm" title="Exporter en Excel">
                <span class="fas fa-file-excel me-1"></span>Exporter Excel
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-tri colonne="numero" label="N° Redressement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Contribuable / Établissement</th>
                            <th class="text-end">Total</th>
                            <x-datatable.th-tri colonne="etat" label="État"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_redressement" label="Date"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalRedressements = '0'; @endphp
                        @forelse ($redressements as $r)
                            @php
                                $contrib = $r->controleFiscal?->etablissement?->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                        : ($contrib->raison_sociale ?? ''))
                                    : '—';
                                $couleur = ['ouvert'=>'warning','notifie'=>'info','solde'=>'success','annule'=>'secondary'][$r->etat] ?? 'secondary';
                                $totalRedressements = bcadd($totalRedressements, (string) $r->montant_total, 2);
                            @endphp
                            <tr>
                                <td class="fw-semi-bold">{{ $r->numero }}</td>
                                <td>
                                    {{ $nomContrib }}
                                    <div class="text-muted">{{ $r->controleFiscal?->etablissement?->numero ?? '—' }}</div>
                                </td>
                                <td class="text-end fw-bold text-danger">{{ number_format((float) $r->montant_total, 0, ',', ' ') }} FCFA</td>
                                <td><span class="badge bg-{{ $couleur }} text-{{ $couleur === 'warning' ? 'dark' : 'white' }}">{{ ucfirst($r->etat) }}</span></td>
                                <td class="text-muted">{{ $r->date_redressement?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('redressements.show', $r) }}" class="btn btn-sm btn-outline-info">
                                        <span class="fas fa-eye me-1"></span>Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun redressement trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($redressements->count())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">Total de la page</td>
                                <td class="text-end text-danger">{{ number_format((float) $totalRedressements, 0, ',', ' ') }} FCFA</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if ($redressements->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $redressements->firstItem() }} à {{ $redressements->lastItem() }}
                    sur {{ $redressements->total() }} redressement(s)
                </small>
                {{ $redressements->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
