<x-app-layout :title="__('Rapports & éditions PDF')">

    <x-page-header titre="Pilotage — Rapports & éditions PDF" />

    <div class="row g-3">

        {{-- Rôle des émissions --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-file-alt fa-2x text-primary me-3"></span>
                        <div>
                            <h6 class="mb-0">Rôle des émissions</h6>
                            <small class="text-muted">Liste détaillée des taxes émises par exercice</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        Génère le rôle fiscal annuel regroupant toutes les émissions de taxe pour un exercice donné,
                        par nature de taxe et par contribuable.
                    </p>
                    <a href="#" class="btn btn-outline-primary btn-sm mt-auto">
                        <span class="fas fa-download me-1"></span>Générer PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Récapitulatif de recouvrement --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-chart-bar fa-2x text-success me-3"></span>
                        <div>
                            <h6 class="mb-0">Récapitulatif de recouvrement</h6>
                            <small class="text-muted">Taux de recouvrement par nature de taxe</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        Tableau de bord du recouvrement : montants émis, encaissés, reste à recouvrer
                        et taux d'encaissement par exercice.
                    </p>
                    <a href="#" class="btn btn-outline-success btn-sm mt-auto">
                        <span class="fas fa-download me-1"></span>Générer PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Avis d'imposition --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-envelope-open-text fa-2x text-warning me-3"></span>
                        <div>
                            <h6 class="mb-0">Avis d'imposition</h6>
                            <small class="text-muted">Édition en masse ou individuelle</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        Génère les avis d'imposition à destination des contribuables pour un exercice
                        et une nature de taxe sélectionnés.
                    </p>
                    <a href="#" class="btn btn-outline-warning btn-sm mt-auto">
                        <span class="fas fa-download me-1"></span>Générer PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Quittances de paiement --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-receipt fa-2x text-info me-3"></span>
                        <div>
                            <h6 class="mb-0">Quittances de paiement</h6>
                            <small class="text-muted">Réimpression des quittances existantes</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        Permet la réimpression des quittances de règlement pour un contribuable
                        ou une période donnée.
                    </p>
                    <a href="#" class="btn btn-outline-info btn-sm mt-auto">
                        <span class="fas fa-download me-1"></span>Générer PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- État des exonérations --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-hand-holding-usd fa-2x text-secondary me-3"></span>
                        <div>
                            <h6 class="mb-0">État des exonérations</h6>
                            <small class="text-muted">Récapitulatif des exemptions accordées</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        État de restitution des montants effectivement exonérés sur les émissions,
                        par exercice et nature de taxe, avec les bases légales (décrets).
                    </p>
                    <form method="GET" action="{{ route('pilotage.rapports.exonerations') }}" class="mt-auto" target="_blank">
                        <div class="input-group input-group-sm">
                            <select name="exercice_fiscal_id" class="form-select">
                                <option value="">Tous exercices</option>
                                @foreach ($exercices as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->annee }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary">
                                <span class="fas fa-download me-1"></span>PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Rapport de contrôle fiscal --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fas fa-search-dollar fa-2x text-danger me-3"></span>
                        <div>
                            <h6 class="mb-0">Rapport de contrôle fiscal</h6>
                            <small class="text-muted">Synthèse des contrôles et sanctions</small>
                        </div>
                    </div>
                    <p class="text-muted fs-9 flex-grow-1">
                        Récapitulatif des contrôles fiscaux menés, montants redressés,
                        délais de réponse et taux de régularisation.
                    </p>
                    <a href="#" class="btn btn-outline-danger btn-sm mt-auto">
                        <span class="fas fa-download me-1"></span>Générer PDF
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
