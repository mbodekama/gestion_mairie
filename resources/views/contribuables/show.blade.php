<x-app-layout :title="'Contribuable — ' . ($contribuable->type_personne === 'PP' ? $contribuable->nom . ' ' . $contribuable->prenoms : $contribuable->raison_sociale)">

@php
    $estPP       = $contribuable->type_personne === 'PP';
    $nomAffiche  = $estPP
        ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
        : ($contribuable->raison_sociale ?? '');
    $initiales   = $estPP
        ? mb_strtoupper(mb_substr($contribuable->nom ?? 'X', 0, 1) . mb_substr($contribuable->prenoms ?? '', 0, 1))
        : mb_strtoupper(mb_substr($contribuable->raison_sociale ?? 'E', 0, 2));
    $statutClasse = match($contribuable->statut) {
        'ACTIF'    => 'success',
        'SUSPENDU' => 'warning',
        'RADIE'    => 'danger',
        default    => 'secondary',
    };
    $fcfa = fn($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
@endphp

{{-- =====================================================================
     BANDEAU IDENTITÉ
     ===================================================================== --}}
<div class="card mb-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="bg-holder" style="background-image: url('{{ asset('img/bg-card-contribuable.svg') }}'); background-size: cover; opacity: .06; pointer-events: none;"></div>
        <div class="d-flex align-items-center gap-3 p-4"
             style="background: linear-gradient(135deg, #0b69ac 0%, #1e3a5f 100%); min-height: 130px; position: relative; z-index: 1;">

            {{-- Avatar initiales --}}
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fs-4 fw-bold text-white border border-3 border-white shadow"
                 style="width:72px; height:72px; background: rgba(255,255,255,.18);">
                {{ $initiales }}
            </div>

            {{-- Identité principale --}}
            <div class="flex-grow-1 text-white">
                <h4 class="mb-1 fw-bold text-white">{{ $nomAffiche }}</h4>
                @if (!$estPP && $contribuable->sigle)
                    <p class="mb-1 text-white-50 fs-9">{{ $contribuable->sigle }}</p>
                @endif
                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                    <span class="badge bg-white text-dark fs-9">
                        <span class="fas fa-hashtag me-1"></span>{{ $contribuable->numero_identifiant ?? '—' }}
                    </span>
                    @if ($contribuable->numero_compte)
                        <span class="badge bg-white text-dark fs-9">
                            <span class="fas fa-folder me-1"></span>{{ $contribuable->numero_compte }}
                        </span>
                    @endif
                    <span class="badge bg-{{ $estPP ? 'info' : 'warning text-dark' }}">
                        {{ $estPP ? 'Personne physique' : 'Personne morale' }}
                    </span>
                    <span class="badge bg-{{ $statutClasse }}">{{ $contribuable->statut ?? '—' }}</span>
                    @if ($contribuable->regimeImposition)
                        <span class="badge bg-light text-dark border">
                            <span class="fas fa-balance-scale me-1"></span>{{ $contribuable->regimeImposition->libelle_court }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                @can('CONTRIB_MODIFIER')
                <a href="{{ route('contribuables.edit', $contribuable) }}"
                   class="btn btn-light btn-sm">
                    <span class="fas fa-edit me-1"></span>Modifier
                </a>
                @endcan
                @can('ETAB_CREER')
                <a href="{{ route('etablissements.create', ['contribuable_id' => $contribuable->id]) }}"
                   class="btn btn-light btn-sm">
                    <span class="fas fa-plus me-1"></span>Établissement
                </a>
                @endcan
                @can('CONTRIB_SUPPRIMER')
                <x-suppression
                    :action="route('contribuables.destroy', $contribuable)"
                    :bloquee="$suppressionBloquee"
                    raison="Rattaché à des impositions / recouvrements : suppression impossible."
                    libelle="ce contribuable"
                    id="modalSuppContrib" />
                @endcan
                <a href="{{ route('contribuables.index') }}"
                   class="btn btn-outline-light btn-sm">
                    <span class="fas fa-arrow-left me-1"></span>Retour
                </a>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     KPI
     ===================================================================== --}}
