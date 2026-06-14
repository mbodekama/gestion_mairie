<x-app-layout :title="'Établissement — ' . ($etablissement->denomination ?? $etablissement->numero)">

@php
    $contrib = $etablissement->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';

    $statutClasse = match($etablissement->statut) {
        'ACTIF'     => 'success',
        'CESSE'     => 'danger',
        'TRANSFERE' => 'warning',
        'SOMMEIL'   => 'secondary',
        default     => 'secondary',
    };

    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

{{-- =====================================================================
     BANDEAU
     ===================================================================== --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #1a6e3c 0%, #0d4023 100%); min-height: 130px;">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18); font-size: 1.6rem;">
                <span class="fas fa-store"></span>
            </div>

            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">
                    {{ $etablissement->denomination ?? '(sans dénomination)' }}
                </h4>
                <p class="mb-1 text-white-50 fs-9">
                    Contribuable :
                    <a href="{{ route('contribuables.show', $contrib) }}" class="text-white-50">
                        {{ $nomContrib }}
                    </a>
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    <span class="badge bg-white text-dark fs-9">
                        <span class="fas fa-hashtag me-1"></span>{{ $etablissement->numero ?? '—' }}
                    </span>
                    <span class="badge bg-{{ $etablissement->type_etablissement === 'PRINCIPAL' ? 'primary' : 'info' }} fs-9">
                        {{ $etablissement->type_etablissement }}
                    </span>
                    <span class="badge bg-{{ $statutClasse }}">{{ $etablissement->statut }}</span>
                    @if ($etablissement->activite)
                        <span class="badge bg-light text-dark border">
                            <span class="fas fa-industry me-1"></span>{{ $etablissement->activite->libelle }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                <a href="{{ route('etablissements.edit', $etablissement) }}"
                   class="btn btn-light btn-sm">
                    <span class="fas fa-edit me-1"></span>Modifier
                </a>
                <a href="{{ route('emissions.create', ['etablissement_id' => $etablissement->id]) }}"
                   class="btn btn-light btn-sm">
                    <span class="fas fa-plus me-1"></span>Émission
                </a>
                <a href="{{ route('contribuables.show', $contrib) }}"
                   class="btn btn-outline-light btn-sm">
                    <span class="fas fa-arrow-left me-1"></span>Retour
                </a>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =====================================================================
     KPIs
     ===================================================================== --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-file-invoice text-primary fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Émissions</div>
                    <div class="fs-5 fw-bold text-900">{{ $etablissement->emissionsTaxe->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-success flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-money-bill-wave text-success fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Total émis</div>
                    <div class="fs-7 fw-bold text-900">{{ $fcfa($totalEmis) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-info flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-check-circle text-info fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Recouvré</div>
                    <div class="fs-7 fw-bold text-900">{{ $fcfa($totalRegle) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-warning flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-exclamation-triangle text-warning fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Solde dû</div>
                    <div class="fs-7 fw-bold text-900">{{ $fcfa($totalSolde) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     CORPS PRINCIPAL : info générales + localisation
     ===================================================================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="tabEtab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-infos"
                                type="button" role="tab">
                            <span class="fas fa-info-circle me-1"></span>Informations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-localisation"
                                type="button" role="tab">
                            <span class="fas fa-map-marker-alt me-1"></span>Localisation
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body tab-content">
                {{-- Infos générales --}}
                <div class="tab-pane fade show active" id="tab-infos" role="tabpanel">
                    <dl class="row mb-0 fs-9">
                        <dt class="col-5 text-600">N° Établissement</dt>
                        <dd class="col-7 fw-semi-bold">{{ $etablissement->numero ?? '—' }}</dd>

                        <dt class="col-5 text-600">Dénomination</dt>
                        <dd class="col-7">{{ $etablissement->denomination ?? '—' }}</dd>

                        <dt class="col-5 text-600">Contribuable</dt>
                        <dd class="col-7">
                            <a href="{{ route('contribuables.show', $contrib) }}">
                                {{ $nomContrib }}
                                @if ($contrib)
                                    <span class="text-500">({{ $contrib->numero_identifiant }})</span>
                                @endif
                            </a>
                        </dd>

                        <dt class="col-5 text-600">Type</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $etablissement->type_etablissement === 'PRINCIPAL' ? 'primary' : 'info' }}">
                                {{ $etablissement->type_etablissement }}
                            </span>
                        </dd>

                        <dt class="col-5 text-600">Activité</dt>
                        <dd class="col-7">{{ $etablissement->activite?->libelle ?? '—' }}</dd>

                        <dt class="col-5 text-600">Date début activité</dt>
                        <dd class="col-7">{{ $etablissement->date_debut_activite?->format('d/m/Y') ?? '—' }}</dd>

                        @if ($etablissement->date_cessation)
                            <dt class="col-5 text-600">Date de cessation</dt>
                            <dd class="col-7">{{ $etablissement->date_cessation->format('d/m/Y') }}</dd>
                        @endif

                        <dt class="col-5 text-600">Statut</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $statutClasse }}">{{ $etablissement->statut }}</span>
                        </dd>
                    </dl>
                </div>

                {{-- Localisation --}}
                <div class="tab-pane fade" id="tab-localisation" role="tabpanel">
                    <dl class="row mb-0 fs-9">
                        <dt class="col-5 text-600">Commune</dt>
                        <dd class="col-7">{{ $etablissement->commune?->libelle ?? '—' }}</dd>

                        <dt class="col-5 text-600">Zone fiscale</dt>
                        <dd class="col-7">{{ $etablissement->zoneFiscale?->libelle ?? '—' }}</dd>

                        <dt class="col-5 text-600">Adresse</dt>
                        <dd class="col-7">{{ $etablissement->adresse ?? '—' }}</dd>

                        <dt class="col-5 text-600">Téléphone</dt>
                        <dd class="col-7">{{ $etablissement->telephone ?? '—' }}</dd>

                        <dt class="col-5 text-600">Email</dt>
                        <dd class="col-7">{{ $etablissement->email ?? '—' }}</dd>

                        <dt class="col-5 text-600">Boîte postale</dt>
                        <dd class="col-7">{{ $etablissement->boite_postale ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0">
                    <span class="fas fa-calendar-alt me-2 text-primary"></span>Cycle de vie
                </h5>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-6 text-600">Créé le</dt>
                    <dd class="col-6">{{ $etablissement->created_at?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-6 text-600">Modifié le</dt>
                    <dd class="col-6">{{ $etablissement->updated_at?->format('d/m/Y') ?? '—' }}</dd>

                    @if ($etablissement->date_cessation)
                        <dt class="col-6 text-600">Cessation</dt>
                        <dd class="col-6">{{ $etablissement->date_cessation->format('d/m/Y') }}</dd>
                    @endif
                    @if ($etablissement->date_transfert)
                        <dt class="col-6 text-600">Transfert</dt>
                        <dd class="col-6">{{ $etablissement->date_transfert->format('d/m/Y') }}</dd>
                    @endif
                    @if ($etablissement->date_sommeil)
                        <dt class="col-6 text-600">Mise en sommeil</dt>
                        <dd class="col-6">{{ $etablissement->date_sommeil->format('d/m/Y') }}</dd>
                    @endif
                </dl>

                @if ($etablissement->supprime_le)
                    <div class="alert alert-danger mt-3 py-2 fs-9">
                        <span class="fas fa-trash me-1"></span>
                        Supprimé le {{ $etablissement->supprime_le->format('d/m/Y H:i') }}
                    </div>
                @endif

                <div class="mt-3 pt-3 border-top">
                    <form method="POST" action="{{ route('etablissements.destroy', $etablissement) }}"
                          onsubmit="return confirm('Supprimer définitivement cet établissement ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <span class="fas fa-trash me-1"></span>Supprimer l'établissement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     ÉMISSIONS DE TAXE
     ===================================================================== --}}
<div class="card mb-3">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <span class="fas fa-file-invoice-dollar me-2 text-primary"></span>
            Émissions de taxe
            <span class="badge bg-secondary ms-2">{{ $etablissement->emissionsTaxe->count() }}</span>
        </h5>
        <a href="{{ route('emissions.create', ['etablissement_id' => $etablissement->id]) }}"
           class="btn btn-primary btn-sm">
            <span class="fas fa-plus me-1"></span>Nouvelle émission
        </a>
    </div>
    @if ($etablissement->emissionsTaxe->isEmpty())
        <div class="card-body text-center py-4 text-500 fs-9">
            <span class="fas fa-file-invoice fa-2x mb-2 d-block opacity-40"></span>
            Aucune émission enregistrée
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>N° Émission</th>
                            <th>Nature taxe</th>
                            <th>Exercice</th>
                            <th>Périodicité</th>
                            <th class="text-end">Montant émis</th>
                            <th class="text-end">Recouvré</th>
                            <th class="text-end">Solde dû</th>
                            <th>Date liquidation</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($etablissement->emissionsTaxe as $em)
                            @php
                                $montantBase = $em->montant_prorata > 0 ? $em->montant_prorata : $em->montant_annuel;
                                $soldeDu     = $em->soldeDu();
                                $regle       = bcsub((string) $montantBase, $soldeDu, 2);
                            @endphp
                            <tr>
                                <td class="fw-semi-bold">
                                    <a href="{{ route('emissions.show', $em) }}">
                                        {{ $em->numero_emission }}
                                    </a>
                                </td>
                                <td>{{ $em->natureTaxe?->libelle_court ?? '—' }}</td>
                                <td>{{ $em->exerciceFiscal?->annee ?? '—' }}</td>
                                <td>{{ $em->periodicite?->libelle_court ?? $em->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-end">{{ $fcfa($montantBase) }}</td>
                                <td class="text-end text-success">{{ $fcfa($regle) }}</td>
                                <td class="text-end {{ (float) $soldeDu > 0 ? 'text-danger fw-semi-bold' : 'text-success' }}">
                                    {{ $fcfa($soldeDu) }}
                                </td>
                                <td>{{ $em->date_liquidation?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('emissions.show', $em) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <span class="fas fa-eye"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end fs-9">Totaux</td>
                            <td class="text-end">{{ $fcfa($totalEmis) }}</td>
                            <td class="text-end text-success">{{ $fcfa($totalRegle) }}</td>
                            <td class="text-end {{ (float) $totalSolde > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $fcfa($totalSolde) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Documents & Historique --}}
<x-documents.panneau :model="$etablissement" />

<x-historique.timeline
    :historiques="$historiques"
    :labels="$etablissement->auditLabels ?? []"
    :creeAt="$etablissement->created_at"
    :misAJourAt="$etablissement->updated_at"
/>

</x-app-layout>
