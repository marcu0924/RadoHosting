@extends('layouts.main')

@section('title', 'My Servers')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">
                {{ auth()->user()->role === 'admin' ? 'All Servers' : 'My Servers' }}
            </h1>
            <p class="mt-1 text-sm text-zinc-400">
                Manage your Minecraft servers and view console output.
            </p>
        </div>

        <div>
            <a href="{{ route('pricing.minecraft') }}"
               class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                New Server
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('status'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($servers->count() === 0)
        {{-- Empty state --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-10 text-center">
            <h2 class="text-lg font-bold text-white">No servers yet</h2>
            <p class="mt-2 text-sm text-zinc-400">
                Once you purchase a Minecraft plan, your server will appear here.
            </p>

            <div class="mt-6">
                <a href="{{ route('pricing.minecraft') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                    View Minecraft Plans
                </a>
            </div>
        </div>
    @else
        {{-- Server grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($servers as $server)
                <a href="{{ route('servers.show', $server) }}"
                   class="group rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 transition
                          hover:border-emerald-500/50 hover:bg-zinc-900/70">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-white truncate">
                                {{ $server->name }}
                            </h2>

                            <div class="mt-1 text-xs text-zinc-400">
                                Port <span class="font-mono text-zinc-300">{{ $server->port }}</span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <span class="text-xs rounded-full px-3 py-1 border
                            {{ $server->running
                                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300'
                                : 'border-red-500/40 bg-red-500/10 text-red-300' }}">
                            {{ $server->running ? 'Running' : 'Stopped' }}
                        </span>
                    </div>

                    {{-- Specs --}}
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-zinc-300">
                        <span class="rounded-full border border-zinc-700 px-3 py-1">
                            {{ $server->ram }} GB RAM
                        </span>
                        <span class="rounded-full border border-zinc-700 px-3 py-1">
                            {{ $server->cpu }} CPU
                        </span>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-4 flex items-center justify-between text-xs text-zinc-500">
                        <span>
                            Created {{ $server->created_at?->diffForHumans() }}
                        </span>

                        <span class="group-hover:text-emerald-400 transition">
                            Open →
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if (method_exists($servers, 'links'))
            <div class="pt-6">
                {{ $servers->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
