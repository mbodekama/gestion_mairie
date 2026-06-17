<x-app-layout title="Encaissement">

@php
    $nomContrib = $contribuable->type_personne === 'PP'
        ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
        : ($contribuable->raison_sociale ?? '—');
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

<x-page-header titre="Encaissement de règlements" sous-titre="Étape 2 — Émissions à régler" />


{{-- ===== Card Contribuable ===== --}}
<div class="card mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <span class="fas fa-user me-2 text-primary"></span>{{ $nomContrib }}
            <span class="badge bg-secondary ms-1">{{ $contribuable->type_personne }}</span>
        </h5>
        <a href="{{ route('recouvrements.create') }}" class="btn btn-outline-secondary btn-sm">
            <span class="fas fa-redo me-1"></span>Changer de redevable
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3 fs-9">
            <div class="col-md-3">
                <div class="text-muted">N° Identifiant</div>
                <div class="fw-bold">{{ $contribuable->numero_identifiant ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">N° Compte</div>
                <div class="fw-bold">{{ $contribuable->numero_compte ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Téléphone</div>
                <div class="fw-bold">{{ $contribuable->telephone ?? $contribuable->cellulaire ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Cible</div>
                <div class="fw-bold">
                    @if ($etablissement)
                        Établissement {{ $etablissement->numero }}
                    @else
                        Tous les établissements
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if ($emissions->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <span class="fas fa-check-circle fa-2x d-block mb-2 text-success"></span>
            Aucune émission à régler : tout est soldé pour ce redevable.
        </div>
    </div>
@else
<form method="POST" action="{{ route('recouvrements.store') }}" id="form-encaissement" novalidate>
    @csrf
    <input type="hidden" name="code" value="{{ $code }}">

    @error('emissions')
        <div class="alert alert-danger py-2 fs-9">{{ $message }}</div>
    @enderror

    {{-- ===== Card Émissions ===== --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-file-invoice-dollar me-2 text-primary"></span>
                Émissions à régler
                <span class="badge bg-secondary ms-2">{{ $emissions->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N°</th>
                            <th>Établissement</th>
                            <th>Code émission</th>
                            <th>Nature recette</th>
                            <th>Période</th>
                            <th class="text-end">Restant à payer</th>
                            <th class="text-end" style="min-width:160px;">Montant à payer</th>
                            <th class="text-center">Régler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emissions as $i => $emission)
                            @php
                                $solde = $emission->soldeDu();
                                $coche = in_array($emission->id, old('emissions', []));
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ $emission->etablissement?->numero ?? '—' }}
                                    @if ($emission->etablissement?->denomination)
                                        <div class="text-muted">{{ $emission->etablissement->denomination }}</div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $emission->numero_emission }}</td>
                                <td>{{ $emission->natureTaxe?->libelle_court ?? $emission->natureTaxe?->libelle ?? '—' }}</td>
                                <td>
                                    {{ $emission->periodicite?->libelle ?? '—' }}
                                    @if ($emission->exerciceFiscal)
                                        <span class="badge bg-light text-dark border">{{ $emission->exerciceFiscal->annee }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-danger">{{ $fcfa($solde) }}</td>
                                <td class="text-end">
                                    <input type="text" inputmode="numeric" name="montant[{{ $emission->id }}]"
                                           value="{{ old('montant.' . $emission->id, (int) $solde) }}"
                                           data-solde="{{ (int) $solde }}"
                                           class="form-control form-control-sm text-end montant-payer @error('montant.' . $emission->id) is-invalid @enderror">
                                    @error('montant.' . $emission->id)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="emissions[]" value="{{ $emission->id }}"
                                           class="form-check-input ligne-check" {{ $coche ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">Total à encaisser (lignes cochées)</th>
                            <th class="text-end" id="total-encaisser">0 FCFA</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Card Mode de paiement ===== --}}
    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-credit-card me-2 text-primary"></span>Mode de paiement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fs-9">Recette <span class="text-danger">*</span></label>
                    <select name="recette_id" class="form-select form-select-lg @error('recette_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($recettes as $rec)
                            <option value="{{ $rec->id }}" {{ old('recette_id') == $rec->id ? 'selected' : '' }}>{{ $rec->libelle }}</option>
                        @endforeach
                    </select>
                    @error('recette_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Mode de règlement <span class="text-danger">*</span></label>
                    <select name="mode_reglement_id" id="mode-reglement" class="form-select form-select-lg @error('mode_reglement_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($modes as $m)
                            <option value="{{ $m->id }}" data-code="{{ $m->code }}" {{ old('mode_reglement_id') == $m->id ? 'selected' : '' }}>{{ $m->libelle }}</option>
                        @endforeach
                    </select>
                    @error('mode_reglement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fs-9">Type de règlement <span class="text-danger">*</span></label>
                    <select name="type_reglement_id" class="form-select form-select-lg @error('type_reglement_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($types as $t)
                            <option value="{{ $t->id }}" {{ old('type_reglement_id') == $t->id ? 'selected' : '' }}>{{ $t->libelle }}</option>
                        @endforeach
                    </select>
                    @error('type_reglement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Groupe chèque / virement (affiché selon le mode) --}}
                <div class="col-12 d-none" id="groupe-cheque">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fs-9">Banque</label>
                            <select name="banque_id" class="form-select form-select-lg">
                                <option value="">— Choisir —</option>
                                @foreach ($banques as $b)
                                    <option value="{{ $b->id }}" {{ old('banque_id') == $b->id ? 'selected' : '' }}>{{ $b->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-9">N° Chèque</label>
                            <input type="text" name="numero_cheque" value="{{ old('numero_cheque') }}"
                                   class="form-control form-control-lg" maxlength="64">
                        </div>
                    </div>
                </div>

                {{-- Groupe Mobile Money (affiché selon le mode) --}}
                <div class="col-12 d-none" id="groupe-mobile">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fs-9">Opérateur</label>
                            <select name="operateur_mobile" class="form-select form-select-lg">
                                <option value="">— Choisir —</option>
                                @foreach (['Orange Money', 'MTN MoMo', 'Moov Money', 'Wave'] as $op)
                                    <option value="{{ $op }}" {{ old('operateur_mobile') === $op ? 'selected' : '' }}>{{ $op }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-9">Référence transaction</label>
                            <input type="text" name="reference_transaction" value="{{ old('reference_transaction') }}"
                                   class="form-control form-control-lg" maxlength="64" placeholder="ID de la transaction">
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-muted fs-10 mb-0 mt-2">
                <span class="fas fa-info-circle me-1"></span>La date du règlement (jour de l'encaissement) et le n° de quittance sont attribués automatiquement.
            </p>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('recouvrements.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-success btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer les règlements
        </button>
    </div>

</form>

@push('scripts')
<script>
(function () {
    const lignes   = Array.from(document.querySelectorAll('.ligne-check'));
    const totalEl  = document.getElementById('total-encaisser');
    const champs   = Array.from(document.querySelectorAll('.montant-payer'));
    const form     = document.getElementById('form-encaissement');

    const chiffres = (val) => val.replace(/\D/g, '');

    // Formate en milliers et plafonne au solde dû (« restant à payer », data-solde)
    function formaterChamp(i) {
        const d = chiffres(i.value);
        if (d === '') { i.value = ''; return; }
        let val = parseInt(d, 10);
        const solde = parseInt(i.dataset.solde, 10) || 0;
        if (val > solde) {
            val = solde;
            i.classList.add('is-invalid');
            setTimeout(() => i.classList.remove('is-invalid'), 800);
        }
        i.value = val.toLocaleString('fr-FR');
    }

    function montantLigne(check) {
        const input = check.closest('tr').querySelector('.montant-payer');
        return check.checked ? (parseInt(chiffres(input.value), 10) || 0) : 0;
    }
    function rafraichirTotal() {
        const total = lignes.reduce((s, c) => s + montantLigne(c), 0);
        totalEl.textContent = total.toLocaleString('fr-FR') + ' FCFA';
    }

    lignes.forEach(c => c.addEventListener('change', rafraichirTotal));

    // Formatage en milliers + plafonnement au solde pendant la saisie
    champs.forEach(i => {
        formaterChamp(i); // état initial (et repopulation après erreur)
        i.addEventListener('input', () => { formaterChamp(i); rafraichirTotal(); });
    });

    // Dé-formatage avant envoi : le serveur reçoit un nombre brut
    form?.addEventListener('submit', () => {
        champs.forEach(i => { i.value = chiffres(i.value); });
    });

    rafraichirTotal();

    // ── Champs conditionnels selon le mode de règlement ──
    const modeSelect   = document.getElementById('mode-reglement');
    const groupeCheque = document.getElementById('groupe-cheque');
    const groupeMobile = document.getElementById('groupe-mobile');

    function basculerMode() {
        const code = modeSelect.options[modeSelect.selectedIndex]?.dataset.code || '';
        groupeCheque.classList.toggle('d-none', !(code === 'CHQ' || code === 'VIR'));
        groupeMobile.classList.toggle('d-none', code !== 'MOB');
    }

    modeSelect?.addEventListener('change', basculerMode);
    basculerMode(); // état initial (repopulation après erreur)
}());
</script>
@endpush

@endif

</x-app-layout>
