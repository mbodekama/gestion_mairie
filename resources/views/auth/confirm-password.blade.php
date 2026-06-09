<x-guest-layout :title="__('Confirm Password')">
    <div class="text-center mb-3">
        <h3>{{ __('Confirm Password') }}</h3>
        <p class="fs-10 text-600">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button class="btn btn-primary d-block w-100 mt-3" type="submit">{{ __('Confirm') }}</button>
    </form>
</x-guest-layout>
