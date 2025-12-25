@extends('layouts.main')

@section('title', $user->name . ' • Profile')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12 space-y-8">

    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6
                flex flex-col sm:flex-row gap-6 sm:items-center">

        {{-- Avatar --}}
        <div class="shrink-0 flex h-16 w-25 items-center justify-center rounded-xl
                    bg-emerald-500 text-zinc-900 text-2xl font-extrabold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-extrabold text-white truncate">
                {{ $user->name }}
            </h1>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-zinc-400">
                @if (!empty($user->username))
                    <span class="inline-flex items-center rounded-lg
                                bg-zinc-800 px-2.5 py-1
                                font-medium text-zinc-200">
                        {{ '@' . $user->username }}
                    </span>
                @endif

                <span class="text-zinc-500">
                    Joined {{ optional($user->created_at)->format('M Y') }}
                </span>
            </div>

            @if (!empty($user->bio))
                <p class="mt-3 text-zinc-300 max-w-2xl">
                    {{ $user->bio }}
                </p>
            @endif
        </div>

        {{-- Actions --}}
        @auth
            @if (auth()->id() === $user->id)
                <a href="{{ route('settings.index') }}"
                class="shrink-0 inline-flex items-center justify-center rounded-xl
                        bg-emerald-500 px-4 py-2 text-sm font-semibold
                        text-zinc-900 hover:bg-emerald-400">
                    Settings
                </a>
            @endif
        @endauth
    </div>


    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 text-sm">
        <span class="rounded-xl border border-zinc-800 bg-zinc-900/50 px-4 py-2 text-zinc-200">
            Overview
        </span>
        <span class="rounded-xl border border-zinc-800 bg-zinc-900/20 px-4 py-2 text-zinc-400">
            Servers (Soon)
        </span>
        <span class="rounded-xl border border-zinc-800 bg-zinc-900/20 px-4 py-2 text-zinc-400">
            Activity (Soon)
        </span>
    </div>

    {{-- Content --}}
    <div class="grid gap-6 md:grid-cols-3">
        {{-- Left: Stats --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 space-y-4">
            <h2 class="text-lg font-bold text-white">Stats</h2>

            <div class="space-y-2 text-sm text-zinc-300">
                <div class="flex justify-between">
                    <span class="text-zinc-400">Servers</span>
                    <span class="font-semibold text-white">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Tickets</span>
                    <span class="font-semibold text-white">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Plan</span>
                    <span class="font-semibold text-white">—</span>
                </div>
            </div>
        </div>

        {{-- Right: About --}}
        <div class="md:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 space-y-3">
            <h2 class="text-lg font-bold text-white">About</h2>

            <p class="text-sm text-zinc-300 leading-relaxed">
                This is a public profile page. Later you can show “My Servers”, status, badges,
                subscription tier, or anything you want—without mixing it with Jetstream settings.
            </p>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <div class="mt-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 text-sm text-emerald-200">
                    Admin view: you can see this page regardless of ownership.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