<div class="row g-3 mb-3">
    {{-- Établissements --}}
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#e8f4fd;">
                    <span class="fas fa-store text-primary"></span>
                </div>
                <div>
                    <p class="text-600 fs-9 mb-0">Établissements</p>
                    <h4 class="mb-0 fw-bold">{{ $contribuable->etablissements->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Total émis --}}
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#e6f9f2;">
                    <span class="fas fa-file-invoice-dollar text-success"></span>
                </div>
                <div>
                    <p class="text-600 fs-9 mb-0">Total émis</p>
                    <h6 class="mb-0 fw-bold">{{ $fcfa($totalEmis) }}</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Total recouvré --}}
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#fff3e0;">
                    <span class="fas fa-hand-holding-usd text-warning"></span>
                </div>
                <div>
                    <p class="text-600 fs-9 mb-0">Total recouvré</p>
                    <h6 class="mb-0 fw-bold">{{ $fcfa($totalRecouvre) }}</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Solde dû --}}
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:{{ bccomp($solde, '0', 2) > 0 ? '#fdecea' : '#e6f9f2' }};">
                    <span class="fas fa-balance-scale {{ bccomp($solde, '0', 2) > 0 ? 'text-danger' : 'text-success' }}"></span>
                </div>
                <div>
                    <p class="text-600 fs-9 mb-0">Solde dû</p>
                    <h6 class="mb-0 fw-bold {{ bccomp($solde, '0', 2) > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $fcfa($solde) }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     CORPS : onglets (col-8) + sidebar (col-4)
     ===================================================================== --}}
