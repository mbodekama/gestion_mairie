<x-app-layout title="Nouvelle convocation / mise en demeure">

<x-page-header titre="Créer une convocation / mise en demeure"
               sous-titre="Relance ou mise en demeure du contribuable (hors contrôle)" />

<form method="POST" action="{{ route('convocations.store') }}" novalidate>
    @csrf
    @include('convocations._form')

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $etablissement ? route('etablissements.show', $etablissement) : route('convocations.index') }}"
           class="btn btn-outline-secondary btn-lg">
            <span class="fas fa-times me-1"></span>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <span class="fas fa-save me-1"></span>Créer la convocation
        </button>
    </div>
</form>

</x-app-layout>
