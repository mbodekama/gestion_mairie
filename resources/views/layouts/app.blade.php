<!DOCTYPE html>
<html data-bs-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-rtl.css') }}" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.css') }}" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.css') }}" rel="stylesheet" id="user-style-default">
    <link href="{{ asset('vendors/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <script>
        var isRTL = JSON.parse(localStorage.getItem('isRTL'));
        if (isRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>

    @stack('styles')
</head>

<body>
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            <script>
                var isFluid = JSON.parse(localStorage.getItem('isFluid'));
                if (isFluid) {
                    var container = document.querySelector('[data-layout]');
                    container.classList.remove('container');
                    container.classList.add('container-fluid');
                }
            </script>

            {{-- ============================== Vertical navbar ============================== --}}
            <nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
                <script>
                    var navbarStyle = localStorage.getItem('navbarStyle');
                    if (navbarStyle && navbarStyle !== 'transparent') {
                        document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
                    }
                </script>
                <div class="d-flex align-items-center">
                    <div class="toggle-icon-wrapper">
                        <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation">
                            <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                        </button>
                    </div>
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <div class="d-flex align-items-center py-3">
                            <span class="font-sans-serif text-primary">{{ config('app.name', 'Laravel') }}</span>
                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <div class="navbar-vertical-content scrollbar">
                        <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

                            {{-- ===== Tableau de bord ===== --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>
                                        <span class="nav-link-text ps-1">Vue d'ensemble</span>
                                    </div>
                                </a>
                            </li>

                            {{-- ===== Contribuable ===== --}}
                            @canany(['CONTRIB_CONSULTER', 'CONTRIB_MAILS', 'ETAB_CONSULTER', 'ACTIVITE_CONSULTER'])
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Contribuable</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                @php
                                    $gestionContribActif = request()->routeIs('contribuables.*')
                                        || request()->routeIs('etablissements.*')
                                        || request()->routeIs('referentiel.activites.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $gestionContribActif ? '' : 'collapsed' }}"
                                   href="#gestionContribCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $gestionContribActif ? 'true' : 'false' }}"
                                   aria-controls="gestionContribCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-users"></span></span>
                                        <span class="nav-link-text ps-1">Gestion Contribuable</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $gestionContribActif ? 'show' : '' }}" id="gestionContribCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('CONTRIB_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('contribuables.*') && ! request()->routeIs('contribuables.mails-groupes.*') ? 'active' : '' }}" href="{{ route('contribuables.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-user"></span></span>
                                                    <span class="nav-link-text ps-1">Contribuables</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('CONTRIB_MAILS')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('contribuables.mails-groupes.*') ? 'active' : '' }}" href="{{ route('contribuables.mails-groupes.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-envelope"></span></span>
                                                    <span class="nav-link-text ps-1">Mails groupé</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('ETAB_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('etablissements.*') ? 'active' : '' }}" href="{{ route('etablissements.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-store"></span></span>
                                                    <span class="nav-link-text ps-1">Établissements</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('ACTIVITE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('referentiel.activites.*') ? 'active' : '' }}" href="{{ route('referentiel.activites.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-industry"></span></span>
                                                    <span class="nav-link-text ps-1">Activités économiques</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcanany

                            {{-- ===== Gestion Recouvrement ===== --}}
                            @canany(['EMISSION_CONSULTER', 'RECOUVR_CONSULTER', 'PARAMFISC_CONSULTER'])
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Gestion Recouvrement</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                @php
                                    $gestionFiscaleActif = request()->routeIs('emissions.*')
                                        || request()->routeIs('recouvrements.*')
                                        || request()->routeIs('parametrage.regimes-imposition.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $gestionFiscaleActif ? '' : 'collapsed' }}"
                                   href="#gestionFiscaleCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $gestionFiscaleActif ? 'true' : 'false' }}"
                                   aria-controls="gestionFiscaleCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-hand-holding-usd"></span></span>
                                        <span class="nav-link-text ps-1">Gestion Recouvrement</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $gestionFiscaleActif ? 'show' : '' }}" id="gestionFiscaleCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('EMISSION_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('emissions.*') ? 'active' : '' }}" href="{{ route('emissions.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-file-invoice"></span></span>
                                                    <span class="nav-link-text ps-1">Émission des taxes</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('RECOUVR_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('recouvrements.*') ? 'active' : '' }}" href="{{ route('recouvrements.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-credit-card"></span></span>
                                                    <span class="nav-link-text ps-1">Recouvrements</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMFISC_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('parametrage.regimes-imposition.*') ? 'active' : '' }}" href="{{ route('parametrage.regimes-imposition.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-percentage"></span></span>
                                                    <span class="nav-link-text ps-1">Régimes d'imposition</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcanany

                            {{-- ===== Gestion du Contrôle ===== --}}
                            @canany(['CONTROLE_CONSULTER', 'REDRESS_CONSULTER', 'EXO_CONSULTER', 'CONVOC_CONSULTER'])
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Gestion du Contrôle</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                @can('CONTROLE_CONSULTER')
                                    <a class="nav-link {{ request()->routeIs('controles.*') ? 'active' : '' }}"
                                       href="{{ route('controles.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="fas fa-search-dollar"></span></span>
                                            <span class="nav-link-text ps-1">Contrôles fiscaux</span>
                                        </div>
                                    </a>
                                @endcan

                                @can('REDRESS_CONSULTER')
                                    <a class="nav-link {{ request()->routeIs('redressements.*') ? 'active' : '' }}"
                                       href="{{ route('redressements.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="fas fa-gavel"></span></span>
                                            <span class="nav-link-text ps-1">Redressements</span>
                                        </div>
                                    </a>
                                @endcan

                                @can('EXO_CONSULTER')
                                <a class="nav-link {{ request()->routeIs('exonerations.*') ? 'active' : '' }}"
                                   href="{{ route('exonerations.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-percent"></span></span>
                                        <span class="nav-link-text ps-1">Exonérations</span>
                                    </div>
                                </a>
                                @endcan

                                @can('CONVOC_CONSULTER')
                                <a class="nav-link {{ request()->routeIs('convocations.*') ? 'active' : '' }}"
                                   href="{{ route('convocations.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-envelope"></span></span>
                                        <span class="nav-link-text ps-1">Convocations &amp; mises en demeure</span>
                                    </div>
                                </a>
                                @endcan
                            </li>
                            @endcanany

                            {{-- ===== Paramétrage ===== --}}
                            @canany(['EXERCICE_CONSULTER', 'PARAMFISC_CONSULTER', 'PILOTAGE_CONSULTER'])
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Gestion de la fiscalité </div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                @canany(['EXERCICE_CONSULTER', 'PARAMFISC_CONSULTER', 'PILOTAGE_CONSULTER'])
                                @php
                                    $paramFiscalActif = request()->routeIs('exercices-fiscaux.*')
                                        || request()->routeIs('referentiel.parametrage.*')
                                        || request()->routeIs('parametrage.baremes-taxe.*')
                                        || request()->routeIs('pilotage.objectifs.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $paramFiscalActif ? '' : 'collapsed' }}"
                                   href="#paramFiscalCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $paramFiscalActif ? 'true' : 'false' }}"
                                   aria-controls="paramFiscalCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-sliders-h"></span></span>
                                        <span class="nav-link-text ps-1">Paramétrage fiscal</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $paramFiscalActif ? 'show' : '' }}" id="paramFiscalCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('EXERCICE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('exercices-fiscaux.*') ? 'active' : '' }}" href="{{ route('exercices-fiscaux.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-calendar-alt"></span></span>
                                                    <span class="nav-link-text ps-1">Exercices fiscaux</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMFISC_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('referentiel.parametrage.*') ? 'active' : '' }}" href="{{ route('referentiel.parametrage.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-coins"></span></span>
                                                    <span class="nav-link-text ps-1">Natures de taxe</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMFISC_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('parametrage.baremes-taxe.*') ? 'active' : '' }}" href="{{ route('parametrage.baremes-taxe.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-percent"></span></span>
                                                    <span class="nav-link-text ps-1">Barèmes de taxe</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PILOTAGE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pilotage.objectifs.*') ? 'active' : '' }}" href="{{ route('pilotage.objectifs.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-bullseye"></span></span>
                                                    <span class="nav-link-text ps-1">Objectifs de recouvrement</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @can('PILOTAGE_CONSULTER')
                                @php
                                    $etatsActif = request()->routeIs('pilotage.rapports.*')
                                        || request()->routeIs('pilotage.statistiques.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $etatsActif ? '' : 'collapsed' }}"
                                   href="#etatsCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $etatsActif ? 'true' : 'false' }}"
                                   aria-controls="etatsCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>
                                        <span class="nav-link-text ps-1">États &amp; Statistiques</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $etatsActif ? 'show' : '' }}" id="etatsCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('PILOTAGE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pilotage.rapports.*') ? 'active' : '' }}"
                                               href="{{ route('pilotage.rapports.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-file-pdf"></span></span>
                                                    <span class="nav-link-text ps-1">Édition d'états</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PILOTAGE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pilotage.statistiques.index') ? 'active' : '' }}"
                                               href="{{ route('pilotage.statistiques.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-chart-line"></span></span>
                                                    <span class="nav-link-text ps-1">Statistiques mairie</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PILOTAGE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pilotage.statistiques.calibree') ? 'active' : '' }}"
                                               href="{{ route('pilotage.statistiques.calibree') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-sliders-h"></span></span>
                                                    <span class="nav-link-text ps-1">Statistique calibrée</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcan
                            </li>
                            @endcanany

                            {{-- ===== Sécurité ===== --}}
                            @canany(['AGENT_CONSULTER', 'SERVICE_CONSULTER', 'AUDIT_CONSULTER', 'TERRITOIRE_CONSULTER', 'PARAMETRE_CONSULTER', 'PARAMFISC_CONSULTER', 'SECURITE_GERER_ROLE'])
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Administration &amp; Système</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                @canany(['AGENT_CONSULTER', 'SERVICE_CONSULTER', 'AUDIT_CONSULTER'])
                                @php
                                    $securiteActif = request()->routeIs('agents.*')
                                        || request()->routeIs('services.*')
                                        || request()->routeIs('administration.journal.*')
                                        || request()->routeIs('administration.audit.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $securiteActif ? '' : 'collapsed' }}"
                                   href="#securiteCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $securiteActif ? 'true' : 'false' }}"
                                   aria-controls="securiteCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-shield-alt"></span></span>
                                        <span class="nav-link-text ps-1">Sécurité</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $securiteActif ? 'show' : '' }}" id="securiteCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('AGENT_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('agents.*') ? 'active' : '' }}" href="{{ route('agents.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-user-shield"></span></span>
                                                    <span class="nav-link-text ps-1">Agents &amp; Accès</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('SERVICE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-building"></span></span>
                                                    <span class="nav-link-text ps-1">Services</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('AUDIT_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('administration.journal.*') ? 'active' : '' }}" href="{{ route('administration.journal.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-sign-in-alt"></span></span>
                                                    <span class="nav-link-text ps-1">Journal des connexions</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('AUDIT_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('administration.audit.*') ? 'active' : '' }}" href="{{ route('administration.audit.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-history"></span></span>
                                                    <span class="nav-link-text ps-1">Audit des données</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @canany(['TERRITOIRE_CONSULTER', 'PARAMETRE_CONSULTER', 'PARAMFISC_CONSULTER', 'SECURITE_GERER_ROLE'])
                                @php
                                    $configActif = request()->routeIs('administration.parametres.*')
                                        || request()->routeIs('referentiel.territorial.*')
                                        || request()->routeIs('parametrage.types-personne.*')
                                        || request()->routeIs('parametrage.statuts-contribuable.*')
                                        || request()->routeIs('administration.roles.*');
                                @endphp
                                <a class="nav-link dropdown-indicator {{ $configActif ? '' : 'collapsed' }}"
                                   href="#configCollapse"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ $configActif ? 'true' : 'false' }}"
                                   aria-controls="configCollapse">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-cogs"></span></span>
                                        <span class="nav-link-text ps-1">Configuration</span>
                                    </div>
                                </a>
                                <div class="collapse {{ $configActif ? 'show' : '' }}" id="configCollapse">
                                    <ul class="nav flex-column ms-3">
                                        @can('TERRITOIRE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('referentiel.territorial.*') ? 'active' : '' }}" href="{{ route('referentiel.territorial.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-map-marker-alt"></span></span>
                                                    <span class="nav-link-text ps-1">Territorial</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMETRE_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('administration.parametres.*') ? 'active' : '' }}" href="{{ route('administration.parametres.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-cog"></span></span>
                                                    <span class="nav-link-text ps-1">Paramètres application</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMFISC_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('parametrage.types-personne.*') ? 'active' : '' }}" href="{{ route('parametrage.types-personne.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-id-card"></span></span>
                                                    <span class="nav-link-text ps-1">Types de personne</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('PARAMFISC_CONSULTER')
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('parametrage.statuts-contribuable.*') ? 'active' : '' }}" href="{{ route('parametrage.statuts-contribuable.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-icon"><span class="fas fa-toggle-on"></span></span>
                                                    <span class="nav-link-text ps-1">Statuts contribuable</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('SECURITE_GERER_ROLE')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('administration.roles.*') ? 'active' : '' }}" href="{{ route('administration.roles.index') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-icon"><span class="fas fa-user-tag"></span></span>
                                                        <span class="nav-link-text ps-1">Config Role</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany
                            </li>
                            @endcanany

                            {{-- ===== Mon compte ===== --}}
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">Mon compte</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>
                                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-user-circle"></span></span>
                                        <span class="nav-link-text ps-1">Mon profil</span>
                                    </div>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </nav>
            {{-- ============================== / Vertical navbar ============================== --}}

            <div class="content">

                {{-- ============================== Top navbar ============================== --}}
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                    </button>
                    <a class="navbar-brand me-1 me-sm-3" href="{{ route('dashboard') }}">
                        <div class="d-flex align-items-center">
                            <img class="me-2" src="{{ asset('assets/img/icons/spot-illustrations/falcon.png') }}" alt="" width="40" />
                            <span class="font-sans-serif text-primary">{{ config('app.name', 'Laravel') }}</span>
                        </div>
                    </a>
                    <ul class="navbar-nav align-items-center d-none d-lg-block">
                        <li class="nav-item">
                            <div class="search-box" data-list='{"valueNames":["title"]}'>
                                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                                    <input class="form-control search-input fuzzy-search" type="search" placeholder="{{ __('Search...') }}" aria-label="Search" />
                                    <span class="fas fa-search search-box-icon"></span>
                                </form>
                                <div class="btn-close-falcon-container position-absolute end-0 top-50 translate-middle shadow-none" data-bs-dismiss="search">
                                    <button class="btn btn-link btn-close-falcon p-0" aria-label="Close"></button>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
                        {{-- Theme switcher (light / dark / auto) --}}
                        <li class="nav-item ps-2 pe-0">
                            <div class="dropdown theme-control-dropdown">
                                <a class="nav-link d-flex align-items-center dropdown-toggle fa-icon-wait fs-9 pe-1 py-0" href="#" role="button" id="themeSwitchDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-sun fs-7" data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="light"></span>
                                    <span class="fas fa-moon fs-7" data-fa-transform="shrink-3" data-theme-dropdown-toggle-icon="dark"></span>
                                    <span class="fas fa-adjust fs-7" data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="auto"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-caret border py-0 mt-3" aria-labelledby="themeSwitchDropdown">
                                    <div class="bg-white dark__bg-1000 rounded-2 py-2">
                                        <button class="dropdown-item d-flex align-items-center gap-2" type="button" value="light" data-theme-control="theme"><span class="fas fa-sun"></span>{{ __('Light') }}<span class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                                        <button class="dropdown-item d-flex align-items-center gap-2" type="button" value="dark" data-theme-control="theme"><span class="fas fa-moon"></span>{{ __('Dark') }}<span class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                                        <button class="dropdown-item d-flex align-items-center gap-2" type="button" value="auto" data-theme-control="theme"><span class="fas fa-adjust"></span>{{ __('Auto') }}<span class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                                    </div>
                                </div>
                            </div>
                        </li>

                        {{-- Cart --}}
                        <li class="nav-item d-none d-sm-block">
                            <a class="nav-link px-0 notification-indicator notification-indicator-warning notification-indicator-fill fa-icon-wait" href="#!">
                                <span class="fas fa-shopping-cart" data-fa-transform="shrink-7" style="font-size: 33px;"></span>
                                <span class="notification-indicator-number">1</span>
                            </a>
                        </li>

                        {{-- Notifications --}}
                        @php($notifsNonLues = auth()->user()?->unreadNotifications ?? collect())
                        <li class="nav-item dropdown">
                            <a class="nav-link px-0 fa-icon-wait @if($notifsNonLues->isNotEmpty()) notification-indicator notification-indicator-primary notification-indicator-fill @endif" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll">
                                <span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span>
                                @if($notifsNonLues->isNotEmpty())
                                    <span class="notification-indicator-number">{{ $notifsNonLues->count() }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
                                <div class="card card-notification shadow-none">
                                    <div class="card-header">
                                        <div class="row justify-content-between align-items-center">
                                            <div class="col-auto">
                                                <h6 class="card-header-title mb-0">{{ __('Notifications') }}</h6>
                                            </div>
                                            @if($notifsNonLues->isNotEmpty())
                                                <div class="col-auto ps-0 ps-sm-3">
                                                    <form method="POST" action="{{ route('notifications.tout-lire') }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link card-link fw-normal p-0 border-0">{{ __('Tout marquer comme lu') }}</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="scrollbar-overlay" style="max-height:19rem">
                                        <div class="list-group list-group-flush fw-normal fs-10">
                                            @forelse($notifsNonLues as $notif)
                                                <div class="list-group-item">
                                                    <a class="notification notification-flush notification-unread" href="{{ route('notifications.lire', $notif->id) }}">
                                                        <div class="notification-avatar">
                                                            <div class="avatar avatar-2xl me-3">
                                                                <div class="avatar-name rounded-circle bg-primary-subtle text-primary"><span class="fas {{ $notif->data['icone'] ?? 'fa-bell' }}"></span></div>
                                                            </div>
                                                        </div>
                                                        <div class="notification-body">
                                                            <p class="mb-1">{{ $notif->data['message'] ?? __('Notification') }}</p>
                                                            <span class="notification-time">{{ $notif->created_at?->diffForHumans() }}</span>
                                                        </div>
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center text-muted py-3">{{ __('Aucune notification') }}</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        {{-- App grid menu --}}
                        <li class="nav-item dropdown px-1">
                            <a class="nav-link fa-icon-wait nine-dots p-1" id="navbarDropdownMenu" role="button" data-hide-on-body-scroll="data-hide-on-body-scroll" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="43" viewBox="0 0 16 16" fill="none">
                                    <circle cx="2" cy="2" r="2" fill="#6C6E71"></circle>
                                    <circle cx="2" cy="8" r="2" fill="#6C6E71"></circle>
                                    <circle cx="2" cy="14" r="2" fill="#6C6E71"></circle>
                                    <circle cx="8" cy="8" r="2" fill="#6C6E71"></circle>
                                    <circle cx="8" cy="14" r="2" fill="#6C6E71"></circle>
                                    <circle cx="14" cy="8" r="2" fill="#6C6E71"></circle>
                                    <circle cx="14" cy="14" r="2" fill="#6C6E71"></circle>
                                    <circle cx="8" cy="2" r="2" fill="#6C6E71"></circle>
                                    <circle cx="14" cy="2" r="2" fill="#6C6E71"></circle>
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-caret-bg" aria-labelledby="navbarDropdownMenu">
                                <div class="card shadow-none">
                                    <div class="scrollbar-overlay nine-dots-dropdown">
                                        <div class="card-body px-3">
                                            <div class="row text-center gx-0 gy-0">
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="{{ route('profile.edit') }}">
                                                        <div class="avatar avatar-2xl">
                                                            <div class="avatar-name rounded-circle bg-primary-subtle text-primary"><span>{{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</span></div>
                                                        </div>
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11">{{ __('Account') }}</p>
                                                    </a></div>
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#!"><img class="rounded" src="{{ asset('assets/img/nav-icons/google.png') }}" alt="" width="40" height="40" />
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Google</p>
                                                    </a></div>
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#!"><img class="rounded" src="{{ asset('assets/img/nav-icons/slack.png') }}" alt="" width="40" height="40" />
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Slack</p>
                                                    </a></div>
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#!"><img class="rounded" src="{{ asset('assets/img/nav-icons/github-light.png') }}" alt="" width="40" height="40" />
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Github</p>
                                                    </a></div>
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#!"><img class="rounded" src="{{ asset('assets/img/nav-icons/discord.png') }}" alt="" width="40" height="40" />
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Discord</p>
                                                    </a></div>
                                                <div class="col-4"><a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#!"><img class="rounded" src="{{ asset('assets/img/nav-icons/trello.png') }}" alt="" width="40" height="40" />
                                                        <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Kanban</p>
                                                    </a></div>
                                                <div class="col-12"><a class="btn btn-outline-primary btn-sm mt-4" href="#!">{{ __('Show more') }}</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        {{-- User dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-xl">
                                    <div class="avatar-name rounded-circle bg-primary-subtle text-primary">
                                        <span>{{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                                    <h6 class="dropdown-header fw-medium text-uppercase px-x1 fs-11 pt-0 pb-2">{{ auth()->user()->name }}</h6>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile & account') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('verrouillage.activer') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <span class="fas fa-lock me-2 text-600"></span>Verrouiller la session
                                        </button>
                                    </form>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('Logout') }}</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
                {{-- ============================== / Top navbar ============================== --}}

                @isset($header)
                    <div class="mb-3">
                        {{ $header }}
                    </div>
                @endisset

                <x-flash />

                <x-toast-bienvenue />

                {{ $slot }}

                <footer class="footer border-top mt-4 pt-2 pb-3">
                    <div class="row align-items-center g-0 fs-11 text-600">

                        {{-- Colonne gauche : identité application --}}
                        <div class="col-12 col-md-4 text-center text-md-start mb-1 mb-md-0">
                            <span class="fas fa-landmark me-1 text-primary opacity-75"></span>
                            <span class="fw-semibold">{{ config('app.name') }}</span>
                            <span class="mx-1 text-300">|</span>
                            <span>Gestion fiscale des collectivités</span>
                        </div>

                        {{-- Colonne centre : collectivité + environnement --}}
                        <div class="col-12 col-md-4 text-center mb-1 mb-md-0">
                            <span class="fas fa-map-marker-alt me-1 opacity-50"></span>
                            Mairie d'Abidjan
                            @if(app()->environment('local'))
                                <span class="badge bg-warning text-dark ms-2 fs-11">DEV</span>
                            @elseif(app()->environment('staging'))
                                <span class="badge bg-info ms-2 fs-11">STAGING</span>
                            @endif
                        </div>

                        {{-- Colonne droite : utilisateur + version + copyright --}}
                        <div class="col-12 col-md-4 text-center text-md-end">
                            @auth
                                <span class="fas fa-user-circle me-1 opacity-50"></span>
                                <span class="me-2">{{ auth()->user()->name }}</span>
                                <span class="text-300 mx-1">|</span>
                            @endauth
                            <span class="me-2">v1.0.0</span>
                            <span class="text-300 mx-1">|</span>
                            <span>&copy; {{ now()->year }}</span>
                        </div>

                    </div>
                </footer>
            </div>
        </div>
    </main>

    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('vendors/chart/chart.umd.js') }}"></script>
    <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('vendors/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/confirmation-suppression.js') }}"></script>

    @stack('scripts')

    {{-- Verrouillage automatique après inactivité --}}
    <script>
    (function () {
        var dureeMs = {{ config('session.lock_timeout', 15) }} * 60 * 1000;
        var minuterie;

        function verrouiller() {
            fetch('{{ route('verrouillage.activer') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            }).finally(function () {
                window.location.href = '{{ route('verrouillage.show') }}';
            });
        }

        function reinitialiser() {
            clearTimeout(minuterie);
            minuterie = setTimeout(verrouiller, dureeMs);
        }

        ['mousemove', 'keydown', 'mousedown', 'scroll', 'touchstart'].forEach(function (evt) {
            document.addEventListener(evt, reinitialiser, { passive: true });
        });

        reinitialiser();
    })();
    </script>
</body>

</html>
