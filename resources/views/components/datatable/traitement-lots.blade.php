<div class="card mt-2">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0 text-secondary">
            <span class="fas fa-layer-group me-2"></span>Traitement par lots
        </h5>
    </div>
    <div class="card-body py-2">
        @if ($slot->isNotEmpty())
            <div class="d-flex gap-2 flex-wrap">
                {{ $slot }}
            </div>
        @else
            <p class="text-muted small mb-0">
                <span class="fas fa-clock me-1"></span>
                Les actions de traitement par lots seront disponibles prochainement.
                Cochez des lignes dans le tableau pour les sélectionner.
            </p>
        @endif
    </div>
</div>