<div class="row g-3">

    {{-- ── Onglets ─────────────────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs px-3 pt-2" id="tabsFiche" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-identite" data-bs-toggle="tab"
                                data-bs-target="#pnl-identite" type="button" role="tab">
                            <span class="fas fa-id-card me-1"></span>Identité
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-etab" data-bs-toggle="tab"
                                data-bs-target="#pnl-etab" type="button" role="tab">
                            <span class="fas fa-store me-1"></span>Établissements
                            <span class="badge bg-soft-primary text-primary ms-1">{{ $contribuable->etablissements->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content card-body p-0" id="tabsFicheContent">

                {{-- ── Onglet Identité ──────────────────────────────── --}}
                <div class="tab-pane fade show active p-4" id="pnl-identite" role="tabpanel">
                    @if ($estPP)
                        {{-- Personne Physique --}}
                        <h6 class="text-900 fw-semi-bold mb-3 border-bottom pb-2">
                            <span class="fas fa-user me-2 text-primary"></span>État civil
                        </h6>
                        <div class="row g-3 fs-9">
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Nom</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->nom ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Prénoms</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->prenoms ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Sexe</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->sexe === 'M' ? 'Masculin' : ($contribuable->sexe === 'F' ? 'Féminin' : '—') }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Date de naissance</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->date_naissance?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Lieu de naissance</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->lieu_naissance ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Nationalité</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->nationalite?->libelle ?? '—' }}</p>
                            </div>
                        </div>

                        @if ($contribuable->numero_piece)
                            <h6 class="text-900 fw-semi-bold mt-4 mb-3 border-bottom pb-2">
                                <span class="fas fa-id-badge me-2 text-primary"></span>Pièce d'identité
                            </h6>
                            <div class="row g-3 fs-9">
                                <div class="col-sm-6">
                                    <p class="text-600 mb-0">Nature</p>
                                    <p class="fw-semi-bold mb-0">{{ $contribuable->nature_piece ?? '—' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-600 mb-0">Numéro</p>
                                    <p class="fw-semi-bold mb-0">{{ $contribuable->numero_piece }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($contribuable->nom_pere || $contribuable->nom_mere)
                            <h6 class="text-900 fw-semi-bold mt-4 mb-3 border-bottom pb-2">
                                <span class="fas fa-users me-2 text-primary"></span>Filiation
                            </h6>
                            <div class="row g-3 fs-9">
                                <div class="col-sm-6">
                                    <p class="text-600 mb-0">Père</p>
                                    <p class="fw-semi-bold mb-0">
                                        {{ trim(($contribuable->prenoms_pere ?? '') . ' ' . ($contribuable->nom_pere ?? '')) ?: '—' }}
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-600 mb-0">Mère</p>
                                    <p class="fw-semi-bold mb-0">
                                        {{ trim(($contribuable->prenoms_mere ?? '') . ' ' . ($contribuable->nom_mere ?? '')) ?: '—' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- Personne Morale --}}
                        <h6 class="text-900 fw-semi-bold mb-3 border-bottom pb-2">
                            <span class="fas fa-building me-2 text-primary"></span>Identification juridique
                        </h6>
                        <div class="row g-3 fs-9">
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Raison sociale</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->raison_sociale ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Sigle</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->sigle ?? '—' }}</p>
                            </div>
                            <div class="col-sm-12">
                                <p class="text-600 mb-0">Dénomination commerciale</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->denomination_commerciale ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Forme juridique</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->formeJuridique?->libelle ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Capital social</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->capital_social ? $fcfa($contribuable->capital_social) : '—' }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-600 mb-0">Nombre d'associés</p>
                                <p class="fw-semi-bold mb-0">{{ $contribuable->nombre_associes ?? '—' }}</p>
                            </div>
                        </div>

                        @if ($contribuable->registre_commerce)
                            <h6 class="text-900 fw-semi-bold mt-4 mb-3 border-bottom pb-2">
                                <span class="fas fa-gavel me-2 text-primary"></span>Registre du commerce
                            </h6>
                            <div class="row g-3 fs-9">
                                <div class="col-sm-4">
                                    <p class="text-600 mb-0">Numéro RC</p>
                                    <p class="fw-semi-bold mb-0">{{ $contribuable->registre_commerce }}</p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-600 mb-0">Date d'immatriculation</p>
                                    <p class="fw-semi-bold mb-0">{{ $contribuable->date_registre_commerce?->format('d/m/Y') ?? '—' }}</p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-600 mb-0">Ville RC</p>
                                    <p class="fw-semi-bold mb-0">{{ $contribuable->ville_registre_commerce ?? '—' }}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- ── Onglet Établissements ────────────────────────── --}}
                <div class="tab-pane fade" id="pnl-etab" role="tabpanel">
                    @if ($contribuable->etablissements->isEmpty())
                        <div class="text-center py-5 text-500">
                            <span class="fas fa-store fa-2x mb-2 d-block"></span>
                            Aucun établissement enregistré
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 fs-9">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Dénomination</th>
                                        <th>Commune</th>
                                        <th class="text-center">Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contribuable->etablissements as $etab)
                                        <tr>
                                            <td class="fw-semi-bold text-primary">{{ $etab->code ?? '—' }}</td>
                                            <td>{{ $etab->denomination ?? $etab->nom ?? '—' }}</td>
                                            <td>{{ $etab->commune ?? '—' }}</td>
                                            <td class="text-center">
                                                @php $clsEtab = match($etab->statut ?? '') { 'ACTIF' => 'success', 'FERME' => 'secondary', default => 'secondary' }; @endphp
                                                <span class="badge bg-{{ $clsEtab }}">{{ $etab->statut ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('etablissements.show', $etab) }}"
                                                   class="btn btn-sm btn-outline-info py-0">
                                                    <span class="fas fa-eye"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>{{-- /tab-content --}}
        </div>
    </div>{{-- /col-8 --}}

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="sticky-top" style="top: 72px;">

            {{-- Contacts --}}
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <span class="fas fa-address-book me-2 text-primary"></span>Contacts
                    </h5>
                </div>
                <div class="card-body py-3 fs-9">
                    <ul class="list-unstyled mb-0">
                        @if ($contribuable->cellulaire)
                            <li class="d-flex align-items-center gap-2 py-1 border-bottom">
                                <span class="fas fa-mobile-alt text-600 fa-fw"></span>
                                <div>
                                    <p class="text-600 mb-0" style="font-size:.7rem;">Mobile</p>
                                    <p class="mb-0 fw-semi-bold">{{ $contribuable->cellulaire }}</p>
                                </div>
                            </li>
                        @endif
                        @if ($contribuable->telephone)
                            <li class="d-flex align-items-center gap-2 py-1 border-bottom">
                                <span class="fas fa-phone text-600 fa-fw"></span>
                                <div>
                                    <p class="text-600 mb-0" style="font-size:.7rem;">Fixe</p>
                                    <p class="mb-0 fw-semi-bold">{{ $contribuable->telephone }}</p>
                                </div>
                            </li>
                        @endif
                        @if ($contribuable->fax)
                            <li class="d-flex align-items-center gap-2 py-1 border-bottom">
                                <span class="fas fa-fax text-600 fa-fw"></span>
                                <div>
                                    <p class="text-600 mb-0" style="font-size:.7rem;">Fax</p>
                                    <p class="mb-0 fw-semi-bold">{{ $contribuable->fax }}</p>
                                </div>
                            </li>
                        @endif
                        @if ($contribuable->email)
                            <li class="d-flex align-items-center gap-2 py-1 border-bottom">
                                <span class="fas fa-envelope text-600 fa-fw"></span>
                                <div>
                                    <p class="text-600 mb-0" style="font-size:.7rem;">Email</p>
                                    <p class="mb-0 fw-semi-bold">{{ $contribuable->email }}</p>
                                </div>
                            </li>
                        @endif
                        @if ($contribuable->boite_postale)
                            <li class="d-flex align-items-center gap-2 py-1">
                                <span class="fas fa-mailbox text-600 fa-fw"></span>
                                <div>
                                    <p class="text-600 mb-0" style="font-size:.7rem;">Boîte postale</p>
                                    <p class="mb-0 fw-semi-bold">{{ $contribuable->boite_postale }}</p>
                                </div>
                            </li>
                        @endif
                        @if (!$contribuable->cellulaire && !$contribuable->telephone && !$contribuable->email)
                            <li class="text-500 text-center py-2">Aucun contact renseigné</li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Coordonnées bancaires --}}
            @if ($contribuable->coordonneesBancaires->isNotEmpty())
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h5 class="mb-0">
                            <span class="fas fa-university me-2 text-primary"></span>Coordonnées bancaires
                        </h5>
                    </div>
                    <div class="card-body py-3 fs-9">
                        @foreach ($contribuable->coordonneesBancaires as $cb)
                            <div class="@if (!$loop->last) border-bottom pb-2 mb-2 @endif">
                                <p class="fw-semi-bold mb-1">{{ $cb->banque?->libelle ?? 'Banque inconnue' }}</p>
                                @if ($cb->numero_compte)
                                    <p class="text-600 mb-0">Compte : <span class="fw-medium">{{ $cb->numero_compte }}</span></p>
                                @endif
                                @if ($cb->rib)
                                    <p class="text-600 mb-0">RIB : <span class="fw-medium">{{ $cb->rib }}</span></p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- /sticky-top --}}
    </div>{{-- /col-4 --}}

