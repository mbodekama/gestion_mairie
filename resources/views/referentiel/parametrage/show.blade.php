<x-app-layout :title="'Nature taxe — ' . ($natureTaxe->libelle_court ?? $natureTaxe->code)">

<x-page-header titre="Nature de taxe : {{ $natureTaxe->libelle_court ?? $natureTaxe->code }}" />


<div class="row g-3 mb-3">
    {{-- Fiche nature taxe --}}
    <div class="col-lg-5">
        <div class="card h-100 card-section">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">01</span>
                    <span class="fas fa-balance-scale me-2 text-primary"></span>Nature de taxe
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('referentiel.parametrage.edit', $natureTaxe) }}"
                       class="btn btn-outline-primary btn-sm">
                        <span class="fas fa-edit me-1"></span>Modifier
                    </a>
                    <a href="{{ route('referentiel.parametrage.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <span class="fas fa-arrow-left me-1"></span>Liste
                    </a>
                </div>
            </div>
            <div class="card-body fs-9">
                <dl class="row mb-0">
                    <dt class="col-5 text-600">Code</dt>
                    <dd class="col-7 fw-bold font-monospace fs-7">{{ $natureTaxe->code }}</dd>

                    <dt class="col-5 text-600">Abrégé</dt>
                    <dd class="col-7">{{ $natureTaxe->libelle_court ?? '—' }}</dd>

                    <dt class="col-5 text-600">Libellé</dt>
                    <dd class="col-7">{{ $natureTaxe->libelle ?? '—' }}</dd>

                    <dt class="col-5 text-600">Domaine de taxe</dt>
                    <dd class="col-7">
                        <span class="badge bg-soft-primary text-primary border border-primary">
                            {{ $natureTaxe->domaineTaxe?->libelle ?? '—' }}
                        </span>
                    </dd>

                    <dt class="col-5 text-600">Catégorie</dt>
                    <dd class="col-7">{{ $natureTaxe->categorieImpotTaxe?->libelle ?? '—' }}</dd>
                </dl>

                <div class="mt-3 pt-3 border-top text-center">
                    <div class="fs-4 fw-bold text-primary">{{ $nbEmissions }}</div>
                    <div class="fs-9 text-600">émission(s) enregistrée(s)</div>
                </div>

                <div class="mt-3">
                    @if ($nbEmissions === 0)
                        <form method="POST" action="{{ route('referentiel.parametrage.destroy', $natureTaxe) }}"
                              onsubmit="return confirm('Supprimer la nature de taxe {{ $natureTaxe->code }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <span class="fas fa-trash me-1"></span>Supprimer
                            </button>
                        </form>
                    @else
                        <p class="text-500 fs-9 mb-0 text-center">
                            <span class="fas fa-lock me-1"></span>Suppression impossible : {{ $nbEmissions }} émission(s)
                        </p>
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-clock me-1"></span>Mis à jour le {{ $natureTaxe->updated_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Barèmes --}}
    <div class="col-lg-7">
        <div class="card h-100 card-section">
            <div class="card-header py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <span class="num-section">02</span>
                    <span class="fas fa-table me-2 text-primary"></span>
                    Barèmes associés
                    <span class="badge bg-secondary ms-2">{{ $natureTaxe->baremesTaxe->count() }}</span>
                </h5>
            </div>
            @if ($natureTaxe->baremesTaxe->isEmpty())
                <div class="card-body text-center py-4 text-500 fs-9">
                    <span class="fas fa-table fa-2x mb-2 d-block opacity-40"></span>
                    Aucun barème défini
                </div>
            @else
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0 fs-9">
                            <thead class="table-light">
                                <tr>
                                    <th>Périodicité</th>
                                    <th>Catégorie activité</th>
                                    <th class="text-end">CA borne inf.</th>
                                    <th class="text-end">CA borne sup.</th>
                                    <th class="text-end">Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($natureTaxe->baremesTaxe as $bareme)
                                    <tr>
                                        <td>{{ $bareme->periodicite?->libelle_court ?? $bareme->periodicite?->libelle ?? '—' }}</td>
                                        <td>{{ $bareme->categorieActivite?->libelle ?? 'Toutes' }}</td>
                                        <td class="text-end">{{ number_format((float) $bareme->ca_borne_inf, 0, ',', ' ') }}</td>
                                        <td class="text-end">
                                            {{ (float) $bareme->ca_borne_sup > 0 ? number_format((float) $bareme->ca_borne_sup, 0, ',', ' ') : '∞' }}
                                        </td>
                                        <td class="text-end fw-semi-bold">{{ $bareme->taux }} %</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <div class="card-footer d-flex justify-content-end align-items-center py-2 fs-9 text-600">
                <span class="fas fa-table me-1"></span>{{ $natureTaxe->baremesTaxe->count() }} barème(s) associé(s)
            </div>
        </div>
    </div>
</div>

</x-app-layout>
