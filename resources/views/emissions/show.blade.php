<x-app-layout :title="'Émission — ' . $emission->numero_emission">

@php
    $contrib = $emission->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';

    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';

    $tauxRecouvrement = (float) $montantBase > 0
        ? round((float) $totalRegle / (float) $montantBase * 100, 1)
        : 0;
@endphp

{{-- =====================================================================
     BANDEAU
     ===================================================================== --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #d63384 0%, #7d1a49 100%); min-height: 130px;">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18); font-size: 1.5rem;">
                <span class="fas fa-file-invoice-dollar"></span>
            </div>

            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $emission->numero_emission }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $nomContrib }}
                    @if ($emission->etablissement)
                        — Étab. <a href="{{ route('etablissements.show', $emission->etablissement) }}" class="text-white-50">{{ $emission->etablissement->numero }}</a>
                    @endif
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    <span class="badge bg-white text-dark fs-9">
                        {{ $emission->natureTaxe?->libelle_court ?? '—' }}
                    </span>
                    <span class="badge bg-light text-dark border fs-9">
                        <span class="fas fa-calendar me-1"></span>{{ $emission->exerciceFiscal?->annee ?? '—' }}
                    </span>
                    @if ($emission->exerciceFiscal?->cloture)
                        <span class="badge bg-danger">Exercice clôturé</span>
                    @endif
                    <span class="badge bg-light text-dark border fs-9">
                        {{ $emission->periodicite?->libelle_court ?? $emission->periodicite?->libelle ?? '—' }}
                    </span>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                <a href="{{ route('emissions.avis', $emission) }}" class="btn btn-light btn-sm" target="_blank">
                    <span class="fas fa-file-pdf me-1"></span>Avis
                </a>
                @if (!$emission->exerciceFiscal?->cloture)
                    <a href="{{ route('emissions.edit', $emission) }}"
                       class="btn btn-light btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    <a href="{{ route('recouvrements.create', ['emission_taxe_id' => $emission->id]) }}"
                       class="btn btn-light btn-sm">
                        <span class="fas fa-plus me-1"></span>Règlement
                    </a>
                    <x-suppression
                        :action="route('emissions.destroy', $emission)"
                        :bloquee="$suppressionBloquee"
                        raison="Émission rattachée à des recouvrements : suppression impossible."
                        libelle="cette émission"
                        id="modalSuppEmission" />
                @endif
                <a href="{{ $emission->etablissement ? route('etablissements.show', $emission->etablissement) : route('emissions.index') }}"
                   class="btn btn-outline-light btn-sm">
                    <span class="fas fa-arrow-left me-1"></span>Retour
                </a>
            </div>
        </div>
    </div>
</div>


