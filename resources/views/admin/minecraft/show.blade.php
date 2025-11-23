@extends('layouts.admin')

@section('title', $server->name)

@section('content')
<div class="flex flex-col gap-8">

    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-zinc-50">
                {{ $server->name }}
            </h1>
            <p class="text-zinc-400 text-sm mt-1">
                Manage server settings, status, and configuration.
            </p>
        </div>

        <div class="flex gap-3">
            {{-- Start --}}
            @if(!$server->running)
                <form method="POST" action="{{ route('admin.minecraft.start', $server->id) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 font-semibold text-sm">
                        Start
                    </button>
                </form>
            @endif

            {{-- Stop --}}
            @if($server->running)
                <form method="POST" action="{{ route('admin.minecraft.stop', $server->id) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 font-semibold text-sm">
                        Stop
                    </button>
                </form>
            @endif

            {{-- Restart --}}
            <form method="POST" action="{{ route('admin.minecraft.restart', $server->id) }}">
                @csrf
                <button class="px-4 py-2 rounded-lg bg-zinc-700 hover:bg-zinc-600 font-semibold text-sm">
                    Restart
                </button>
            </form>

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.minecraft.destroy', $server->id) }}">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 rounded-lg bg-red-700 hover:bg-red-600 font-semibold text-sm">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Server Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Info --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
            <h2 class="text-xl font-bold mb-4">Server Details</h2>

            <div class="space-y-2 text-sm text-zinc-300">
                <p><span class="text-zinc-500">Name:</span> {{ $server->name }}</p>
                @php
                    $isRunning = $dockerStatus === 'running';
                @endphp

                <p><span class="text-zinc-500">Status:</span>
                    <span class="text-{{ $isRunning ? 'emerald-400' : 'red-400' }}">
                        {{ $isRunning ? 'Running' : ucfirst($dockerStatus ?? 'unknown') }}
                    </span>
                </p>
                <p><span class="text-zinc-500">RAM:</span> {{ $server->ram }} GB</p>
                <p><span class="text-zinc-500">CPU:</span> {{ $server->cpu }} cores</p>
                <p><span class="text-zinc-500">Port:</span> {{ $server->port }}</p>
                <p><span class="text-zinc-500">Created:</span> {{ $server->created_at->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Environment Variables --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
            <h2 class="text-xl font-bold mb-4">Environment</h2>

            @if($server->environment)
                <pre class="text-xs text-zinc-400 bg-black/30 p-3 rounded-lg overflow-x-auto">{{ print_r($server->environment, true) }}</pre>
            @else
                <p class="text-zinc-500 text-sm italic">No environment variables stored.</p>
            @endif
        </div>
    </div>
    {{-- Console section --}}
    <section class="mt-8 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-semibold text-zinc-50">
                Console
            </h2>

            <div class="flex items-center gap-2">
                {{-- Filter dropdown --}}
                <label class="text-xs text-zinc-400">
                    Filter:
                </label>
                <select
                    id="console-filter"
                    class="text-xs rounded-lg border border-zinc-700 bg-zinc-900/80 px-2 py-1 text-zinc-100 focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="all">All</option>
                    <option value="player">Players & chat</option>
                    <option value="rcon">RCON</option>
                    <option value="warn">Warnings & errors</option>
                    <option value="startup">Hide startup noise</option>
                </select>

                {{-- Manual refresh --}}
                <button
                    id="refresh-console"
                    type="button"
                    class="text-xs px-3 py-1 rounded-full border border-zinc-700 hover:border-emerald-500 hover:text-emerald-400 transition"
                >
                    Refresh now
                </button>
            </div>
        </div>

        {{-- Output window --}}
        <div
            id="console-output"
            class="h-64 overflow-y-auto rounded-xl border border-zinc-800 bg-black/80 p-3 font-mono text-xs text-zinc-200 whitespace-pre-wrap"
        >
            Loading console...
        </div>

        {{-- Command input --}}
        <form
            action="{{ route('admin.minecraft.console.send', $server) }}"
            method="POST"
            class="flex gap-2"
            autocomplete="off"
        >
            @csrf

            <input
                type="text"
                name="command"
                class="flex-1 rounded-lg border border-zinc-700 bg-zinc-900/60 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="Type a Minecraft console command (e.g. say Hello, time set day)..."
            >

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-black hover:bg-emerald-400"
            >
                Send
            </button>
        </form>

        @if (session('status'))
            <p class="text-xs text-emerald-300 mt-1">
                {{ session('status') }}
            </p>
        @endif

        @if (session('error'))
            <p class="text-xs text-red-300 mt-1">
                {{ session('error') }}
            </p>
        @endif
    </section>

    {{-- Console JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const outputEl = document.getElementById('console-output');
            const refreshBtn = document.getElementById('refresh-console');
            const filterSelect = document.getElementById('console-filter');

            // Keep the raw logs in memory and filter client-side
            let rawOutput = '';

            const applyFilter = () => {
                const filter = filterSelect.value;
                let lines = (rawOutput || '').split('\n');

                if (filter === 'player') {
                    // Join / leave / login related
                    lines = lines.filter(line =>
                        line.includes('joined the game') ||
                        line.includes('left the game') ||
                        line.includes('logged in with entity id') ||
                        line.includes('UUID of player')
                    );
                } else if (filter === 'rcon') {
                    lines = lines.filter(line => {
                        // Keep only actual RCON commands (the "real" stuff)
                        if (line.includes('[Rcon]')) {
                            return true;
                        }

                        // Hide noisy connection lifecycle messages:
                        // "Thread RCON Client ... started"
                        // "Thread RCON Client ... shutting down"
                        if (line.includes('Thread RCON Client') && line.includes('started')) return false;
                        if (line.includes('Thread RCON Client') && line.includes('shutting down')) return false;

                        // Hide listener startup spam
                        if (line.includes('RCON Listener') && line.includes('started')) return false;

                        return false;
                    });
                } else if (filter === 'warn') {
                    // Warnings / errors / exceptions
                    lines = lines.filter(line =>
                        line.includes('WARN') ||
                        line.includes('ERROR') ||
                        line.toLowerCase().includes('exception')
                    );
                } else if (filter === 'startup') {
                    // Hide noisy startup stuff like spawn area + library unpack
                    lines = lines.filter(line =>
                        !line.includes('Preparing spawn area') &&
                        !line.includes('Unpacking ') &&
                        !line.includes('Download') &&
                        !line.includes('Loaded 1290 recipes') &&
                        !line.includes('Loaded 1399 advancements')
                    );
                }
                // 'all' = no extra filter

                const text = lines.join('\n').trim();
                outputEl.textContent = text.length ? text : '(no lines for this filter)';
                outputEl.scrollTop = outputEl.scrollHeight;
            };

            const refreshConsole = () => {
                fetch("{{ route('admin.minecraft.console.logs', $server) }}", {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        rawOutput = data.output || '';
                        applyFilter();
                    })
                    .catch(() => {
                        outputEl.textContent = 'Failed to load console logs.';
                    });
            };

            // Initial load
            refreshConsole();

            // Poll every 5 seconds (logs only)
            const intervalId = setInterval(refreshConsole, 5000);

            // Manual refresh
            refreshBtn.addEventListener('click', (e) => {
                e.preventDefault();
                refreshConsole();
            });

            // Re-apply filter when user changes it (no refetch needed)
            filterSelect.addEventListener('change', () => {
                applyFilter();
            });

            // Clean up on page unload
            window.addEventListener('beforeunload', () => clearInterval(intervalId));
        });
    </script>

</div>
@endsection
