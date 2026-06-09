<x-guest-layout :title="__('Register')">
    <div class="row flex-between-center">
        <div class="col-auto">
            <h3>{{ __('Register') }}</h3>
        </div>
        <div class="col-auto fs-10 text-600">
            <span class="mb-0 fw-semi-bold">{{ __('Already registered?') }}</span>
            <span><a href="{{ route('login') }}">{{ __('Log in') }}</a></span>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="row gx-2">
            <div class="mb-3 col-sm-6">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mb-3 col-sm-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="mb-3">
            <button class="btn btn-primary d-block w-100 mt-3" type="submit">{{ __('Register') }}</button>
        </div>
    </form>
</x-guest-layout>
