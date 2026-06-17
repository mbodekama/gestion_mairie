<!DOCTYPE html>
<html data-bs-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Régie Fiscale Locale — Gestion de la fiscalité des collectivités</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>

    <link href="{{ asset('vendors/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-rtl.css') }}" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.css') }}" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.css') }}" rel="stylesheet" id="user-style-default">
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

    <style>
        /* Animation au survol des cartes de module (page d'accueil) */
        .module-card {
            transition: transform .25s ease, box-shadow .25s ease;
            cursor: default;
            will-change: transform;
        }

        .module-card:hover {
            transform: translateY(-.5rem);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, .15) !important;
        }

        .module-card__icon {
            transition: transform .25s ease;
        }

        .module-card:hover .module-card__icon {
            transform: scale(1.18) rotate(-6deg);
        }

        /* Animation d'entrée des atouts : apparition en glissant depuis la gauche */
        .atout-col {
            opacity: 0;
            transform: translateX(-2.5rem);
            transition: opacity .6s ease, transform .6s ease;
        }

        .atout-col.is-visible {
            opacity: 1;
            transform: translateX(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .module-card,
            .module-card__icon,
            .atout-col {
                transition: none;
            }

            .module-card:hover,
            .module-card:hover .module-card__icon {
                transform: none;
            }

            .atout-col {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>

<body>
    <main class="main" id="top">

        {{-- ============================== Navbar ============================== --}}
        <nav class="navbar navbar-standard navbar-expand-lg fixed-top navbar-dark" data-navbar-darken-on-scroll="data-navbar-darken-on-scroll">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <span class="text-white dark__text-white">
                        <span class="fas fa-landmark me-2"></span>Régie Fiscale Locale
                    </span>
                </a>
                <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Ouvrir la navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#modules">Modules</a></li>
                        <li class="nav-item"><a class="nav-link" href="#territoire">Territoire</a></li>
                        <li class="nav-item"><a class="nav-link" href="#atouts">Atouts</a></li>
                        @auth
                            <li class="nav-item">
                                <a class="btn btn-outline-light rounded-pill mt-2 mt-lg-0 ms-lg-2" href="{{ url('/dashboard') }}">Espace de travail</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-outline-light rounded-pill mt-2 mt-lg-0 ms-lg-2" href="{{ route('login') }}">Se connecter</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        {{-- ============================== / Navbar ============================== --}}

        {{-- ============================== Hero ============================== --}}
        <section class="py-0 overflow-hidden" id="banner" data-bs-theme="light">
            <div class="bg-holder overlay" style="background-image:url({{ asset('assets/img/generic/bg-1.jpg') }});background-position: center bottom;"></div>
            <!--/.bg-holder-->

            <div class="container">
                <div class="row flex-center pt-8 pt-lg-10 pb-lg-9 pb-xl-7">
                    <div class="col-md-11 col-lg-9 col-xl-8 pb-7 pb-xl-9 text-center text-xl-start">
                        <span class="badge badge-subtle-primary rounded-pill px-3 py-2 mb-3">Backoffice interne · Collectivités territoriales</span>
                        <h1 class="text-white fw-light">Pilotez la fiscalité locale avec <span class="typed-text fw-bold" data-typed-text='["fiabilité","clarté","traçabilité","efficacité"]'></span></h1>
                        <p class="lead text-white opacity-75">Recensement des contribuables et établissements, paramétrage des taxes, émission, recouvrement et contrôle fiscal — réunis dans un espace de travail web moderne, sécurisé et réservé aux agents de la collectivité.</p>
                        @auth
                            <a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-9 py-2" href="{{ url('/dashboard') }}">Accéder à mon espace<span class="fas fa-arrow-right ms-2" data-fa-transform="shrink-6 down-1"></span></a>
                        @else
                            <a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-9 py-2" href="{{ route('login') }}">Se connecter<span class="fas fa-arrow-right ms-2" data-fa-transform="shrink-6 down-1"></span></a>
                        @endauth
                    </div>
                </div>
            </div>
            <!-- end of .container-->
        </section>
        {{-- ============================== / Hero ============================== --}}

        {{-- ============================== Présentation & atouts ============================== --}}
        <section id="atouts">
            <div class="container">
                <div class="row justify-content-center text-center mb-5">
                    <div class="col-lg-8 col-xl-7 col-xxl-6">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">Toute la chaîne fiscale, d'un seul outil</h1>
                        <p class="lead">Du recensement du contribuable jusqu'au recouvrement de la taxe, chaque étape de la fiscalité locale est couverte — dans une solution web fiable, sécurisée et accessible.</p>
                    </div>
                </div>
                <div class="row g-4">
                    @php
                        $atouts = [
                            ['icon' => 'fa-calculator',  'titre' => 'Calculs fiables',     'desc' => "Montants en NUMERIC et calculs en précision décimale : fini les erreurs d'arrondi monétaire."],
                            ['icon' => 'fa-globe',       'titre' => 'Accès web',           'desc' => "Disponible depuis un simple navigateur, sans installation poste par poste."],
                            ['icon' => 'fa-lock',        'titre' => 'Sécurité par action', 'desc' => "Authentification obligatoire, droits par permission et traçabilité des opérations sensibles."],
                            ['icon' => 'fa-layer-group', 'titre' => 'Évolutif',            'desc' => "Mono-collectivité aujourd'hui, prêt pour le multi-collectivité demain."],
                        ];
                    @endphp
                    @foreach ($atouts as $a)
                        <div class="col-md-6 col-lg-3 atout-col" style="transition-delay: {{ $loop->index * 0.12 }}s">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="rounded-circle bg-primary-subtle d-flex flex-center mb-3" style="width:3.5rem;height:3.5rem;">
                                    <span class="fas {{ $a['icon'] }} fs-5 text-primary"></span>
                                </div>
                                <h5 class="mb-2">{{ $a['titre'] }}</h5>
                                <p class="text-body-secondary mb-0">{{ $a['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        {{-- ============================== / Présentation & atouts ============================== --}}

        {{-- ============================== Modules ============================== --}}
        <section class="bg-body-tertiary dark__bg-opacity-50 text-center" id="modules">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">Les modules métier</h1>
                        <p class="lead">Neuf domaines fonctionnels, repris et modernisés depuis l'ERP historique.</p>
                    </div>
                </div>
                <div class="row mt-6 g-4">
                    @php
                        $modules = [
                            ['icon' => 'fa-users',               'color' => 'primary', 'titre' => 'Contribuables & établissements', 'desc' => "Recensement des personnes physiques et morales, dirigeants et établissements exploités, avec recherche multicritère."],
                            ['icon' => 'fa-sliders-h',           'color' => 'info',    'titre' => 'Paramétrage fiscal',            'desc' => "Natures et domaines de taxe, barèmes par tranche de CA, taxe foncière par zone, périodicités et exercices."],
                            ['icon' => 'fa-file-invoice-dollar', 'color' => 'success', 'titre' => 'Émission & recouvrement',         'desc' => "Calcul et émission des taxes par établissement, encaissement des règlements et édition des quittances."],
                            ['icon' => 'fa-folder-open',         'color' => 'warning', 'titre' => 'Dossiers administratifs',        'desc' => "Cycle de vie et circulation des dossiers fiscaux entre services, avec historique des mouvements."],
                            ['icon' => 'fa-envelope-open-text',  'color' => 'primary', 'titre' => 'Convocations',                   'desc' => "Génération, impression et suivi des convocations adressées aux contribuables."],
                            ['icon' => 'fa-balance-scale',       'color' => 'danger',  'titre' => 'Contrôle & exonérations',        'desc' => "Contrôle fiscal, infractions et sanctions, gestion des exonérations partielles."],
                            ['icon' => 'fa-map-marked-alt',      'color' => 'info',    'titre' => 'Référentiel territorial',        'desc' => "Découpage administratif complet de la Côte d'Ivoire, du pays jusqu'à la zone fiscale."],
                            ['icon' => 'fa-user-shield',         'color' => 'success', 'titre' => 'Administration & sécurité',       'desc' => "Agents, comptes et rôles, permissions par action, journal de connexion et piste d'audit."],
                            ['icon' => 'fa-chart-line',          'color' => 'warning', 'titre' => 'Pilotage & éditions',            'desc' => "Objectifs de recouvrement, états et éditions PDF (avis, rôles, quittances, convocations)."],
                        ];
                    @endphp
                    @foreach ($modules as $m)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm module-card">
                                <div class="card-body text-start">
                                    <span class="fas {{ $m['icon'] }} fs-4 text-{{ $m['color'] }} mb-3 d-inline-block module-card__icon"></span>
                                    <h5 class="mb-2">{{ $m['titre'] }}</h5>
                                    <p class="mb-0 text-body-secondary">{{ $m['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- end of .container-->
        </section>
        {{-- ============================== / Modules ============================== --}}

        {{-- ============================== Référentiel territorial (stats) ============================== --}}
        <section class="bg-dark" data-bs-theme="light" id="territoire">
            <div class="container">
                <div class="row justify-content-center text-center mb-5">
                    <div class="col-lg-8 col-xl-7">
                        <h1 class="text-white fs-7 fs-sm-5 fs-md-4">Un référentiel territorial complet</h1>
                        <p class="lead text-white opacity-75">Le découpage administratif de la Côte d'Ivoire est embarqué pour localiser précisément contribuables, établissements et collectivités.</p>
                    </div>
                </div>
                <div class="row text-center g-4">
                    @php
                        $stats = [
                            ['n' => '198', 'l' => 'Communes'],
                            ['n' => '473', 'l' => 'Sous-préfectures'],
                            ['n' => '108', 'l' => 'Départements'],
                            ['n' => '33',  'l' => 'Régions'],
                            ['n' => '14',  'l' => 'Districts'],
                        ];
                    @endphp
                    @foreach ($stats as $s)
                        <div class="col-6 col-md">
                            <h2 class="text-primary fw-bold mb-1">{{ $s['n'] }}</h2>
                            <p class="text-white opacity-75 mb-0">{{ $s['l'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        {{-- ============================== / Référentiel territorial ============================== --}}

        {{-- ============================== CTA ============================== --}}
        <section class="bg-primary" data-bs-theme="light">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8 col-xl-7 col-xxl-6">
                        <h1 class="text-white fs-7 fs-sm-5 fs-md-4">Espace réservé aux agents</h1>
                        <p class="lead text-white opacity-85">L'accès à l'application est strictement interne. Connectez-vous avec le compte fourni par votre administration.</p>
                        @guest
                            <a class="btn btn-light rounded-pill btn-lg mt-3 fs-9 py-2 px-5" href="{{ route('login') }}"><span class="fas fa-sign-in-alt me-2"></span>Se connecter</a>
                        @else
                            <a class="btn btn-light rounded-pill btn-lg mt-3 fs-9 py-2 px-5" href="{{ url('/dashboard') }}"><span class="fas fa-tachometer-alt me-2"></span>Accéder à mon espace</a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>
        {{-- ============================== / CTA ============================== --}}

        {{-- ============================== Footer ============================== --}}
        <section class="py-0 bg-dark" data-bs-theme="light">
            <div>
                <hr class="my-0 text-600 opacity-25" />
                <div class="container py-3">
                    <div class="row justify-content-between align-items-center fs-10">
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600 opacity-85"><span class="fas fa-landmark me-2"></span>Régie Fiscale Locale &copy; {{ now()->year }} — Usage interne</p>
                        </div>
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600 opacity-85">Côte d'Ivoire · v{{ app()->version() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- ============================== / Footer ============================== --}}
    </main>

    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('vendors/typed.js/typed.umd.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    <script>
        // Animation d'entrée des atouts au défilement (apparition en glissant).
        (function () {
            var items = document.querySelectorAll('.atout-col');
            if (!items.length) return;

            if (!('IntersectionObserver' in window)) {
                items.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }

            var observer = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            items.forEach(function (el) { observer.observe(el); });
        })();
    </script>
</body>

</html>
