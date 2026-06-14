<x-app-layout :title="'Exercice fiscal — ' . $exerciceFiscal->annee">

@php
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

{{-- =====================================================================
     BANDEAU
     ===================================================================== --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #6f42c1 0%, #3d0f7c 100%); min-height: 130px;">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18); font-size: 1.4rem; font-weight: 700;">
                {{ $exerciceFiscal->annee }}
            </div>

            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">Exercice fiscal {{ $exerciceFiscal->annee }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $exerciceFiscal->date_debut?->format('d/m/Y') }} →
                    {{ $exerciceFiscal->date_fin?->format('d/m/Y') }}
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    @if ($exerciceFiscal->cloture)
                        <span class="badge bg-danger">Clôturé</span>
                    @else
                        <span class="badge bg-success">Ouvert</span>
                    @endif
                    @if ($exerciceFiscal->collectivite)
                        <span class="badge bg-light text-dark border">
                            <span class="fas fa-building me-1"></span>{{ $exerciceFiscal->collectivite->libelle }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @if (!$exerciceFiscal->cloture)
                    <a href="{{ route('exercices-fiscaux.edit', $exerciceFiscal) }}"
                       class="btn btn-light btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    <form method="POST" action="{{ route('exercices-fiscaux.cloturer', $exerciceFiscal) }}"
                          onsubmit="return confirm('Clôturer l\'exercice {{ $exerciceFiscal->annee }} ? Cette action est irréversible.')">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">
                            <span class="fas fa-lock me-1"></span>Clôturer
                        </button>
                    </form>
                @endif
                <a href="{{ route('exercices-fiscaux.index') }}"
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
@if (session('error'))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =====================================================================
     KPIs
     ===================================================================== --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-primary flex-shrink-0"
                     style="width:48px;height:48px;">
                    <span class="fas fa-file-invoice text-primary fs-7"></span>
                </div>
                <div>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold">Émissions</div>
                    <div class="fs-5 fw-bold text-900">{{ $nbEmissions }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
    <div class="col-sm-6 col-md-4">
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
</div>

{{-- =====================================================================
     ÉMISSIONS DU EXERCICE
     ===================================================================== --}}
<div class="card mb-3">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <span class="fas fa-file-invoice-dollar me-2 text-primary"></span>
            Émissions de taxe
            <span class="badge bg-secondary ms-2">{{ $nbEmissions }}</span>
        </h5>
        @if (!$exerciceFiscal->cloture)
            <a href="{{ route('emissions.create', ['exercice_fiscal_id' => $exerciceFiscal->id]) }}"
               class="btn btn-primary btn-sm">
                <span class="fas fa-plus me-1"></span>Nouvelle émission
            </a>
        @endif
    </div>
    @if ($emissions->isEmpty())
        <div class="card-body text-center py-4 text-500 fs-9">
            <span class="fas fa-file-invoice fa-2x mb-2 d-block opacity-40"></span>
            Aucune émission pour cet exercice
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>N° Émission</th>
                            <th>Contribuable</th>
                            <th>Établissement</th>
                            <th>Nature taxe</th>
                            <th>Périodicité</th>
                            <th class="text-end">Montant émis</th>
                            <th class="text-end">Solde dû</th>
                            <th>Date liquidation</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emissions as $em)
                            @php
                                $contrib = $em->etablissement?->contribuable;
                                $nomContrib = $contrib
                                    ? ($contrib->type_personne === 'PP'
                                        ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                        : ($contrib->raison_sociale ?? ''))
                                    : '—';
                                $montantBase = $em->montant_prorata > 0 ? $em->montant_prorata : $em->montant_annuel;
                                $soldeDu     = $em->soldeDu();
                            @endphp
                            <tr>
                                <td class="fw-semi-bold">
                                    <a href="{{ route('emissions.show', $em) }}">{{ $em->numero_emission }}</a>
                                </td>
                                <td class="text-truncate" style="max-width:160px;">{{ $nomContrib }}</td>
                                <td>{{ $em->etablissement?->numero ?? '—' }}</td>
                                <td>{{ $em->natureTaxe?->libelle_court ?? '—' }}</td>
                                <td>{{ $em->periodicite?->libelle_court ?? $em->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-end">{{ $fcfa($montantBase) }}</td>
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
                </table>
            </div>
        </div>
        @if ($emissions->hasPages())
            <div class="card-footer py-2">
                {{ $emissions->links() }}
            </div>
        @endif
    @endif
</div>

</x-app-layout>
