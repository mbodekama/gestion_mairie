<x-app-layout :title="'Règlement — ' . $recouvrement->numero_reglement">

@php
    $emission   = $recouvrement->emissionTaxe;
    $contrib    = $emission?->etablissement?->contribuable;
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
             style="background: linear-gradient(135deg, #198754 0%, #0c4b2e 100%); min-height: 130px;">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18); font-size: 1.5rem;">
                <span class="fas fa-hand-holding-usd"></span>
            </div>

            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $recouvrement->numero_reglement }}</h4>
                <p class="mb-1 text-white-50 fs-9">{{ $nomContrib }}</p>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    <span class="badge bg-white text-dark fs-9">
                        <span class="fas fa-calendar me-1"></span>{{ $recouvrement->date_reglement?->format('d/m/Y') ?? '—' }}
                    </span>
                    <span class="badge bg-light text-dark border fs-9">
                        {{ $recouvrement->modeReglement?->libelle ?? '—' }}
                    </span>
                    <span class="badge bg-success fs-9">
                        {{ $fcfa($recouvrement->montant_impute) }} imputés
                    </span>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @unless ($recouvrement->estAnnule())
                    @if ($recouvrement->numero_quittance)
                        <a href="{{ route('recouvrements.quittance', $recouvrement) }}"
                           class="btn btn-warning btn-sm" target="_blank">
                            <span class="fas fa-file-pdf me-1"></span>Quittance PDF
                        </a>
                    @endif
                    @can('RECOUVR_ANNULER')
                    <button type="button" class="btn btn-danger btn-sm"
                            data-bs-toggle="modal" data-bs-target="#modalAnnulation">
                        <span class="fas fa-ban me-1"></span>Annuler
                    </button>
                    @endcan
                @endunless
                @if ($emission)
                    <a href="{{ route('emissions.show', $emission) }}"
                       class="btn btn-outline-light btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Émission
                    </a>
                @else
                    <a href="{{ route('recouvrements.index') }}"
                       class="btn btn-outline-light btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Retour
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>



@if ($recouvrement->estAnnule())
    <div class="alert alert-danger py-3 fs-9">
        <h6 class="alert-heading mb-1"><span class="fas fa-ban me-1"></span>Règlement annulé</h6>
        <div><strong>Motif :</strong> {{ $recouvrement->motif_annulation }}</div>
        <div class="text-muted">
            Annulé le {{ $recouvrement->annule_le?->format('d/m/Y à H:i') }}
            @if ($recouvrement->annulePar)— par {{ $recouvrement->annulePar->name }}@endif
        </div>
    </div>
@endif

