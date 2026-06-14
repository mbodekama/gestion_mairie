@props(['titre', 'sousTitre' => null])

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">{{ $titre }}</h4>
                <p class="mb-0 text-600">
                    {{ $sousTitre ?? __('Bienvenue, :name !', ['name' => auth()->user()->name]) }}
                </p>
            </div>
        </div>
    </div>
</div>
