<x-app-layout :title="'Contrôle fiscal — ' . $controleFiscal->numero">

@php
    $contrib = $controleFiscal->etablissement?->contribuable;
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
             style="background: linear-gradient(135deg, #dc3545 0%, #7c1520 100%); min-height: 130px;">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18); font-size: 1.4rem;">
                <span class="fas fa-search-dollar"></span>
            </div>

            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $controleFiscal->numero }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $nomContrib }}
                    @if ($controleFiscal->etablissement)
                        — Étab.
                        <a href="{{ route('etablissements.show', $controleFiscal->etablissement) }}" class="text-white-50">
                            {{ $controleFiscal->etablissement->numero }}
                        </a>
                    @endif
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    <span class="badge bg-white text-dark fs-9">
                        <span class="fas fa-calendar me-1"></span>{{ $controleFiscal->annee }}
                    </span>
                    @if ($controleFiscal->motif)
                        <span class="badge bg-light text-dark border fs-10 text-truncate" style="max-width:250px;">
                            {{ $controleFiscal->motif }}
                        </span>
                    @endif
                    @if ($controleFiscal->date_reponse)
                        <span class="badge bg-success">Répondu le {{ $controleFiscal->date_reponse->format('d/m/Y') }}</span>
                    @elseif ($controleFiscal->date_limite && $controleFiscal->date_limite->isPast())
                        <span class="badge bg-danger">Date limite dépassée</span>
                    @elseif ($controleFiscal->date_convocation)
                        <span class="badge bg-warning text-dark">En cours</span>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @can('CONTROLE_GERER')
                <a href="{{ route('controle-fiscal.edit', $controleFiscal) }}"
                   class="btn btn-light btn-sm">
                    <span class="fas fa-edit me-1"></span>Modifier
                </a>
                @endcan
                @if ($controleFiscal->etablissement)
                    <a href="{{ route('etablissements.show', $controleFiscal->etablissement) }}"
                       class="btn btn-outline-light btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Étab.
                    </a>
                @endif
                <a href="{{ route('controle-fiscal.index') }}"
                   class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-3">
    {{-- Détails principaux --}}
    <div class="col-lg-8">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-info-circle me-2 text-primary"></span>Détails du contrôle
                </h5>
            </div>
            <div class="card-body fs-9">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-600">N° Contrôle</dt>
                            <dd class="col-7 fw-semi-bold">{{ $controleFiscal->numero }}</dd>

                            <dt class="col-5 text-600">Année</dt>
                            <dd class="col-7">{{ $controleFiscal->annee }}</dd>

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
                                @if ($controleFiscal->etablissement)
                                    <a href="{{ route('etablissements.show', $controleFiscal->etablissement) }}">
                                        {{ $controleFiscal->etablissement->numero }}
                                        @if ($controleFiscal->etablissement->denomination)
                                            — {{ $controleFiscal->etablissement->denomination }}
                                        @endif
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>

                            <dt class="col-5 text-600">Motif</dt>
                            <dd class="col-7">{{ $controleFiscal->motif ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-6 text-600">Service</dt>
                            <dd class="col-6">{{ $controleFiscal->service?->libelle ?? '—' }}</dd>

                            <dt class="col-6 text-600">Agent chargé</dt>
                            <dd class="col-6">
                                {{ ($controleFiscal->agent?->nom ?? '') . ' ' . ($controleFiscal->agent?->prenoms ?? '') }}
                                @if (!$controleFiscal->agent) — @endif
                            </dd>

                            <dt class="col-6 text-600">Date convocation</dt>
                            <dd class="col-6">{{ $controleFiscal->date_convocation?->format('d/m/Y') ?? '—' }}</dd>

                            <dt class="col-6 text-600">Délai réponse</dt>
                            <dd class="col-6">{{ $controleFiscal->delai_reponse ? $controleFiscal->delai_reponse . ' jours' : '—' }}</dd>

                            <dt class="col-6 text-600">Date limite</dt>
                            <dd class="col-6 {{ $controleFiscal->date_limite?->isPast() && !$controleFiscal->date_reponse ? 'text-danger fw-bold' : '' }}">
                                {{ $controleFiscal->date_limite?->format('d/m/Y') ?? '—' }}
                            </dd>

                            <dt class="col-6 text-600">Date réponse</dt>
                            <dd class="col-6 {{ $controleFiscal->date_reponse ? 'text-success' : '' }}">
                                {{ $controleFiscal->date_reponse?->format('d/m/Y') ?? '—' }}
                                @if ($controleFiscal->heure_reponse)
                                    à {{ $controleFiscal->heure_reponse }}
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $controleFiscal->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Période et montants --}}
    <div class="col-lg-4">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-coins me-2 text-primary"></span>Rappel fiscal
                </h5>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-3">
                    <dt class="col-6 text-600">Période début</dt>
                    <dd class="col-6">{{ $controleFiscal->periode_due_debut?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-6 text-600">Période fin</dt>
                    <dd class="col-6">{{ $controleFiscal->periode_due_fin?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-6 text-600">Nb mois dus</dt>
                    <dd class="col-6">{{ $controleFiscal->nb_mois_du ?? '—' }}</dd>

                    <dt class="col-6 text-600">Nb jours dus</dt>
                    <dd class="col-6">{{ $controleFiscal->nb_jours_du ?? '—' }}</dd>
                </dl>

                <div class="p-3 rounded text-center"
                     style="background: rgba(220,53,69,.08); border: 1px solid rgba(220,53,69,.2);">
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold mb-1">Montant dû</div>
                    <div class="fs-5 fw-bold text-danger">{{ $fcfa($controleFiscal->montant_du) }}</div>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <dl class="row mb-0 fs-9">
                        <dt class="col-6 text-600">Enregistré le</dt>
                        <dd class="col-6">{{ $controleFiscal->created_at?->format('d/m/Y') ?? '—' }}</dd>
                    </dl>
                    <div class="d-grid mt-3">
                        @can('CONTROLE_GERER')
                        <form method="POST" action="{{ route('controle-fiscal.destroy', $controleFiscal) }}"
                              onsubmit="return confirm('Supprimer définitivement ce contrôle fiscal ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <span class="fas fa-trash me-1"></span>Supprimer
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $controleFiscal->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>
</div>

{{-- Documents --}}
<x-documents.panneau :model="$controleFiscal" numero="03" />

</x-app-layout>
