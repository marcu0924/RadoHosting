@extends('layouts.main')

@section('title', 'Settings')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12 space-y-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Settings</h1>
            <p class="mt-1 text-sm text-zinc-400">
                Manage your account, security, and hosting preferences.
            </p>
        </div>

        <a href="{{ route('users.show', auth()->user()) }}"
           class="text-sm text-emerald-400 hover:text-emerald-300">
            View Public Profile →
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-3">

        {{-- Sidebar --}}
        <aside class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-4 space-y-2 text-sm">
            @php
                $tabs = [
                    'profile' => 'Profile',
                    'hosting' => 'Hosting',
                    'password' => 'Password',
                    'security' => '2FA & Security',
                    'sessions' => 'Sessions',
                    'danger' => 'Danger Zone',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <a href="{{ route('profile.show', ['tab' => $key]) }}"
                   class="block rounded-xl px-4 py-3 transition
                          {{ $tab === $key
                                ? 'bg-zinc-800 text-white'
                                : 'text-zinc-300 hover:bg-zinc-800 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </aside>

        {{-- Content --}}
        <section class="md:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            {{-- PROFILE --}}
            @if ($tab === 'profile')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-white">Profile</h2>
                        <p class="mt-1 text-sm text-zinc-400">Update your display information.</p>
                    </div>

                    <form method="POST" action="{{ route('settings.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">Name</label>
                                <input name="name"
                                       value="{{ old('name', auth()->user()->name) }}"
                                       class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">Email</label>
                                <input name="email"
                                       type="email"
                                       value="{{ old('email', auth()->user()->email) }}"
                                       class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm"
                                       required>
                            </div>

                            <button class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3
                                           text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                                Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- HOSTING --}}
            @if ($tab === 'hosting')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-white">Hosting Preferences</h2>
                        <p class="mt-1 text-sm text-zinc-400">Application-specific settings.</p>
                    </div>

                    <form method="POST" action="{{ route('settings.hosting.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">Default Server Region</label>
                                <select name="default_region"
                                        class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm">
                                    <option value="auto">Auto</option>
                                    <option value="us-east">US-East</option>
                                    <option value="us-central">US-Central</option>
                                    <option value="us-west">US-West</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between gap-4 rounded-xl border border-zinc-800 bg-zinc-950 p-4">
                                <div>
                                    <div class="font-semibold text-white text-sm">Email status alerts</div>
                                    <div class="text-xs text-zinc-400">Notify me when my servers go offline.</div>
                                </div>
                                <input type="checkbox" name="status_alerts"
                                       class="h-5 w-5 accent-emerald-500">
                            </div>

                            <button class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3
                                           text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                                Save Hosting Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- PASSWORD --}}
            @if ($tab === 'password')
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-white">Change Password</h2>

                    <form method="POST" action="{{ route('settings.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">Current password</label>
                                <input type="password" name="current_password"
                                       class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">New password</label>
                                <input type="password" name="password"
                                       class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm text-zinc-300 mb-2">Confirm new password</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm" required>
                            </div>

                            <button class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3
                                           text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- SECURITY --}}
            @if ($tab === 'security')
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-white">2FA & Security</h2>
                    <p class="text-sm text-zinc-400">
                        You can later wire this to Jetstream’s 2FA & recovery code system.
                    </p>

                    <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-4 text-sm text-zinc-300">
                        Coming soon.
                    </div>
                </div>
            @endif

            {{-- SESSIONS --}}
            @if ($tab === 'sessions')
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-white">Sessions</h2>
                    <p class="text-sm text-zinc-400">
                        List and manage active sessions (coming soon).
                    </p>

                    <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-4 text-sm text-zinc-300">
                        Coming soon.
                    </div>
                </div>
            @endif

            {{-- DANGER ZONE --}}
            @if ($tab === 'danger')
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-red-400">Danger Zone</h2>
                    <p class="text-sm text-zinc-400">
                        Deleting your account is permanent and cannot be undone.
                    </p>

                    @if ($errors->has('current_password'))
                        <div class="rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-200">
                            {{ $errors->first('current_password') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.account.destroy') }}"
                          onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.');"
                          class="rounded-2xl border border-red-500/40 bg-red-500/10 p-6 space-y-4">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label class="block text-sm text-zinc-200 mb-2">
                                Confirm your password
                            </label>
                            <input type="password" name="current_password" required
                                   class="w-full rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-3 text-sm"
                                   placeholder="Enter current password">
                        </div>

                        <button class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3
                                       text-sm font-semibold text-white hover:bg-red-700">
                            Delete My Account
                        </button>
                    </form>
                </div>
            @endif

        </section>
    </div>
</div>
@endsection
