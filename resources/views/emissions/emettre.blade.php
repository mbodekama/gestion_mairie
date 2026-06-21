<x-app-layout title="Nouvelle émission de taxe">

@php
    $contrib = $etablissement->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? '—'))
        : '—';
@endphp

<x-page-header titre="Créer une émission de taxe"
               sous-titre="Étape 2 — Renseigner l'émission" />


{{-- ===== Card Établissement / contribuable ===== --}}
<div class="card mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <span class="fas fa-store me-2 text-primary"></span>
            {{ $etablissement->numero }}
            @if ($etablissement->denomination)
                — {{ $etablissement->denomination }}
            @endif
        </h5>
        @can('EMISSION_CREER')
        <a href="{{ route('emissions.create') }}" class="btn btn-outline-secondary btn-sm">
            <span class="fas fa-redo me-1"></span>Changer d'établissement
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="row g-3 fs-9">
            <div class="col-md-4">
                <div class="text-muted">Contribuable</div>
                <div class="fw-bold">{{ $nomContrib }}
                    <span class="badge bg-secondary ms-1">{{ $contrib->type_personne ?? '' }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">N° Identifiant</div>
                <div class="fw-bold">{{ $contrib->numero_identifiant ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">CA de référence</div>
                <div class="fw-bold">
                    {{ $etablissement->ca_reference !== null ? number_format((float) $etablissement->ca_reference, 0, ',', ' ') . ' FCFA' : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>

@if ($obligations->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <span class="fas fa-exclamation-triangle fa-2x d-block mb-2 text-warning"></span>
            <p class="mb-3 text-muted">
                Ce contribuable n'est assujetti à aucune obligation fiscale.
                Assignez-lui d'abord une nature de taxe.
            </p>
            @can('PILOTAGE_GERER')
            <a href="{{ route('pilotage.obligations.create', ['code' => $contrib->numero_identifiant]) }}"
               class="btn btn-primary">
                <span class="fas fa-tasks me-1"></span>Gérer les obligations
            </a>
            @endcan
        </div>
    </div>
@else
<form method="POST" action="{{ route('emissions.store') }}" novalidate>
    @csrf
    <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">

    {{-- Exercice et nature --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-link me-2 text-primary"></span>Exercice et nature
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Exercice fiscal (obligatoire) --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Exercice fiscal <span class="text-danger">*</span></label>
                    <select name="exercice_fiscal_id"
                            class="form-select form-select-lg @error('exercice_fiscal_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($exercices as $ex)
                            <option value="{{ $ex->id }}"
                                    {{ old('exercice_fiscal_id', $exerciceDefaut?->id) == $ex->id ? 'selected' : '' }}>
                                {{ $ex->annee }}
                            </option>
                        @endforeach
                    </select>
                    @error('exercice_fiscal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Nature de taxe (limitée aux obligations du contribuable) --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id" id="nature-taxe"
                            class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($obligations as $obl)
                            <option value="{{ $obl->nature_taxe_id }}"
                                    data-periodicite="{{ $obl->periodicite_id }}"
                                    {{ old('nature_taxe_id') == $obl->nature_taxe_id ? 'selected' : '' }}>
                                {{ $obl->natureTaxe?->libelle_court ?? $obl->natureTaxe?->libelle ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('nature_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Seules les obligations du contribuable sont proposées.</small>
                </div>

                {{-- Périodicité (pré-remplie depuis l'obligation) --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Périodicité <span class="text-danger">*</span></label>
                    <select name="periodicite_id" id="periodicite"
                            class="form-select form-select-lg @error('periodicite_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($periodicites as $p)
                            <option value="{{ $p->id }}"
                                    {{ old('periodicite_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodicite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Montants --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-coins me-2 text-primary"></span>Montants
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- CA annuel --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">CA annuel (FCFA)</label>
                    <input type="number" name="ca_annuel"
                           value="{{ old('ca_annuel', $etablissement->ca_reference !== null ? (int) $etablissement->ca_reference : '') }}"
                           class="form-control form-control-lg @error('ca_annuel') is-invalid @enderror"
                           min="0" step="1" placeholder="0">
                    @if ($etablissement->ca_reference !== null)
                        <small class="text-muted">Pré-rempli depuis le CA de référence de l'établissement.</small>
                    @endif
                    @error('ca_annuel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Montant annuel --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant annuel (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant_annuel" value="{{ old('montant_annuel', '0') }}"
                           class="form-control form-control-lg @error('montant_annuel') is-invalid @enderror"
                           min="0" step="1" required>
                    @error('montant_annuel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Montant période --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant par période (FCFA)</label>
                    <input type="number" name="montant_periode" value="{{ old('montant_periode') }}"
                           class="form-control form-control-lg"
                           min="0" step="1" placeholder="Calculé automatiquement">
                </div>

                {{-- Prorata --}}
                <div class="col-md-2">
                    <label class="form-label fs-9">Nb mois prorata</label>
                    <input type="number" name="nb_mois_prorata" value="{{ old('nb_mois_prorata') }}"
                           class="form-control form-control-lg"
                           min="1" max="12" placeholder="—">
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-9">Montant prorata (FCFA)</label>
                    <input type="number" name="montant_prorata" value="{{ old('montant_prorata') }}"
                           class="form-control form-control-lg"
                           min="0" step="1" placeholder="—">
                </div>

                {{-- Calcul automatique depuis le barème --}}
                <div class="col-12 d-flex align-items-center gap-2">
                    <button type="button" id="btn-liquider" class="btn btn-outline-primary"
                            data-url="{{ route('emissions.liquider') }}">
                        <span class="fas fa-calculator me-1"></span>Calculer depuis le barème
                    </button>
                    <small id="liquider-info" class="text-muted"></small>
                </div>
            </div>
        </div>
    </div>

    {{-- Dates --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-calendar me-2 text-primary"></span>Dates
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de déclaration</label>
                    <input type="date" name="date_declaration" value="{{ old('date_declaration', now()->toDateString()) }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de liquidation</label>
                    <input type="date" name="date_liquidation" value="{{ old('date_liquidation', now()->toDateString()) }}"
                           class="form-control form-control-lg">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('etablissements.show', $etablissement) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'émission
        </button>
    </div>

</form>

@push('scripts')
<script>
(function () {
    // ── Pré-remplissage de la périodicité depuis l'obligation choisie ──
    const nature      = document.getElementById('nature-taxe');
    const periodicite = document.getElementById('periodicite');

    function appliquerPeriodicite() {
        const pid = nature.options[nature.selectedIndex]?.dataset.periodicite || '';
        if (pid) periodicite.value = pid;
    }
    nature?.addEventListener('change', appliquerPeriodicite);

    // ── Calcul depuis le barème ──
    const btn = document.getElementById('btn-liquider');
    if (!btn) return;
    const form  = btn.closest('form');
    const info  = document.getElementById('liquider-info');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const val = (name) => { const el = form.querySelector(`[name="${name}"]`); return el ? el.value : ''; };
    const set = (name, value) => { const el = form.querySelector(`[name="${name}"]`); if (el) el.value = value; };

    btn.addEventListener('click', async function () {
        if (!val('nature_taxe_id') || !val('periodicite_id')) {
            info.className = 'text-danger';
            info.textContent = 'Choisissez d\'abord la nature de taxe et la périodicité.';
            return;
        }
        btn.disabled = true;
        info.className = 'text-muted';
        info.textContent = 'Calcul…';
        try {
            const resp = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    etablissement_id: val('etablissement_id') || null,
                    nature_taxe_id:   val('nature_taxe_id'),
                    periodicite_id:   val('periodicite_id'),
                    ca_annuel:        val('ca_annuel') || 0,
                    nb_mois_prorata:  val('nb_mois_prorata') || null,
                }),
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();

            if (data.bareme_id === null) {
                info.className = 'text-danger';
                info.textContent = 'Aucun barème applicable trouvé pour ces critères.';
                return;
            }
            set('montant_annuel',  data.montant_annuel);
            set('montant_periode', data.montant_periode);
            set('montant_prorata', data.montant_prorata);
            info.className = 'text-success';
            info.textContent = `Barème appliqué (taux ${data.taux} %). Montants pré-remplis, ajustables avant enregistrement.`;
        } catch (e) {
            info.className = 'text-danger';
            info.textContent = 'Erreur lors du calcul.';
        } finally {
            btn.disabled = false;
        }
    });
}());
</script>
@endpush

@endif

</x-app-layout>
