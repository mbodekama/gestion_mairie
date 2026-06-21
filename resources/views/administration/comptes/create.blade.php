<x-app-layout :title="'Nouveau compte — ' . $agent->matricule">

@php $nomAgent = trim(($agent->nom ?? '') . ' ' . ($agent->prenoms ?? '')) ?: $agent->matricule; @endphp

<x-page-header titre="Créer un compte d'accès"
               :sous-titre="'Agent ' . $agent->matricule . ' — ' . $nomAgent" />

<form method="POST" action="{{ route('agents.comptes.store', $agent) }}" novalidate>
    @csrf

    <div class="card mb-3">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="fas fa-user-shield me-2 text-primary"></span>Compte utilisateur
            </h5>
        </div>
        <div class="card-body">
            @include('administration.comptes._form', [
                'estCreation'       => true,
                'compte'            => null,
                'nomDefaut'         => $nomParDefaut,
                'emailDefaut'       => null,
                'actifDefaut'       => true,
                'rolesSelectionnes' => [],
            ])
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('agents.show', $agent) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer le compte
        </button>
    </div>
</form>

</x-app-layout>