</div>{{-- /row --}}

{{-- =====================================================================
     DATATABLE ÉMISSIONS DE TAXE
     ===================================================================== --}}
<div class="card mt-3 card-section">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 d-flex align-items-center">
            <span class="num-section">01</span>
            <span class="fas fa-file-invoice-dollar me-2 text-primary"></span>
            Émissions de taxe
            <span class="badge bg-secondary ms-2">{{ $emissions->count() }}</span>
        </h5>
        @can('EMISSION_CREER')
        <a href="{{ route('emissions.create', ['contribuable_id' => $contribuable->id]) }}"
           class="btn btn-primary btn-sm">
            <span class="fas fa-plus me-1"></span>Nouvelle émission
        </a>
        @endcan
    </div>

    @if ($emissions->isEmpty())
        <div class="card-body text-center py-5 text-500">
            <span class="fas fa-file-invoice fa-2x mb-2 d-block opacity-50"></span>
            Aucune émission de taxe enregistrée pour ce contribuable
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>N° Émission</th>
                            <th>Exercice</th>
                            <th>Nature de taxe</th>
                            <th>Établissement</th>
                            <th>Périodicité</th>
                            <th class="text-end">Montant émis</th>
                            <th class="text-end">Recouvré</th>
                            <th class="text-end">Solde dû</th>
                            <th class="text-center">Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emissions as $em)
                            @php
                                $montantRef  = $em->montant_prorata ?? $em->montant_annuel ?? '0';
                                $recouvreEm  = $em->reglements->sum('montant_impute');
                                $soldeEm     = bcsub((string) $montantRef, (string) $recouvreEm, 2);
                                $clsSolde    = bccomp($soldeEm, '0', 2) > 0 ? 'text-danger fw-semi-bold' : 'text-success';
                                $clsStatutEm = match($em->statut ?? '') {
                                    'LIQUIDE', 'RECOUVRE' => 'success',
                                    'EN_COURS'            => 'warning',
                                    'ANNULE'              => 'secondary',
                                    default               => 'light text-dark border',
                                };
                            @endphp
                            <tr>
                                <td class="fw-semi-bold text-primary">{{ $em->numero_emission ?? '—' }}</td>
                                <td>{{ $em->exerciceFiscal?->annee ?? '—' }}</td>
                                <td>{{ $em->natureTaxe?->libelle_court ?? $em->natureTaxe?->libelle ?? '—' }}</td>
                                <td>{{ $em->etablissement?->denomination ?? $em->etablissement?->nom ?? '—' }}</td>
                                <td>{{ $em->periodicite?->libelle ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float) $montantRef, 0, ',', ' ') }}</td>
                                <td class="text-end text-success">{{ number_format((float) $recouvreEm, 0, ',', ' ') }}</td>
                                <td class="text-end {{ $clsSolde }}">{{ number_format((float) $soldeEm, 0, ',', ' ') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $clsStatutEm }}">{{ $em->statut ?? '—' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('emissions.show', $em) }}"
                                       class="btn btn-sm btn-outline-info py-0" title="Voir">
                                        <span class="fas fa-eye"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-semi-bold fs-9">
                        <tr>
                            <td colspan="5" class="text-end text-600">Totaux</td>
                            <td class="text-end">{{ number_format((float) $totalEmis, 0, ',', ' ') }}</td>
                            <td class="text-end text-success">{{ number_format((float) $totalRecouvre, 0, ',', ' ') }}</td>
                            <td class="text-end {{ bccomp($solde, '0', 2) > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format((float) $solde, 0, ',', ' ') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between align-items-center py-2 fs-9">
        <span class="text-600">
            <span class="fas fa-file-invoice-dollar me-1"></span>{{ $emissions->count() }} émission(s)
        </span>
        <span class="text-600">Total émis : <span class="fw-semi-bold">{{ $fcfa($totalEmis) }}</span></span>
    </div>
