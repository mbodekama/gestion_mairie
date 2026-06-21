<x-app-layout :title="'Contrôle — ' . $controle->numero">

@php
    $contrib = $controle->etablissement?->contribuable;
    $nomContrib = $contrib
        ? ($contrib->type_personne === 'PP'
            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
            : ($contrib->raison_sociale ?? ''))
        : '—';
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';

    $codeEtat = $controle->etatControle?->code;
    $couleurEtat = [
        'INSTRUCTION' => 'secondary', 'VALIDE' => 'info',
        'EXECUTE' => 'primary', 'CLOTURE' => 'success', 'REDRESSE' => 'danger',
    ][$codeEtat] ?? 'secondary';

    // Étapes du workflow (la dernière dépend de l'issue)
    $etapes = [
        'INSTRUCTION' => 'Instruction',
        'VALIDE'      => 'Validé',
        'EXECUTE'     => 'Exécuté',
    ];
    $ordreCourant = ['INSTRUCTION' => 1, 'VALIDE' => 2, 'EXECUTE' => 3, 'CLOTURE' => 4, 'REDRESSE' => 4][$codeEtat] ?? 0;

    // Transitions disponibles indexées par code cible
    $trParCible = $transitions->keyBy(fn($t) => $t->etatCible->code);

    // Compteur de sections numérotées (continu malgré les blocs conditionnels)
    $numSection = 0;
    $noSection  = function () use (&$numSection) {
        return str_pad((string) (++$numSection), 2, '0', STR_PAD_LEFT);
    };
@endphp

