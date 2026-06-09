<x-guest-layout :title="__('Login')">
    <div class="row flex-between-center">
        <div class="col-auto">
            <h3>{{ __('Login') }}</h3>
        </div>
        <div class="col-auto fs-10 text-600">
            <span class="mb-0 fw-semi-bold">{{ __('New User?') }}</span>
            <span><a href="{{ route('register') }}">{{ __('Create account') }}</a></span>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <x-input-label for="password" :value="__('Password')" />
            </div>
            <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="row flex-between-center">
            <div class="col-auto">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label mb-0" for="remember_me">{{ __('Remember me') }}</label>
                </div>
            </div>
            @if (Route::has('password.request'))
                <div class="col-auto">
                    <a class="fs-10" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                </div>
            @endif
        </div>

        <div class="mb-3">
            <button class="btn btn-primary d-block w-100 mt-3" type="submit">{{ __('Log in') }}</button>
        </div>
    </form>
</x-guest-layout>