<div class="row g-3">
    {{-- Détails règlement --}}
    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-info-circle me-2 text-primary"></span>Détails du règlement
                </h5>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">N° Règlement</dt>
                    <dd class="col-7 fw-semi-bold">{{ $recouvrement->numero_reglement }}</dd>

                    <dt class="col-5 text-600">Date de règlement</dt>
                    <dd class="col-7">{{ $recouvrement->date_reglement?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-5 text-600">Exercice</dt>
                    <dd class="col-7">{{ $recouvrement->exerciceFiscal?->annee ?? '—' }}</dd>

                    <dt class="col-5 text-600">Recette</dt>
                    <dd class="col-7">{{ $recouvrement->recette?->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">Montant versé</dt>
                    <dd class="col-7 fw-semi-bold">{{ $fcfa($recouvrement->montant) }}</dd>

                    <dt class="col-5 text-600">Montant imputé</dt>
                    <dd class="col-7 fw-bold text-success">{{ $fcfa($recouvrement->montant_impute) }}</dd>

                    @if ($recouvrement->mois_impute)
                        <dt class="col-5 text-600">Mois imputé</dt>
                        <dd class="col-7">{{ $recouvrement->mois_impute }}</dd>
                    @endif

                    <dt class="col-5 text-600">Mode de règlement</dt>
                    <dd class="col-7">{{ $recouvrement->modeReglement?->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">Type de règlement</dt>
                    <dd class="col-7">{{ $recouvrement->typeReglement?->libelle ?? '—' }}</dd>

                    @if ($recouvrement->banque)
                        <dt class="col-5 text-600">Banque</dt>
                        <dd class="col-7">{{ $recouvrement->banque->libelle }}</dd>
                    @endif

                    @if ($recouvrement->numero_cheque)
                        <dt class="col-5 text-600">N° Chèque</dt>
                        <dd class="col-7">{{ $recouvrement->numero_cheque }}</dd>
                    @endif

                    @if ($recouvrement->numero_quittance)
                        <dt class="col-5 text-600">N° Quittance</dt>
                        <dd class="col-7 fw-semi-bold">{{ $recouvrement->numero_quittance }}</dd>
                    @endif

                    <dt class="col-5 text-600">Enregistré le</dt>
                    <dd class="col-7">{{ $recouvrement->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $recouvrement->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Émission liée --}}
    <div class="col-lg-6">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-file-invoice-dollar me-2 text-primary"></span>Émission liée
                </h5>
            </div>
            <div class="card-body fs-9">
                @if ($emission)
                    <dl class="row mb-0">
                        <dt class="col-5 text-600">N° Émission</dt>
                        <dd class="col-7">
                            <a href="{{ route('emissions.show', $emission) }}">{{ $emission->numero_emission }}</a>
                        </dd>

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
                                </a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-5 text-600">Nature taxe</dt>
                        <dd class="col-7">{{ $emission->natureTaxe?->libelle_court ?? '—' }}</dd>

                        <dt class="col-5 text-600">Exercice</dt>
                        <dd class="col-7">{{ $emission->exerciceFiscal?->annee ?? '—' }}</dd>

                        @php
                            $montantBase = $emission->montant_prorata > 0 ? $emission->montant_prorata : $emission->montant_annuel;
                            $soldeDu = $emission->soldeDu();
                        @endphp
                        <dt class="col-5 text-600">Montant émis</dt>
                        <dd class="col-7 fw-semi-bold">{{ $fcfa($montantBase) }}</dd>

                        <dt class="col-5 text-600">Solde restant dû</dt>
                        <dd class="col-7 {{ (float) $soldeDu > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                            {{ $fcfa($soldeDu) }}
                        </dd>
                    </dl>

                    <div class="mt-3">
                        <a href="{{ route('emissions.show', $emission) }}"
                           class="btn btn-outline-primary btn-sm">
                            <span class="fas fa-eye me-1"></span>Voir l'émission
                        </a>
                    </div>
                @else
                    <p class="text-500 fs-9 mb-0">Aucune émission de taxe liée.</p>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-coins me-1"></span>Montant imputé : {{ $fcfa($recouvrement->montant_impute) }}
            </div>
        </div>
    </div>
</div>

{{-- Pièces jointes & Historique --}}
<x-documents.panneau :model="$recouvrement" :editable="true" numero="03" />

<x-historique.timeline
    :historiques="$historiques"
    :labels="$recouvrement->auditLabels ?? []"
    :creeAt="$recouvrement->created_at"
/>

@unless ($recouvrement->estAnnule())
    @can('RECOUVR_ANNULER')
    {{-- Modal d'annulation --}}
    <div class="modal fade" id="modalAnnulation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('recouvrements.annuler', $recouvrement) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="fas fa-ban text-danger me-1"></span>Annuler le règlement {{ $recouvrement->numero_reglement }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-9 text-muted">
                        L'annulation est définitive et tracée. Le montant ne sera plus déduit du solde de l'émission.
                    </p>
                    <label class="form-label fs-9">Motif de l'annulation <span class="text-danger">*</span></label>
                    <textarea name="motif_annulation" rows="3" maxlength="255" required
                              class="form-control @error('motif_annulation') is-invalid @enderror"
                              placeholder="Ex : erreur de saisie, double encaissement…">{{ old('motif_annulation') }}</textarea>
                    @error('motif_annulation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="fas fa-ban me-1"></span>Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>

    @error('motif_annulation')
        <script>document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal(document.getElementById('modalAnnulation')).show());</script>
    @enderror
    @endcan
@endunless

</x-app-layout>
