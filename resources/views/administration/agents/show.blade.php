<x-app-layout :title="'Agent — ' . $agent->matricule">

@php $nomComplet = trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) ?: '—'; @endphp

{{-- Bandeau --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #0d6efd 0%, #0a3d91 100%); min-height: 120px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:64px; height:64px; background: rgba(255,255,255,.18); font-size: 1.3rem;">
                <span class="fas fa-user-tie"></span>
            </div>
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $nomComplet }}</h4>
                <p class="mb-1 text-white-50 fs-9">
                    <span class="badge bg-white text-dark">{{ $agent->matricule }}</span>
                    {{ $agent->fonctionAgent?->libelle ? '· ' . $agent->fonctionAgent->libelle : '' }}
                </p>
                <span class="badge bg-{{ $agent->actif ? 'success' : 'danger' }}">{{ $agent->actif ? 'Actif' : 'Inactif' }}</span>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                <a href="{{ route('agents.edit', $agent) }}" class="btn btn-light btn-sm">
                    <span class="fas fa-edit me-1"></span>Modifier
                </a>
                <a href="{{ route('agents.index') }}" class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card h-100 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">01</span><span class="fas fa-info-circle me-2 text-primary"></span>Informations</h5></div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-4 text-600">Matricule</dt><dd class="col-8 fw-semi-bold">{{ $agent->matricule }}</dd>
                    <dt class="col-4 text-600">Nom & Prénoms</dt><dd class="col-8">{{ $nomComplet }}</dd>
                    <dt class="col-4 text-600">Fonction</dt><dd class="col-8">{{ $agent->fonctionAgent?->libelle ?? '—' }}</dd>
                    <dt class="col-4 text-600">Grade</dt><dd class="col-8">{{ $agent->gradeAgent?->libelle ?? '—' }}</dd>
                    <dt class="col-4 text-600">Service</dt><dd class="col-8">{{ $agent->service?->libelle ?? '—' }}</dd>
                    <dt class="col-4 text-600">Supérieur</dt>
                    <dd class="col-8">
                        @if ($agent->superieur)
                            <a href="{{ route('agents.show', $agent->superieur) }}">
                                {{ $agent->superieur->matricule }} — {{ trim(($agent->superieur->nom ?? '') . ' ' . ($agent->superieur->prenoms ?? '')) }}
                            </a>
                        @else — @endif
                    </dd>
                    <dt class="col-4 text-600">Observation</dt><dd class="col-8">{{ $agent->observation ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $agent->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        {{-- Comptes utilisateurs --}}
        <div class="card mb-3 card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">02</span><span class="fas fa-user-shield me-2 text-primary"></span>Compte utilisateur</h5></div>
            <div class="card-body fs-9">
                @forelse ($agent->utilisateurs as $u)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom pb-2 mb-2' : '' }}">
                        <span><span class="fas fa-user me-1 text-muted"></span>{{ $u->login ?? $u->email ?? '—' }}</span>
                        <span class="badge bg-light text-dark border">{{ $u->statut ?? '' }}</span>
                    </div>
                @empty
                    <span class="text-muted">Aucun compte rattaché.</span>
                @endforelse
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-user-shield me-1"></span>{{ $agent->utilisateurs->count() }} compte(s)
            </div>
        </div>

        {{-- Subordonnés --}}
        <div class="card card-section">
            <div class="card-header py-3"><h5 class="mb-0 d-flex align-items-center"><span class="num-section">03</span><span class="fas fa-users me-2 text-primary"></span>Subordonnés
                <span class="badge bg-secondary ms-1">{{ $agent->subordonnes->count() }}</span></h5></div>
            <div class="card-body fs-9">
                @forelse ($agent->subordonnes as $sub)
                    <div class="{{ !$loop->last ? 'border-bottom pb-1 mb-1' : '' }}">
                        <a href="{{ route('agents.show', $sub) }}">{{ $sub->matricule }}</a>
                        — {{ trim(($sub->nom ?? '') . ' ' . ($sub->prenoms ?? '')) }}
                    </div>
                @empty
                    <span class="text-muted">Aucun subordonné.</span>
                @endforelse
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-users me-1"></span>{{ $agent->subordonnes->count() }} subordonné(s)
            </div>
        </div>
    </div>
</div>

{{-- Suppression --}}
<div class="card mb-4 border-danger">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div class="fs-9 text-muted">
            La suppression est définitive. Un agent rattaché à un compte utilisateur ne peut être supprimé.
        </div>
        <form method="POST" action="{{ route('agents.destroy', $agent) }}"
              onsubmit="return confirm('Supprimer définitivement l\'agent {{ $agent->matricule }} ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" {{ $agent->utilisateurs->isNotEmpty() ? 'disabled' : '' }}>
                <span class="fas fa-trash me-1"></span>Supprimer l'agent
            </button>
        </form>
    </div>
</div>

</x-app-layout>
