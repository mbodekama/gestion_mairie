<x-app-layout :title="'Modifier — ' . $service->code">

<x-page-header :titre="'Modifier le service ' . $service->code" sous-titre="Administration — Services" />

<form method="POST" action="{{ route('services.update', $service) }}" novalidate>
    @csrf @method('PUT')
    @include('administration.services._form', ['service' => $service])

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('services.show', $service) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

</x-app-layout>
