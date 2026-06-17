<x-app-layout :title="'Convocation — ' . $convocation->numero">

@php
    $contrib = $convocation->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

{{-- Bandeau --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #fd7e14 0%, #b35309 100%); min-height: 120px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:64px; height:64px; background: rgba(255,255,255,.18); font-size: 1.3rem;">
                <span class="fas fa-envelope-open-text"></span>
            </div>
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $convocation->numero }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $nomContrib }}
                    @if ($convocation->etablissement)
                        — Étab. <a href="{{ route('etablissements.show', $convocation->etablissement) }}" class="text-white">{{ $convocation->etablissement->numero }}</a>
                    @endif
                </p>
                @if ($convocation->controle)
                    <span class="badge bg-light text-dark">Issue du contrôle {{ $convocation->controle->numero }}</span>
                @else
                    <span class="badge bg-light text-dark">Convocation / mise en demeure</span>
                @endif
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @unless ($convocation->controle_id)
                    <a href="{{ route('convocations.edit', $convocation) }}" class="btn btn-light btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                @endunless
                <a href="{{ route('convocations.index') }}" class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">01</span><span class="fas fa-info-circle me-2 text-primary"></span>Détails</h5></div>
            <div class="card-body fs-9">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-600">N° Convocation</dt><dd class="col-7 fw-semi-bold">{{ $convocation->numero }}</dd>
                            <dt class="col-5 text-600">Année</dt><dd class="col-7">{{ $convocation->annee }}</dd>
                            <dt class="col-5 text-600">Contribuable</dt>
                            <dd class="col-7">@if ($contrib)<a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>@else — @endif</dd>
                            <dt class="col-5 text-600">Motif</dt><dd class="col-7">{{ $convocation->motif ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-6 text-600">Service</dt><dd class="col-6">{{ $convocation->service?->libelle ?? '—' }}</dd>
                            <dt class="col-6 text-600">Agent chargé</dt>
                            <dd class="col-6">{{ trim(($convocation->agent?->nom ?? '') . ' ' . ($convocation->agent?->prenoms ?? '')) ?: '—' }}</dd>
                            <dt class="col-6 text-600">Date convocation</dt><dd class="col-6">{{ $convocation->date_convocation?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Délai réponse</dt><dd class="col-6">{{ $convocation->delai_reponse ? $convocation->delai_reponse . ' jours' : '—' }}</dd>
                            <dt class="col-6 text-600">Date limite</dt>
                            <dd class="col-6 {{ $convocation->date_limite?->isPast() && !$convocation->date_reponse ? 'text-danger fw-bold' : '' }}">{{ $convocation->date_limite?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Date réponse</dt><dd class="col-6">{{ $convocation->date_reponse?->format('d/m/Y') ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $convocation->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">02</span><span class="fas fa-coins me-2 text-primary"></span>Rappel fiscal</h5></div>
            <div class="card-body fs-9">
                <dl class="row mb-3">
                    <dt class="col-6 text-600">Période</dt>
                    <dd class="col-6">{{ $convocation->periode_due_debut?->format('d/m/Y') ?? '—' }} → {{ $convocation->periode_due_fin?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-6 text-600">Nb mois dus</dt><dd class="col-6">{{ $convocation->nb_mois_du ?? '—' }}</dd>
                    <dt class="col-6 text-600">Nb jours dus</dt><dd class="col-6">{{ $convocation->nb_jours_du ?? '—' }}</dd>
                </dl>
                <div class="p-3 rounded text-center" style="background: rgba(253,126,20,.08); border:1px solid rgba(253,126,20,.2);">
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold mb-1">Montant dû</div>
                    <div class="fs-5 fw-bold text-warning">{{ $fcfa($convocation->montant_du) }}</div>
                </div>
                @unless ($convocation->controle_id)
                    <form method="POST" action="{{ route('convocations.destroy', $convocation) }}" class="d-grid mt-3"
                          onsubmit="return confirm('Supprimer la convocation {{ $convocation->numero }} ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <span class="fas fa-trash me-1"></span>Supprimer
                        </button>
                    </form>
                @endunless
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $convocation->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>
</div>

{{-- Documents --}}
<x-documents.panneau :model="$convocation" numero="03" />

</x-app-layout>
