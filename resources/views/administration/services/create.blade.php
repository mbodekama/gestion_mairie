<x-app-layout title="Nouveau service">

<x-page-header titre="Créer un service" sous-titre="Administration — Services" />

<form method="POST" action="{{ route('services.store') }}" novalidate>
    @csrf
    @include('administration.services._form', ['service' => null])

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer le service
        </button>
    </div>
</form>

</x-app-layout>
