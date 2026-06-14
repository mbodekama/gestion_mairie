<x-app-layout :title="__('Recouvrements')">

    <x-page-header titre="Gestion des Recouvrements" />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('recouvrements.filtre')" :reset="route('recouvrements.index')"
        titre="Filtrer les recouvrements selon vos critères">
        <x-filtre.input  name="numero_reglement" label="N° Règlement"    placeholder="Ex : RG2024000001" />
        <x-filtre.input  name="numero_quittance" label="N° Quittance"    placeholder="N° quittance..." />
        <x-filtre.input  name="contribuable"     label="Contribuable"    placeholder="Nom, raison sociale ou N° identifiant..." />
        <x-filtre.select name="exercice_fiscal_id" label="Exercice fiscal"
            :options="$exercices" option-label="annee" />
        <x-filtre.select name="mode_reglement_id" label="Mode de règlement" :options="$modes" />
        <x-filtre.select name="type_reglement_id" label="Type de règlement" :options="$types" />
        <x-filtre.date   name="date_du" label="Date du" />
        <x-filtre.date   name="date_au" label="Date au" />
    </x-filtre.card>

    {{-- ===== Card Tableau ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-credit-card me-2 text-primary"></span>
                Liste des recouvrements
                <span class="badge bg-secondary ms-2">{{ $reglements->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" form="formFiltres" formaction="{{ route('recouvrements.export') }}"
                        class="btn btn-success btn-sm" title="Exporter les données filtrées en Excel">
                    <span class="fas fa-file-excel me-1"></span>Exporter Excel
                </button>
                <a href="{{ route('recouvrements.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-1"></span>Nouveau règlement
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <x-datatable.th-check />
                            <x-datatable.th-tri colonne="numero_reglement" label="N° Règlement"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Contribuable</th>
                            <th>N° Émission / Cotisation</th>
                            <x-datatable.th-tri colonne="exercice_fiscal_id" label="Exercice" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="date_reglement" label="Date règlement" class="text-center"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="mode_reglement_id" label="Mode"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="type_reglement_id" label="Type"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="montant" label="Montant" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="montant_impute" label="Montant imputé" class="text-end"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <x-datatable.th-tri colonne="numero_quittance" label="N° Quittance"
                                :sort-actuel="$sortActuel" :dir-actuelle="$dirActuelle" />
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reglements as $reglement)
                            @php
                                $contribuable = $reglement->emissionTaxe?->etablissement?->contribuable
                                    ?? $reglement->emissionCotisation?->etablissement?->contribuable;

                                $numeroEmission = $reglement->emissionTaxe?->numero_emission
                                    ?? $reglement->emissionCotisation?->numero_article
                                    ?? '—';

                                $typeEmission = $reglement->emission_taxe_id ? 'Taxe' : 'Foncier';
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="selection[]"
                                           value="{{ $reglement->id }}"
                                           class="form-check-input ligne-check">
                                </td>
                                <td class="fw-semi-bold">{{ $reglement->numero_reglement }}</td>
                                <td>
                                    @if ($contribuable)
                                        {{ $contribuable->type_personne === 'PP'
                                            ? $contribuable->nom . ' ' . $contribuable->prenoms
                                            : $contribuable->raison_sociale }}
                                        <br>
                                        <small class="text-muted">{{ $contribuable->numero_identifiant }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $numeroEmission }}
                                    <br>
                                    <span class="badge {{ $reglement->emission_taxe_id ? 'bg-info' : 'bg-warning text-dark' }} fs-11">
                                        {{ $typeEmission }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $reglement->exerciceFiscal?->annee ?? '—' }}</td>
                                <td class="text-center">{{ $reglement->date_reglement?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $reglement->modeReglement?->libelle ?? '—' }}</td>
                                <td>{{ $reglement->typeReglement?->libelle ?? '—' }}</td>
                                <td class="text-end fw-semi-bold">
                                    {{ number_format((float) $reglement->montant, 0, ',', ' ') }} F
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format((float) $reglement->montant_impute, 0, ',', ' ') }} F
                                </td>
                                <td>{{ $reglement->numero_quittance ?? '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('recouvrements.show', $reglement) }}"
                                           class="btn btn-sm btn-outline-info" title="Voir le détail">
                                            <span class="fas fa-eye me-1"></span>Voir
                                        </a>
                                        <a href="{{ route('recouvrements.edit', $reglement) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <span class="fas fa-edit me-1"></span>Modifier
                                        </a>
                                        <form method="POST"
                                              action="{{ route('recouvrements.destroy', $reglement) }}"
                                              onsubmit="return confirm('Confirmer la suppression du règlement {{ $reglement->numero_reglement }} ?')">
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
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucun recouvrement trouvé pour ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($reglements->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $reglements->firstItem() }} à {{ $reglements->lastItem() }}
                    sur {{ $reglements->total() }} règlement(s)
                </small>
                {{ $reglements->links() }}
            </div>
        @endif
    </div>

    <x-datatable.traitement-lots />

</x-app-layout>
