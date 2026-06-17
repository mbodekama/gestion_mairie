<x-app-layout :title="'Redressement — ' . $redressement->numero">

@php
    $controle = $redressement->controleFiscal;
    $etab     = $controle?->etablissement;
    $contrib  = $etab?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
    $couleur = ['ouvert'=>'warning','notifie'=>'info','solde'=>'success','annule'=>'secondary'][$redressement->etat] ?? 'secondary';
@endphp

{{-- Bandeau --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #dc3545 0%, #7c1520 100%); min-height: 120px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:64px; height:64px; background: rgba(255,255,255,.18); font-size: 1.3rem;">
                <span class="fas fa-gavel"></span>
            </div>
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $redressement->numero }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $nomContrib }}
                    @if ($controle)
                        — Contrôle <a href="{{ route('controles.show', $controle) }}" class="text-white">{{ $controle->numero }}</a>
                    @endif
                </p>
                <span class="badge bg-{{ $couleur }} text-{{ $couleur === 'warning' ? 'dark' : 'white' }}">{{ ucfirst($redressement->etat) }}</span>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                <a href="{{ route('redressements.avis', $redressement) }}" class="btn btn-light btn-sm" target="_blank">
                    <span class="fas fa-file-pdf me-1"></span>Avis
                </a>
                <a href="{{ route('redressements.index') }}" class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger fs-9"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">01</span><span class="fas fa-info-circle me-2 text-primary"></span>Détails</h5></div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-3 text-600">Contribuable</dt>
                    <dd class="col-9">@if ($contrib)<a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>@else — @endif</dd>
                    <dt class="col-3 text-600">Établissement</dt>
                    <dd class="col-9">@if ($etab)<a href="{{ route('etablissements.show', $etab) }}">{{ $etab->numero }}</a>@else — @endif</dd>
                    <dt class="col-3 text-600">Contrôle d'origine</dt>
                    <dd class="col-9">@if ($controle)<a href="{{ route('controles.show', $controle) }}">{{ $controle->numero }}</a>@else — @endif</dd>
                    <dt class="col-3 text-600">Date</dt>
                    <dd class="col-9">{{ $redressement->date_redressement?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-3 text-600">Observation</dt>
                    <dd class="col-9">{{ $redressement->observation ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $redressement->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">02</span><span class="fas fa-coins me-2 text-primary"></span>Montants</h5></div>
            <div class="card-body fs-9">
                <dl class="row mb-3">
                    <dt class="col-7 text-600">Droits</dt><dd class="col-5 text-end">{{ $fcfa($redressement->montant_droits) }}</dd>
                    <dt class="col-7 text-600">Pénalités</dt><dd class="col-5 text-end">{{ $fcfa($redressement->montant_penalites) }}</dd>
                </dl>
                <div class="p-3 rounded text-center" style="background: rgba(220,53,69,.08); border:1px solid rgba(220,53,69,.2);">
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold mb-1">Total redressé</div>
                    <div class="fs-5 fw-bold text-danger">{{ $fcfa($redressement->montant_total) }}</div>
                </div>
                @can('REDRESS_GERER')
                    <form method="POST" action="{{ route('redressements.etat', $redressement) }}" class="mt-3 d-flex gap-2">
                        @csrf @method('PATCH')
                        <select name="etat" class="form-select form-select-sm">
                            @foreach (['ouvert'=>'Ouvert','notifie'=>'Notifié','solde'=>'Soldé','annule'=>'Annulé'] as $val => $lib)
                                <option value="{{ $val }}" {{ $redressement->etat === $val ? 'selected' : '' }}>{{ $lib }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Mettre à jour</button>
                    </form>
                @endcan
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center py-2 fs-9 text-600">
                <span><span class="fas fa-flag me-1"></span>État : {{ ucfirst($redressement->etat) }}</span>
                <span><span class="fas fa-clock me-1"></span>Mis à jour le {{ $redressement->updated_at?->format('d/m/Y') ?? '—' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ===== Émissions complémentaires existantes ===== --}}
<div class="card mb-3 card-section">
    <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center"><span class="num-section">03</span><span class="fas fa-file-invoice-dollar me-2 text-primary"></span>Déclarations complémentaires
            <span class="badge bg-secondary ms-2">{{ $redressement->emissionsTaxe->count() }}</span>
        </h5>
        <small class="text-muted fs-10">Émissions générées automatiquement depuis les constats du contrôle, recouvrables comme toute émission.</small>
    </div>
    @php $modifiable = auth()->user()?->can('REDRESS_GERER') && ! in_array($redressement->etat, ['solde','annule'], true); @endphp
    <div class="card-body p-0">
        @if ($redressement->emissionsTaxe->isEmpty())
            <p class="text-center py-4 text-muted mb-0">Aucune déclaration générée pour ce redressement.</p>
        @else
            <form method="POST" action="{{ route('redressements.penalites', $redressement) }}">
                @csrf @method('PATCH')
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 fs-9 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>N° Émission</th><th>Nature</th><th>Exercice</th>
                                <th class="text-end">Droits</th>
                                <th class="text-end" style="min-width:140px;">Pénalité</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($redressement->emissionsTaxe as $em)
                                @php $droits = bcsub((string) $em->montant_annuel, (string) $em->penalite, 2); @endphp
                                <tr>
                                    <td class="fw-semi-bold">{{ $em->numero_emission }}</td>
                                    <td>{{ $em->natureTaxe?->libelle_court ?? $em->natureTaxe?->libelle ?? '—' }}</td>
                                    <td>{{ $em->exerciceFiscal?->annee ?? '—' }}</td>
                                    <td class="text-end">{{ $fcfa($droits) }}</td>
                                    <td class="text-end">
                                        @if ($modifiable)
                                            <input type="number" name="penalite[{{ $em->id }}]" min="0" step="1"
                                                   value="{{ (int) $em->penalite }}" class="form-control form-control-sm text-end">
                                        @else
                                            {{ $fcfa($em->penalite) }}
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">{{ $fcfa($em->montant_annuel) }}</td>
                                    <td><a href="{{ route('emissions.show', $em) }}" class="btn btn-sm btn-outline-info" title="Voir l'émission"><span class="fas fa-eye"></span></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($modifiable)
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <span class="fas fa-save me-1"></span>Enregistrer les pénalités
                        </button>
                    </div>
                @endif
            </form>
        @endif
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
        <span class="fas fa-file-invoice-dollar me-1"></span>{{ $redressement->emissionsTaxe->count() }} déclaration(s) complémentaire(s)
    </div>
</div>

{{-- ===== Générer des émissions complémentaires ===== --}}
@can('REDRESS_GERER')
@if (! in_array($redressement->etat, ['solde','annule'], true))
<div class="card mb-4 border-primary">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
        <h5 class="mb-0"><span class="fas fa-plus-circle me-2 text-primary"></span>Ajouter une déclaration complémentaire</h5>
        <button type="button" id="btn-ajouter" class="btn btn-outline-primary btn-sm"><span class="fas fa-plus me-1"></span>Ajouter une ligne</button>
    </div>
    <form method="POST" action="{{ route('redressements.emissions', $redressement) }}" id="form-emissions">
        @csrf
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:160px;">Nature de taxe</th>
                            <th style="min-width:120px;">Exercice</th>
                            <th style="min-width:140px;">Périodicité</th>
                            <th class="text-end" style="min-width:130px;">Droits</th>
                            <th class="text-end" style="min-width:130px;">Pénalité</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="lignes-emissions"></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-success"><span class="fas fa-save me-1"></span>Générer les émissions</button>
        </div>
    </form>
</div>

<template id="tpl-ligne">
    <tr>
        <td>
            <select name="lignes[__i__][nature_taxe_id]" class="form-select form-select-sm" required>
                <option value="">—</option>
                @foreach ($naturesTaxe as $nt)<option value="{{ $nt->id }}">{{ $nt->libelle_court ?? $nt->libelle }}</option>@endforeach
            </select>
        </td>
        <td>
            <select name="lignes[__i__][exercice_fiscal_id]" class="form-select form-select-sm" required>
                <option value="">—</option>
                @foreach ($exercices as $ex)<option value="{{ $ex->id }}">{{ $ex->annee }}</option>@endforeach
            </select>
        </td>
        <td>
            <select name="lignes[__i__][periodicite_id]" class="form-select form-select-sm" required>
                <option value="">—</option>
                @foreach ($periodicites as $p)<option value="{{ $p->id }}">{{ $p->libelle }}</option>@endforeach
            </select>
        </td>
        <td><input type="number" name="lignes[__i__][montant]" min="1" step="1" class="form-control form-control-sm text-end" placeholder="Droits" required></td>
        <td><input type="number" name="lignes[__i__][penalite]" min="0" step="1" value="0" class="form-control form-control-sm text-end"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-suppr"><span class="fas fa-trash"></span></button></td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    const tbody = document.getElementById('lignes-emissions');
    const tpl   = document.getElementById('tpl-ligne').innerHTML;
    let index = 0;
    function ajouter() {
        const tr = document.createElement('tr');
        tr.innerHTML = tpl.replace(/__i__/g, index).replace(/^\s*<tr>|<\/tr>\s*$/g, '');
        tr.querySelector('.btn-suppr').addEventListener('click', () => tr.remove());
        tbody.appendChild(tr);
        index++;
    }
    document.getElementById('btn-ajouter').addEventListener('click', ajouter);
    ajouter();
}());
</script>
@endpush
@endif
@endcan

</x-app-layout>
