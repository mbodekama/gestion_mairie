<x-app-layout :title="'Compte — ' . $compte->email">

@php $nomAgent = trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) ?: $agent->matricule; @endphp

<x-page-header titre="Gérer le compte d'accès"
               :sous-titre="'Agent ' . $agent->matricule . ' — ' . $nomAgent" />

<form method="POST" action="{{ route('agents.comptes.update', [$agent, $compte]) }}" novalidate>
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-user-shield me-2 text-primary"></span>Compte utilisateur
            </h5>
        </div>
        <div class="card-body">
            @include('administration.comptes._form', [
                'estCreation'       => false,
                'nomDefaut'         => $compte->name,
                'emailDefaut'       => $compte->email,
                'actifDefaut'       => $compte->actif,
                'rolesSelectionnes' => $rolesActuels,
            ])
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('agents.show', $agent) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

{{-- Un compte ne peut pas être supprimé : il peut seulement être désactivé
     (case « Compte actif » ci-dessus) ou modifié. --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-2 fs-9 text-muted">
        <span class="fas fa-info-circle text-info"></span>
        Un compte ne peut pas être supprimé. Pour bloquer l'accès, décochez
        « Compte actif » : l'utilisateur ne pourra plus se connecter, sans perdre son historique.
    </div>
</div>

</x-app-layout>
