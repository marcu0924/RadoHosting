<x-guest-layout>
    <div class="auth-card">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email">Email</label>
                <x-input id="email" class="block mt-1 w-full"
                         type="email" name="email"
                         :value="old('email', $request->email)" required />
            </div>

            <div class="mt-4">
                <label for="password">New Password</label>
                <x-input id="password" class="block mt-1 w-full"
                         type="password" name="password" required />
            </div>

            <div class="mt-4">
                <label for="password_confirmation">Confirm Password</label>
                <x-input id="password_confirmation" class="block mt-1 w-full"
                         type="password" name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-6">
                <button class="auth-button">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
