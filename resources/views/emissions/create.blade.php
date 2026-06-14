<x-app-layout title="Nouvelle émission de taxe">

<x-page-header titre="Créer une émission de taxe" />

@if (session('error'))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('emissions.store') }}" novalidate>
    @csrf

    {{-- Rattachement --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-link me-2 text-primary"></span>Rattachement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Établissement --}}
                <div class="col-md-6">
                    <label class="form-label fs-9">Établissement <span class="text-danger">*</span></label>
                    @if ($etablissement)
                        <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                        @php
                            $contrib = $etablissement->contribuable;
                            $nomContrib = $contrib
                                ? ($contrib->type_personne === 'PP'
                                    ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                                    : ($contrib->raison_sociale ?? ''))
                                : '';
                        @endphp
                        <input type="text" class="form-control form-control-lg bg-light"
                               value="{{ $etablissement->numero }} — {{ $etablissement->denomination ?? $nomContrib }}"
                               readonly>
                    @else
                        <input type="number" name="etablissement_id"
                               value="{{ old('etablissement_id') }}"
                               class="form-control form-control-lg @error('etablissement_id') is-invalid @enderror"
                               placeholder="ID de l'établissement" required>
                        @error('etablissement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Exercice fiscal --}}
                <div class="col-md-3">
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
            </div>
        </div>
    </div>

    {{-- Nature et montants --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-coins me-2 text-primary"></span>Nature et montants
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Nature taxe --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Nature de taxe <span class="text-danger">*</span></label>
                    <select name="nature_taxe_id"
                            class="form-select form-select-lg @error('nature_taxe_id') is-invalid @enderror"
                            required>
                        <option value="">— Choisir —</option>
                        @foreach ($naturesTaxe as $nt)
                            <option value="{{ $nt->id }}"
                                    {{ old('nature_taxe_id') == $nt->id ? 'selected' : '' }}>
                                {{ $nt->libelle_court }}
                            </option>
                        @endforeach
                    </select>
                    @error('nature_taxe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Périodicité --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Périodicité <span class="text-danger">*</span></label>
                    <select name="periodicite_id"
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

                {{-- CA annuel --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">CA annuel (FCFA)</label>
                    <input type="number" name="ca_annuel"
                           value="{{ old('ca_annuel', $etablissement?->ca_reference !== null ? (int) $etablissement->ca_reference : '') }}"
                           class="form-control form-control-lg @error('ca_annuel') is-invalid @enderror"
                           min="0" step="1" placeholder="0">
                    @if ($etablissement?->ca_reference !== null)
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
                    <input type="date" name="date_declaration" value="{{ old('date_declaration') }}"
                           class="form-control form-control-lg">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Date de liquidation</label>
                    <input type="date" name="date_liquidation" value="{{ old('date_liquidation') }}"
                           class="form-control form-control-lg">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $etablissement ? route('etablissements.show', $etablissement) : route('emissions.index') }}"
           class="btn btn-outline-secondary btn-lg">
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

</x-app-layout>
