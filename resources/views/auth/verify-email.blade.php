<x-guest-layout :title="__('Verify Email')">
    <div class="text-center">
        <img class="d-block mx-auto mb-4" src="{{ asset('assets/img/icons/spot-illustrations/16.png') }}" alt="Email" width="100" />
        <h3 class="mb-2">{{ __('Please check your email!') }}</h3>
        <p>{{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success" role="alert">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="d-flex justify-content-center gap-2 mt-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="fas fa-paper-plane me-1" data-fa-transform="shrink-4 down-1"></span>{{ __('Resend Verification Email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <span class="fas fa-chevron-left me-1" data-fa-transform="shrink-4 down-1"></span>{{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
