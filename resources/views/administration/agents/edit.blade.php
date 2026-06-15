<x-app-layout :title="'Modifier — ' . $agent->matricule">

<x-page-header :titre="'Modifier l\'agent ' . $agent->matricule" sous-titre="Administration — Agents" />

<form method="POST" action="{{ route('agents.update', $agent) }}" novalidate>
    @csrf @method('PUT')
    @include('administration.agents._form', ['agent' => $agent])

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('agents.show', $agent) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

</x-app-layout>