{{-- =====================================================================
     KPIs
     ===================================================================== --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-money-bill text-primary fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Montant émis</div>
                    <div class="fs-7 fw-bold text-900">{{ $fcfa($montantBase) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-success flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-check-circle text-success fs-7"></span>
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
                <div class="rounded-circle d-flex align-items-center justify-content-center {{ (float) $soldeDu > 0 ? 'bg-soft-danger' : 'bg-soft-success' }} flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-exclamation-circle {{ (float) $soldeDu > 0 ? 'text-danger' : 'text-success' }} fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Solde dû</div>
                    <div class="fs-7 fw-bold {{ (float) $soldeDu > 0 ? 'text-danger' : 'text-success' }}">{{ $fcfa($soldeDu) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-info flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-percent text-info fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Taux recouvrement</div>
                    <div class="fs-5 fw-bold text-900">{{ $tauxRecouvrement }} %</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     CORPS : détails émission
     ===================================================================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-info-circle me-2 text-primary"></span>Détails de l'émission
                </h5>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">N° Émission</dt>
                    <dd class="col-7 fw-semi-bold">{{ $emission->numero_emission }}</dd>

                    <dt class="col-5 text-600">N° Fiche</dt>
                    <dd class="col-7">{{ $emission->numero_fiche ?? '—' }}</dd>

                    <dt class="col-5 text-600">N° Article</dt>
                    <dd class="col-7">{{ $emission->numero_article ?? '—' }}</dd>

                    <dt class="col-5 text-600">Contribuable</dt>
                    <dd class="col-7">
                        @if ($contrib)
                            <a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-5 text-600">Établissement</dt>
                    <dd class="col-7">
                        @if ($emission->etablissement)
                            <a href="{{ route('etablissements.show', $emission->etablissement) }}">
                                {{ $emission->etablissement->numero }}
                                @if ($emission->etablissement->denomination)
                                    — {{ $emission->etablissement->denomination }}
                                @endif
                            </a>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-5 text-600">Nature de taxe</dt>
                    <dd class="col-7">{{ $emission->natureTaxe?->libelle_court ?? '—' }}</dd>

                    <dt class="col-5 text-600">Exercice</dt>
                    <dd class="col-7">{{ $emission->exerciceFiscal?->annee ?? '—' }}</dd>

                    <dt class="col-5 text-600">Périodicité</dt>
                    <dd class="col-7">{{ $emission->periodicite?->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">CA annuel</dt>
                    <dd class="col-7">{{ $emission->ca_annuel ? $fcfa($emission->ca_annuel) : '—' }}</dd>

                    <dt class="col-5 text-600">Montant annuel</dt>
                    <dd class="col-7">{{ $fcfa($emission->montant_annuel) }}</dd>

                    @if ((float) $emission->montant_prorata > 0)
                        <dt class="col-5 text-600">Nb mois prorata</dt>
                        <dd class="col-7">{{ $emission->nb_mois_prorata ?? '—' }}</dd>

                        <dt class="col-5 text-600">Montant prorata</dt>
                        <dd class="col-7 fw-semi-bold text-primary">{{ $fcfa($emission->montant_prorata) }}</dd>
                    @endif

                    @if ($emission->exoneration_id)
                        <dt class="col-5 text-600">Exonération</dt>
                        <dd class="col-7">
                            <a href="{{ route('exonerations.show', $emission->exoneration_id) }}">{{ $emission->exoneration?->numero }}</a>
                            <span class="badge bg-success ms-1">−{{ $fcfa($emission->montant_exonere) }}</span>
                        </dd>
                    @endif

                    <dt class="col-5 text-600">Date déclaration</dt>
                    <dd class="col-7">{{ $emission->date_declaration?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-5 text-600">Date liquidation</dt>
                    <dd class="col-7">{{ $emission->date_liquidation?->format('d/m/Y') ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $emission->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Sidebar : actions et métadonnées --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0">
                    <span class="fas fa-cogs me-2 text-primary"></span>Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if (!$emission->exerciceFiscal?->cloture)
                        <a href="{{ route('recouvrements.create', ['emission_taxe_id' => $emission->id]) }}"
                           class="btn btn-success btn-sm">
                            <span class="fas fa-plus me-1"></span>Enregistrer un règlement
                        </a>
                        <a href="{{ route('emissions.edit', $emission) }}"
                           class="btn btn-outline-primary btn-sm">
                            <span class="fas fa-edit me-1"></span>Modifier l'émission
                        </a>
                    @endif
                </div>

                <hr class="my-3">

                <dl class="row mb-0 fs-9">
                    <dt class="col-6 text-600">Créé le</dt>
                    <dd class="col-6">{{ $emission->created_at?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-6 text-600">Modifié le</dt>
                    <dd class="col-6">{{ $emission->updated_at?->format('d/m/Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     RÈGLEMENTS
     ===================================================================== --}}
<div class="card mb-3 card-section">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 d-flex align-items-center">
            <span class="num-section">02</span>
            <span class="fas fa-hand-holding-usd me-2 text-primary"></span>
            Règlements
            <span class="badge bg-secondary ms-2">{{ $emission->reglements->count() }}</span>
        </h5>
        @if (!$emission->exerciceFiscal?->cloture)
            <a href="{{ route('recouvrements.create', ['emission_taxe_id' => $emission->id]) }}"
               class="btn btn-success btn-sm">
                <span class="fas fa-plus me-1"></span>Nouveau règlement
            </a>
        @endif
    </div>
    @if ($emission->reglements->isEmpty())
        <div class="card-body text-center py-4 text-500 fs-9">
            <span class="fas fa-hand-holding-usd fa-2x mb-2 d-block opacity-40"></span>
            Aucun règlement enregistré
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>N° Règlement</th>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Type</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Imputé</th>
                            <th>N° Quittance</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emission->reglements as $r)
                            <tr>
                                <td class="fw-semi-bold">
                                    <a href="{{ route('recouvrements.show', $r) }}">
                                        {{ $r->numero_reglement }}
                                    </a>
                                </td>
                                <td>{{ $r->date_reglement?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $r->modeReglement?->libelle ?? '—' }}</td>
                                <td>{{ $r->typeReglement?->libelle ?? '—' }}</td>
                                <td class="text-end">{{ $fcfa($r->montant) }}</td>
                                <td class="text-end text-success fw-semi-bold">{{ $fcfa($r->montant_impute) }}</td>
                                <td>{{ $r->numero_quittance ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('recouvrements.show', $r) }}"
                                           class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <span class="fas fa-eye"></span>
                                        </a>
                                        <form method="POST" action="{{ route('recouvrements.destroy', $r) }}"
                                              onsubmit="return confirm('Supprimer ce règlement ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger py-0 px-2">
                                                <span class="fas fa-trash"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end fs-9">Totaux</td>
                            <td class="text-end">{{ $fcfa($emission->reglements->sum('montant')) }}</td>
                            <td class="text-end text-success">{{ $fcfa($totalRegle) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
    <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
        <span class="fas fa-hand-holding-usd me-1"></span>{{ $emission->reglements->count() }} règlement(s)
    </div>
</div>

{{-- Documents --}}
<x-documents.panneau :model="$emission" numero="03" />

</x-app-layout>
