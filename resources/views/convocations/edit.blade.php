<x-app-layout :title="'Modifier — ' . $convocation->numero">

<x-page-header :titre="'Modifier la convocation ' . $convocation->numero" />

<form method="POST" action="{{ route('convocations.update', $convocation) }}" novalidate>
    @csrf @method('PUT')
    @include('convocations._form')

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('convocations.show', $convocation) }}" class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Enregistrer
        </button>
    </div>
</form>

</x-app-layout>
