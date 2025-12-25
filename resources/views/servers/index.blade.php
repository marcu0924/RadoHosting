@extends('layouts.main')

@section('title', 'My Servers')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-8">

    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">My Servers</h1>
            <p class="mt-1 text-sm text-zinc-400">
                View and manage your game servers.
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($servers->count() === 0)
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-10 text-center">
            <h2 class="text-lg font-bold text-white">No servers yet</h2>
            <p class="mt-2 text-sm text-zinc-400">
                When you purchase Minecraft hosting, your server will appear here.
            </p>
            <div class="mt-6">
                <a href="{{ route('pricing.minecraft') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                    View Minecraft Plans
                </a>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($servers as $server)
                <a href="{{ route('servers.show', $server) }}"
                   class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 hover:border-emerald-500/50 transition">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-lg font-bold text-white truncate">{{ $server->name }}</div>
                            <div class="mt-1 text-xs text-zinc-400">
                                Port <span class="font-mono text-zinc-300">{{ $server->port }}</span>
                            </div>
                        </div>

                        <div class="text-xs rounded-full px-3 py-1 border border-zinc-700 text-zinc-300">
                            {{ $server->ram }}GB • {{ $server->cpu }} CPU
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-zinc-500">
                        Created {{ $server->created_at?->diffForHumans() }}
                    </div>
                </a>
            @endforeach
        </div>

        <div>
            {{ $servers->links() }}
        </div>
    @endif

</div>
@endsection
