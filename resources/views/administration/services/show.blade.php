<x-app-layout :title="'Service — ' . $service->code">

{{-- Bandeau --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #0d6efd 0%, #0a3d91 100%); min-height: 110px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white border border-3 border-white shadow"
                 style="width:60px; height:60px; background: rgba(255,255,255,.18); font-size: 1.2rem;">
                <span class="fas fa-building"></span>
            </div>
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $service->libelle }}</h4>
                <p class="mb-0 text-white-50 fs-9">
                    <span class="badge bg-white text-dark">{{ $service->code }}</span>
                    {{ $service->sigle ? '· ' . $service->sigle : '' }}
                </p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                <a href="{{ route('services.edit', $service) }}" class="btn btn-light btn-sm">
                    <span class="fas fa-edit me-1"></span>Modifier
                </a>
                <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-sm">
                    <span class="fas fa-list me-1"></span>Liste
                </a>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
        {{ session('success') }}<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible py-2 fs-9" role="alert">
        {{ session('error') }}<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3"><h5 class="mb-0"><span class="fas fa-info-circle me-2 text-primary"></span>Informations</h5></div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-4 text-600">Code</dt><dd class="col-8 fw-semi-bold">{{ $service->code }}</dd>
                    <dt class="col-4 text-600">Libellé</dt><dd class="col-8">{{ $service->libelle }}</dd>
                    <dt class="col-4 text-600">Sigle</dt><dd class="col-8">{{ $service->sigle ?? '—' }}</dd>
                    <dt class="col-4 text-600">Département</dt><dd class="col-8">{{ $service->departementService?->libelle ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Agents rattachés --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3"><h5 class="mb-0"><span class="fas fa-users me-2 text-primary"></span>Agents rattachés
                <span class="badge bg-secondary ms-1">{{ $service->agents->count() }}</span></h5></div>
            <div class="card-body p-0">
                @if ($service->agents->isEmpty())
                    <p class="text-center py-4 text-muted mb-0">Aucun agent rattaché.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0 fs-9 align-middle">
                            <thead class="table-light"><tr><th>Matricule</th><th>Nom & Prénoms</th><th>Fonction</th></tr></thead>
                            <tbody>
                                @foreach ($service->agents as $a)
                                    <tr>
                                        <td><a href="{{ route('agents.show', $a) }}">{{ $a->matricule }}</a></td>
                                        <td>{{ trim(($a->nom ?? '') . ' ' . ($a->prenoms ?? '')) ?: '—' }}</td>
                                        <td>{{ $a->fonctionAgent?->libelle ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Suppression --}}
<div class="card mb-4 border-danger">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div class="fs-9 text-muted">
            La suppression est définitive. Un service auquel des agents sont rattachés ne peut être supprimé.
        </div>
        <form method="POST" action="{{ route('services.destroy', $service) }}"
              onsubmit="return confirm('Supprimer définitivement le service {{ $service->code }} ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" {{ $service->agents->isNotEmpty() ? 'disabled' : '' }}>
                <span class="fas fa-trash me-1"></span>Supprimer le service
            </button>
        </form>
    </div>
</div>

</x-app-layout>
