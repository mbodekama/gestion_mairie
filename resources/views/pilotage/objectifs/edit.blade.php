<x-app-layout :title="'Modifier l\'objectif ' . $objectif->annee">

<x-page-header titre="Pilotage — Modifier l'objectif de recouvrement" />

<form method="POST" action="{{ route('pilotage.objectifs.update', $objectif) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-bullseye me-2 text-primary"></span>Objectif de recouvrement
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Exercice fiscal --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Exercice fiscal <span class="text-danger">*</span></label>
                    <select name="exercice_fiscal_id" id="exercice-select"
                            class="form-select form-select-lg @error('exercice_fiscal_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($exercices as $ex)
                            <option value="{{ $ex->id }}"
                                    data-debut="{{ $ex->date_debut->format('Y-m-d') }}"
                                    data-fin="{{ $ex->date_fin->format('Y-m-d') }}"
                                    {{ old('exercice_fiscal_id', $objectif->exercice_fiscal_id) == $ex->id ? 'selected' : '' }}>
                                {{ $ex->annee }} ({{ $ex->date_debut->format('d/m/Y') }} → {{ $ex->date_fin->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('exercice_fiscal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Période couverte --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Période — du <span class="text-danger">*</span></label>
                    <input type="date" name="periode_debut" id="periode-debut"
                           value="{{ old('periode_debut', $objectif->periode_debut?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('periode_debut') is-invalid @enderror" required>
                    @error('periode_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Période — au <span class="text-danger">*</span></label>
                    <input type="date" name="periode_fin" id="periode-fin"
                           value="{{ old('periode_fin', $objectif->periode_fin?->format('Y-m-d')) }}"
                           class="form-control form-control-lg @error('periode_fin') is-invalid @enderror" required>
                    @error('periode_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Montants --}}
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant objectif (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="montant" value="{{ old('montant', $objectif->montant) }}"
                           class="form-control form-control-lg text-end @error('montant') is-invalid @enderror"
                           min="0" step="0.01" required placeholder="0">
                    @error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-9">Montant révisé (FCFA)</label>
                    <input type="number" name="montant_revise" value="{{ old('montant_revise', $objectif->montant_revise) }}"
                           class="form-control form-control-lg text-end @error('montant_revise') is-invalid @enderror"
                           min="0" step="0.01" placeholder="Optionnel">
                    @error('montant_revise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text fs-10">À renseigner en cas de révision de l'objectif en cours d'exercice.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('pilotage.objectifs.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

@push('scripts')
    <script>
        // À la sélection d'un exercice : borne les dates de période et, si elles
        // sont vides, les pré-remplit avec la période complète de l'exercice.
        (function () {
            const select = document.getElementById('exercice-select');
            const debut  = document.getElementById('periode-debut');
            const fin    = document.getElementById('periode-fin');
            if (!select) return;

            function appliquer(prefill) {
                const opt = select.options[select.selectedIndex];
                const d = opt ? opt.dataset.debut : '';
                const f = opt ? opt.dataset.fin : '';
                debut.min = fin.min = d || '';
                debut.max = fin.max = f || '';
                if (prefill && d && f) {
                    if (!debut.value) debut.value = d;
                    if (!fin.value) fin.value = f;
                }
            }

            select.addEventListener('change', () => appliquer(true));
            appliquer(false); // au chargement, ne touche pas aux valeurs existantes
        })();
    </script>
@endpush

</x-app-layout>