{{-- ===== Bandeau ===== --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #6c5ce7 0%, #341f97 100%); min-height: 120px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:64px; height:64px; background: rgba(255,255,255,.18); font-size: 1.3rem;">
                <span class="fas fa-search-dollar"></span>
            </div>
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $controle->numero }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    {{ $nomContrib }}
                    @if ($controle->etablissement)
                        — Étab.
                        <a href="{{ route('etablissements.show', $controle->etablissement) }}" class="text-white">
                            {{ $controle->etablissement->numero }}
                        </a>
                    @endif
                </p>
                <span class="badge bg-{{ $couleurEtat }}">{{ $controle->etatControle?->libelle }}</span>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @if ($codeEtat === 'INSTRUCTION')
                    @can('CONTROLE_INSTRUIRE')
                        <a href="{{ route('controles.edit', $controle) }}" class="btn btn-light btn-sm">
                            <span class="fas fa-edit me-1"></span>Modifier
                        </a>
                    @endcan
                @endif
                @if ($controle->redressement)
                    <a href="{{ route('redressements.show', $controle->redressement) }}" class="btn btn-warning btn-sm fw-semi-bold">
                        <span class="fas fa-gavel me-1"></span>Dossier de redressement
                    </a>
                @endif
                @if ($controle->convocation_id)
                    <a href="{{ route('controles.convocation.pdf', $controle) }}" class="btn btn-light btn-sm" target="_blank">
                        <span class="fas fa-file-pdf me-1"></span>Convocation
                    </a>
                @endif
                @if ($codeEtat === 'CLOTURE')
                    <a href="{{ route('controles.pv-cloture', $controle) }}" class="btn btn-light btn-sm" target="_blank">
                        <span class="fas fa-file-pdf me-1"></span>PV clôture
                    </a>
                @endif
                <a href="{{ route('controles.index') }}" class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>


{{-- ===== Stepper workflow ===== --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            @foreach ($etapes as $code => $libelle)
                @php $ordre = ['INSTRUCTION' => 1, 'VALIDE' => 2, 'EXECUTE' => 3][$code]; @endphp
                <div class="text-center flex-fill">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                                {{ $ordreCourant >= $ordre ? 'bg-primary text-white' : 'bg-light text-muted border' }}"
                         style="width:40px;height:40px;">
                        @if ($ordreCourant > $ordre)<span class="fas fa-check"></span>@else {{ $ordre }} @endif
                    </div>
                    <div class="fs-10 mt-1 {{ $ordreCourant >= $ordre ? 'fw-bold' : 'text-muted' }}">{{ $libelle }}</div>
                </div>
                <div class="flex-fill border-top {{ $ordreCourant > $ordre ? 'border-primary' : '' }}"
                     style="max-width:60px; margin-top:20px;"></div>
            @endforeach
            {{-- Issue --}}
            <div class="text-center flex-fill">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                            {{ $codeEtat === 'CLOTURE' ? 'bg-success text-white' : ($codeEtat === 'REDRESSE' ? 'bg-danger text-white' : 'bg-light text-muted border') }}"
                     style="width:40px;height:40px;">
                    <span class="fas {{ $codeEtat === 'REDRESSE' ? 'fa-gavel' : 'fa-flag-checkered' }}"></span>
                </div>
                <div class="fs-10 mt-1 {{ in_array($codeEtat, ['CLOTURE','REDRESSE']) ? 'fw-bold' : 'text-muted' }}">
                    {{ $codeEtat === 'REDRESSE' ? 'Redressement' : 'Clôture' }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Actions du workflow ===== --}}
@if ($transitions->isNotEmpty() || in_array($codeEtat, ['VALIDE','EXECUTE'], true))
<div class="card mb-3 border-primary">
    <div class="card-header py-3 bg-light">
        <h5 class="mb-0"><span class="fas fa-bolt me-2 text-primary"></span>Actions disponibles</h5>
    </div>
    <div class="card-body d-flex flex-wrap gap-2">
        {{-- Valider (génère la convocation) --}}
        @if ($trParCible->has('VALIDE'))
            @can('CONTROLE_VALIDER')
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalValider">
                <span class="fas fa-check-circle me-1"></span>Valider le contrôle
            </button>
            @endcan
        @endif

        {{-- Saisir / compléter le rapport --}}
        @if (in_array($codeEtat, ['VALIDE','EXECUTE'], true))
            @can('CONTROLE_EXECUTER')
                <a href="{{ route('controles.rapport', $controle) }}" class="btn btn-primary">
                    <span class="fas fa-file-signature me-1"></span>{{ $controle->constats->isEmpty() ? 'Saisir le rapport' : 'Modifier le rapport' }}
                </a>
            @endcan
        @endif

        {{-- Clôturer (favorable) --}}
        @if ($trParCible->has('CLOTURE'))
            @can('CONTROLE_CLOTURER')
            <form method="POST" action="{{ route('controles.transition', $controle) }}"
                  onsubmit="return confirm('Clôturer ce contrôle sans dommage pour le contribuable ?')">
                @csrf
                <input type="hidden" name="code_cible" value="CLOTURE">
                <button type="submit" class="btn btn-success">
                    <span class="fas fa-flag-checkered me-1"></span>Clôturer (favorable)
                </button>
            </form>
            @endcan
        @endif

        {{-- Redresser (défaillant) --}}
        @if ($trParCible->has('REDRESSE'))
            @can('CONTROLE_REDRESSER')
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRedresser">
                <span class="fas fa-gavel me-1"></span>Ouvrir un redressement
            </button>
            @endcan
        @endif

        {{-- Renvoi en instruction --}}
        @if ($trParCible->has('INSTRUCTION'))
            @can('CONTROLE_INSTRUIRE')
            <form method="POST" action="{{ route('controles.transition', $controle) }}"
                  onsubmit="return confirm('Renvoyer le contrôle en instruction ?')">
                @csrf
                <input type="hidden" name="code_cible" value="INSTRUCTION">
                <button type="submit" class="btn btn-outline-secondary">
                    <span class="fas fa-undo me-1"></span>Renvoyer en instruction
                </button>
            </form>
            @endcan
        @endif
    </div>
</div>
@endif

<div class="row g-3 mb-3">
    {{-- Détails --}}
    <div class="col-lg-8">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center"><span class="num-section">{{ $noSection() }}</span><span class="fas fa-info-circle me-2 text-primary"></span>Détails du contrôle</h5>
            </div>
            <div class="card-body fs-9">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-600">N° Contrôle</dt><dd class="col-7 fw-semi-bold">{{ $controle->numero }}</dd>
                            <dt class="col-5 text-600">Contribuable</dt>
                            <dd class="col-7">@if ($contrib)<a href="{{ route('contribuables.show', $contrib) }}">{{ $nomContrib }}</a>@else — @endif</dd>
                            <dt class="col-5 text-600">Établissement</dt>
                            <dd class="col-7">
                                @if ($controle->etablissement)
                                    <a href="{{ route('etablissements.show', $controle->etablissement) }}">{{ $controle->etablissement->numero }}</a>
                                @else — @endif
                            </dd>
                            <dt class="col-5 text-600">Motif</dt><dd class="col-7">{{ $controle->motif ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-6 text-600">Agent instructeur</dt>
                            <dd class="col-6">{{ trim(($controle->agentInstructeur?->nom ?? '') . ' ' . ($controle->agentInstructeur?->prenoms ?? '')) ?: '—' }}</dd>
                            <dt class="col-6 text-600">Période</dt>
                            <dd class="col-6">{{ $controle->periode_debut?->format('d/m/Y') ?? '—' }} → {{ $controle->periode_fin?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Instruit le</dt><dd class="col-6">{{ $controle->date_instruction?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Validé le</dt><dd class="col-6">{{ $controle->date_validation?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Exécuté le</dt><dd class="col-6">{{ $controle->date_execution?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-6 text-600">Clôturé le</dt><dd class="col-6">{{ $controle->date_cloture?->format('d/m/Y') ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
                @if ($controle->rapport_synthese)
                    <hr>
                    <div class="text-600 fs-10 text-uppercase fw-semi-bold mb-1">Synthèse du rapport</div>
                    <p class="mb-0">{{ $controle->rapport_synthese }}</p>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $controle->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Liens --}}
    <div class="col-lg-4">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center"><span class="num-section">{{ $noSection() }}</span><span class="fas fa-link me-2 text-primary"></span>Pièces liées</h5>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">Convocation</dt>
                    <dd class="col-7">
                        @if ($controle->convocation)
                            <a href="{{ route('controle-fiscal.show', $controle->convocation) }}">{{ $controle->convocation->numero }}</a>
                        @else — @endif
                    </dd>
                    <dt class="col-5 text-600">Redressement</dt>
                    <dd class="col-7">
                        @if ($controle->redressement)
                            <a href="{{ route('redressements.show', $controle->redressement) }}">{{ $controle->redressement->numero }}</a>
                            <div class="text-danger fw-bold">{{ $fcfa($controle->redressement->montant_total) }}</div>
                        @else — @endif
                    </dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $controle->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>
</div>

{{-- ===== Constats (rapport) ===== --}}
@if ($controle->constats->isNotEmpty())
<div class="card mb-3 card-section">
    <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center"><span class="num-section">{{ $noSection() }}</span><span class="fas fa-clipboard-check me-2 text-primary"></span>Constats du rapport
            <span class="badge bg-secondary ms-2">{{ $controle->constats->count() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 fs-9 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nature de taxe</th><th class="text-end">Déclaré</th>
                        <th class="text-end">Vérifié</th><th class="text-end">Écart</th>
                        <th>Sanction</th><th>Observation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($controle->constats as $constat)
                        <tr>
                            <td>{{ $constat->natureTaxe?->libelle_court ?? $constat->natureTaxe?->libelle ?? '—' }}</td>
                            <td class="text-end">{{ $fcfa($constat->montant_declare) }}</td>
                            <td class="text-end">{{ $fcfa($constat->montant_verifie) }}</td>
                            <td class="text-end fw-bold {{ $constat->ecart > 0 ? 'text-danger' : 'text-muted' }}">{{ $fcfa($constat->ecart) }}</td>
                            <td>{{ $constat->sanctionFiscale?->libelle ?? '—' }}</td>
                            <td class="text-muted">{{ $constat->observation ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
        <span class="fas fa-clipboard-check me-1"></span>{{ $controle->constats->count() }} constat(s)
    </div>
</div>
@endif

{{-- ===== Historique du workflow ===== --}}
@if ($controle->historiques->isNotEmpty())
<div class="card mb-3 card-section">
    <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center"><span class="num-section">{{ $noSection() }}</span><span class="fas fa-history me-2 text-primary"></span>Historique du workflow</h5>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush fs-9">
            @foreach ($controle->historiques->sortByDesc('created_at') as $h)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <span class="badge bg-light text-dark border">{{ $h->etatSource?->libelle ?? '—' }}</span>
                        <span class="fas fa-arrow-right mx-1 text-muted"></span>
                        <span class="badge bg-primary">{{ $h->etatCible?->libelle ?? '—' }}</span>
                        <span class="ms-2 text-muted">{{ $h->motif }}</span>
                    </span>
                    <span class="text-muted">{{ $h->created_at?->format('d/m/Y H:i') }}</span>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
        <span class="fas fa-history me-1"></span>{{ $controle->historiques->count() }} mouvement(s)
    </div>
</div>
@endif

{{-- ===== Documents ===== --}}
<x-documents.panneau :model="$controle" :numero="$noSection()" />

{{-- ===== Modale Valider (convocation) ===== --}}
@if ($trParCible->has('VALIDE'))
@can('CONTROLE_VALIDER')
<div class="modal fade" id="modalValider" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('controles.transition', $controle) }}">
            @csrf
            <input type="hidden" name="code_cible" value="VALIDE">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="fas fa-check-circle me-2 text-info"></span>Valider et convoquer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted fs-9">La validation génère la convocation du contribuable.</p>
                    <div class="mb-3">
                        <label class="form-label fs-9">Service <span class="text-danger">*</span></label>
                        <select name="service_id" class="form-select" required>
                            <option value="">— Choisir —</option>
                            @foreach ($services as $s)
                                <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-9">Agent <span class="text-danger">*</span></label>
                        <select name="agent_id" class="form-select" required>
                            <option value="">— Choisir —</option>
                            @foreach ($agents as $a)
                                <option value="{{ $a->id }}" {{ $controle->agent_instructeur_id == $a->id ? 'selected' : '' }}>
                                    {{ trim(($a->nom ?? '') . ' ' . ($a->prenoms ?? '')) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fs-9">Date convocation</label>
                            <input type="date" name="date_convocation" value="{{ now()->toDateString() }}" class="form-control">
                        </div>
                        <div class="col-3">
                            <label class="form-label fs-9">Délai (j)</label>
                            <input type="number" name="delai_reponse" min="1" class="form-control" placeholder="15">
                        </div>
                        <div class="col-3">
                            <label class="form-label fs-9">Date limite</label>
                            <input type="date" name="date_limite" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info"><span class="fas fa-check me-1"></span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endif

{{-- ===== Modale Redresser ===== --}}
@if ($trParCible->has('REDRESSE'))
@can('CONTROLE_REDRESSER')
<div class="modal fade" id="modalRedresser" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('controles.transition', $controle) }}">
            @csrf
            <input type="hidden" name="code_cible" value="REDRESSE">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="fas fa-gavel me-2 text-danger"></span>Ouvrir un redressement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted fs-9">
                        Les droits sont calculés depuis les écarts du rapport
                        (<strong>{{ $fcfa($controle->constats->where('ecart', '>', 0)->sum('ecart')) }}</strong>).
                        Les <strong>déclarations complémentaires</strong> sont générées automatiquement à partir
                        des constats. Les <strong>pénalités</strong> se saisissent ensuite, par déclaration, dans le dossier de redressement.
                    </p>
                    <div class="mb-2">
                        <label class="form-label fs-9">Observation</label>
                        <input type="text" name="observation" maxlength="255" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><span class="fas fa-gavel me-1"></span>Ouvrir le redressement</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endif

</x-app-layout>
