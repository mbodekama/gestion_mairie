<!DOCTYPE html>
<html data-bs-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
</head>

<body>
    <main class="main" id="top">

        {{-- ============================== Navbar ============================== --}}
        <nav class="navbar navbar-standard navbar-expand-lg fixed-top navbar-dark" data-navbar-darken-on-scroll="data-navbar-darken-on-scroll">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}"><span class="text-white dark__text-white">{{ config('app.name', 'Laravel') }}</span></a>
                <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item">
                                <a class="btn btn-outline-light rounded-pill mt-2 mt-lg-0 ms-lg-2" href="{{ url('/dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Log in') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-outline-light rounded-pill mt-2 mt-lg-0 ms-lg-2" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
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
                    <div class="col-md-11 col-lg-9 col-xl-7 pb-7 pb-xl-9 text-center text-xl-start">
                        <h1 class="text-white fw-light">{{ __('Manage your business with') }} <span class="typed-text fw-bold" data-typed-text='["{{ __('clarity') }}","{{ __('confidence') }}","{{ __('ease') }}","{{ __('control') }}"]'></span></h1>
                        <p class="lead text-white opacity-75">{{ __(':app brings everything you need to run your day-to-day operations together in one simple, modern workspace.', ['app' => config('app.name', 'Laravel')]) }}</p>
                        @auth
                            <a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-9 py-2" href="{{ url('/dashboard') }}">{{ __('Go to dashboard') }}<span class="fas fa-arrow-right ms-2" data-fa-transform="shrink-6 down-1"></span></a>
                        @else
                            <a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-9 py-2" href="{{ route('register') }}">{{ __('Get started') }}<span class="fas fa-arrow-right ms-2" data-fa-transform="shrink-6 down-1"></span></a>
                        @endauth
                    </div>
                </div>
            </div>
            <!-- end of .container-->
        </section>
        {{-- ============================== / Hero ============================== --}}

        {{-- ============================== Highlights ============================== --}}
        <section>
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8 col-xl-7 col-xxl-6">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">{{ __('Built for the way your team works') }}</h1>
                        <p class="lead">{{ __('A clean dashboard, powerful tools, and an interface designed to help you and your team get more done, faster.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        {{-- ============================== / Highlights ============================== --}}

        {{-- ============================== Features ============================== --}}
        <section class="bg-body-tertiary dark__bg-opacity-50 text-center">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">{{ __("Here's what you get") }}</h1>
                        <p class="lead">{{ __('Everything you need, right out of the box.') }}</p>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <div class="card-span-img"><span class="fas fa-gauge-high fs-5 text-info"></span></div>
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">{{ __('A modern dashboard') }}</h5>
                                <p>{{ __('A clean, responsive interface that gives you a clear overview of your activity at a glance.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-6 mt-lg-0">
                        <div class="card card-span h-100">
                            <div class="card-span-img"><span class="fas fa-shield-halved fs-4 text-success"></span></div>
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">{{ __('Secure by default') }}</h5>
                                <p>{{ __('Your account and data are protected with secure authentication built on Laravel.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-6 mt-lg-0">
                        <div class="card card-span h-100">
                            <div class="card-span-img"><span class="fas fa-user-gear fs-3 text-danger"></span></div>
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">{{ __('Easy to manage') }}</h5>
                                <p>{{ __('Manage your profile and preferences from a simple, dedicated account page.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of .container-->
        </section>
        {{-- ============================== / Features ============================== --}}

        {{-- ============================== CTA ============================== --}}
        <section class="bg-dark" data-bs-theme="light">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8 col-xl-7 col-xxl-6">
                        <h1 class="text-white fs-7 fs-sm-5 fs-md-4">{{ __('Ready to get started?') }}</h1>
                        <p class="lead text-white opacity-75">{{ __('Create your account and start using :app today.', ['app' => config('app.name', 'Laravel')]) }}</p>
                        @guest
                            <a class="btn btn-light rounded-pill btn-lg mt-3 fs-9 py-2 px-5" href="{{ route('register') }}">{{ __('Create an account') }}</a>
                        @else
                            <a class="btn btn-light rounded-pill btn-lg mt-3 fs-9 py-2 px-5" href="{{ url('/dashboard') }}">{{ __('Go to dashboard') }}</a>
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
                    <div class="row justify-content-between fs-10">
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600 opacity-85">{{ config('app.name', 'Laravel') }} &copy; {{ now()->year }}</p>
                        </div>
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600 opacity-85">v{{ app()->version() }}</p>
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
</body>

</html>
