<x-app-layout title="Nouvel agent">

<x-page-header titre="Créer un agent" sous-titre="Administration — Agents" />

<form method="POST" action="{{ route('agents.store') }}" novalidate>
    @csrf
    @include('administration.agents._form', ['agent' => null])

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer l'agent
        </button>
    </div>
</form>

</x-app-layout>
