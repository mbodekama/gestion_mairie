<x-guest-layout :title="__('Forgot Password')">
    <div class="text-center">
        <h4 class="mb-0">{{ __('Forgot your password?') }}</h4>
        <small>{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</small>

        <x-auth-session-status class="mt-3" :status="session('status')" />

        <form class="mb-3 mt-4 text-start" method="POST" action="{{ route('password.email') }}">
            @csrf

            <x-input-label for="email" :value="__('Email address')" class="visually-hidden" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" placeholder="{{ __('Email address') }}" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <button class="btn btn-primary d-block w-100 mt-3" type="submit">{{ __('Send reset link') }}</button>
        </form>
    </div>
</x-guest-layout>
