<x-guest-layout>
    <x-authentication-card>

        <x-slot name="logo">
            <div class="text-center mb-6">
                <a href='{{ route('home') }}' class="hover:underline">
                    <h1 class="text-3xl font-bold tracking-wide text-emerald-400">
                        Rado <span class="text-white">Hosting</span>
                    </h1>
                </a>
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email or Username --}}
            <div>
                <label for="login">Email or Username</label>
                <x-input id="login" class="block mt-1 w-full"
                    type="text"
                    name="login"
                    value="{{ old('login') }}"
                    required
                    autofocus
                    autocapitalize="none" />
            </div>

            {{-- Password --}}
            <div class="mt-4">
                <label for="password">Password</label>
                <x-input id="password" class="block mt-1 w-full"
                    type="password" name="password" required />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center mt-4">
                <x-checkbox id="remember_me" name="remember" />
                <span class="ml-2 text-sm text-zinc-400">Remember me</span>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif

                <button class="auth-button">
                    Log in
                </button>
            </div>

            {{-- Create Account --}}
            <div class="mt-6 text-center text-sm">
                <span class="text-zinc-400">Don’t have an account?</span>
                <a href="{{ route('register') }}" class="auth-link ml-1">
                    Create one
                </a>
            </div>
        </form>

    </x-authentication-card>
</x-guest-layout>
