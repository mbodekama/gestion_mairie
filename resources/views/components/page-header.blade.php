@props(['titre', 'sousTitre' => null])

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-{{ filled($sousTitre) ? '1' : '0' }}">{{ $titre }}</h4>
                @if (filled($sousTitre))
                    <p class="mb-0 text-600">{{ $sousTitre }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
