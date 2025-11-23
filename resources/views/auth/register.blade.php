<x-guest-layout>
    <x-authentication-card>

        {{-- Logo --}}
        <x-slot name="logo">
            <div class="text-center mb-6">
                <a href='{{ route('home') }}' class="hover:underline">
                    <h1 class="text-3xl font-bold tracking-wide text-emerald-400">
                        Rado <span class="text-white">Hosting</span>
                    </h1>
                </a>
            </div>
        </x-slot>

        {{-- Validation Errors --}}
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name">Name</label>
                <x-input id="name" class="block mt-1 w-full"
                    type="text" name="name" value="{{ old('name') }}"
                    required autofocus autocomplete="name" />
            </div>

            {{-- Username --}}
            <div class="mt-4">
                <label for="username">Username</label>
                <x-input id="username" class="block mt-1 w-full"
                    type="text" name="username" value="{{ old('username') }}"
                    required autocomplete="username" autocapitalize="none" />
            </div>

            {{-- Email --}}
            <div class="mt-4">
                <label for="email">Email</label>
                <x-input id="email" class="block mt-1 w-full"
                    type="email" name="email" value="{{ old('email') }}"
                    required autocomplete="email" />
            </div>

            {{-- Password --}}
            <div class="mt-4">
                <label for="password">Password</label>
                <x-input id="password" class="block mt-1 w-full"
                    type="password" name="password" required autocomplete="new-password" />
            </div>

            {{-- Confirm Password --}}
            <div class="mt-4">
                <label for="password_confirmation">Confirm Password</label>
                <x-input id="password_confirmation" class="block mt-1 w-full"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            {{-- Terms (if enabled) --}}
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4 text-sm text-zinc-300">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox id="terms" name="terms" required />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="auth-link">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="auth-link">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" class="auth-link">
                    Already registered?
                </a>

                <button class="auth-button">
                    Register
                </button>
            </div>

            {{-- Login Link at Bottom --}}
            <div class="mt-6 text-center text-sm">
                <span class="text-zinc-400">Already have an account?</span>
                <a href="{{ route('login') }}" class="auth-link ml-1">Log in</a>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