</div>

{{-- =====================================================================
     DATATABLE EXONÉRATIONS
     ===================================================================== --}}
<div class="card mt-3 card-section">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 d-flex align-items-center">
            <span class="num-section">02</span>
            <span class="fas fa-shield-alt me-2 text-primary"></span>
            Exonérations fiscales
            <span class="badge bg-secondary ms-2">{{ $contribuable->exonerations->count() }}</span>
        </h5>
        @can('EXO_CREER')
        <a href="{{ route('exonerations.create', ['contribuable_id' => $contribuable->id]) }}"
           class="btn btn-primary btn-sm">
            <span class="fas fa-plus me-1"></span>Nouvelle exonération
        </a>
        @endcan
    </div>

    @if ($contribuable->exonerations->isEmpty())
        <div class="card-body text-center py-5 text-500">
            <span class="fas fa-shield-alt fa-2x mb-2 d-block opacity-50"></span>
            Aucune exonération enregistrée pour ce contribuable
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>Type d'exonération</th>
                            <th>N° Décret</th>
                            <th>Date décret</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Durée</th>
                            <th class="text-center">Situation</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contribuable->exonerations as $exo)
                            @php
                                $now = now();
                                if ($exo->date_fin && $exo->date_fin->lt($now)) {
                                    $situExo = ['label' => 'Expirée',  'cls' => 'secondary'];
                                } elseif ($exo->date_debut && $exo->date_debut->lte($now)) {
                                    $situExo = ['label' => 'En cours', 'cls' => 'success'];
                                } else {
                                    $situExo = ['label' => 'À venir',  'cls' => 'info'];
                                }
                                $duree = ($exo->date_debut && $exo->date_fin)
                                    ? $exo->date_debut->diffInMonths($exo->date_fin) . ' mois'
                                    : '—';
                            @endphp
                            <tr>
                                <td class="fw-semi-bold">{{ $exo->typeExoneration?->libelle ?? '—' }}</td>
                                <td>{{ $exo->numero_decret ?? '—' }}</td>
                                <td>{{ $exo->date_decret?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $exo->date_debut?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $exo->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-600">{{ $duree }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $situExo['cls'] }}">{{ $situExo['label'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('exonerations.show', $exo) }}"
                                       class="btn btn-sm btn-outline-info py-0" title="Voir">
                                        <span class="fas fa-eye"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between align-items-center py-2 fs-9">
        <span class="text-600">
            <span class="fas fa-shield-alt me-1"></span>{{ $contribuable->exonerations->count() }} exonération(s)
        </span>
    </div>
</div>

{{-- =====================================================================
     PIÈCES JOINTES
     ===================================================================== --}}
<x-documents.panneau :model="$contribuable" numero="03" />

{{-- =====================================================================
     OBLIGATIONS FISCALES (obligations actives du contribuable, lecture seule)
     ===================================================================== --}}
<div class="card mt-3 card-section">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 d-flex align-items-center">
            <span class="num-section">04</span>
            <span class="fas fa-clipboard-list me-2 text-primary"></span>Obligations fiscales
            <span class="badge bg-secondary ms-1">{{ $contribuable->obligations->count() }}</span>
        </h5>
        @can('PILOTAGE_GERER')
        <a href="{{ route('pilotage.obligations.create', ['code' => $contribuable->numero_identifiant]) }}"
           class="btn btn-sm btn-outline-primary" title="Gérer les obligations">
            <span class="fas fa-edit me-1"></span>Gérer
        </a>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped mb-0 fs-9 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Code nature</th>
                        <th>Libellé taxe</th>
                        <th>Périodicité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contribuable->obligations as $i => $obligation)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $obligation->natureTaxe?->code ?? '—' }}</span></td>
                            <td class="fw-semi-bold">{{ $obligation->natureTaxe?->libelle ?? '—' }}</td>
                            <td>{{ $obligation->periodicite?->libelle ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                Aucune obligation active pour ce contribuable.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center py-2 fs-9">
        <span class="text-600">
            <span class="fas fa-clipboard-list me-1"></span>{{ $contribuable->obligations->count() }} obligation(s) active(s)
        </span>
    </div>
</div>

{{-- =====================================================================
     HISTORIQUE DES MODIFICATIONS
     ===================================================================== --}}
<div id="section-historique">
    <x-historique.timeline
        :historiques="$historiques"
        :labels="$auditLabels"
        :cree-at="$contribuable->created_at"
        :mis-a-jour-at="$contribuable->updated_at"
        titre="Historique des modifications du contribuable"
    />
</div>

</x-app-layout>
