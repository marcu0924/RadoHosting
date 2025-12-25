@extends('layouts.main')

@section('title', $server->name . ' • Server')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white">
                {{ $server->name }}
            </h1>
            <p class="mt-1 text-sm text-zinc-400">
                Manage your server, view console output, and control power state.
            </p>
        </div>

        {{-- Power controls --}}
        <div class="flex gap-3">
            @php $isRunning = (bool) $server->running; @endphp

            @if (! $isRunning)
                <form method="POST" action="{{ route('servers.start', $server) }}">
                    @csrf
                    <button class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                        Start
                    </button>
                </form>
            @endif

            @if ($isRunning)
                <form method="POST" action="{{ route('servers.stop', $server) }}">
                    @csrf
                    <button class="rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-400">
                        Stop
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('servers.restart', $server) }}">
                @csrf
                <button class="rounded-xl bg-zinc-700 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-600">
                    Restart
                </button>
            </form>
        </div>
    </div>

    {{-- Server info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 space-y-3">
            <h2 class="text-lg font-bold text-white">Server Details</h2>

            <div class="text-sm text-zinc-300 space-y-2">
                <div><span class="text-zinc-400">Status:</span>
                    <span class="{{ $isRunning ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $isRunning ? 'Running' : 'Stopped' }}
                    </span>
                </div>

                <div><span class="text-zinc-400">RAM:</span> {{ $server->ram }} GB</div>
                <div><span class="text-zinc-400">CPU:</span> {{ $server->cpu }} Core(s)</div>
                <div><span class="text-zinc-400">Port:</span> {{ $server->port }}</div>

                <div>
                    <span class="text-zinc-400">Connect:</span>
                    <span class="font-mono text-zinc-200">
                        {{ $externalIp }}:{{ $server->port }}
                    </span>
                </div>

                <div>
                    <span class="text-zinc-400">Container:</span>
                    <span class="font-mono text-zinc-400">
                        {{ $server->container_name }}
                    </span>
                </div>

                <div>
                    <span class="text-zinc-400">Created:</span>
                    {{ $server->created_at?->diffForHumans() }}
                </div>
            </div>
        </div>

        {{-- Environment --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
            <h2 class="text-lg font-bold text-white mb-3">Environment</h2>

            @if (!empty($server->environment))
                <pre class="rounded-lg bg-black/40 p-3 text-xs text-zinc-300 overflow-x-auto">{{ json_encode($server->environment, JSON_PRETTY_PRINT) }}</pre>
            @else
                <p class="text-sm text-zinc-500 italic">No environment variables.</p>
            @endif
        </div>
    </div>

    {{-- Console --}}
    <div class="space-y-3">
        <h2 class="text-lg font-bold text-white">Console</h2>

        <div id="console-output"
             class="h-72 overflow-y-auto rounded-xl border border-zinc-800 bg-black/80 p-3 font-mono text-xs text-zinc-200 whitespace-pre-wrap">
            Loading console…
        </div>

        <form method="POST" action="{{ route('servers.console.send', $server) }}" class="flex gap-2">
            @csrf

            <input type="text"
                   name="command"
                   class="flex-1 rounded-xl bg-zinc-950 border border-zinc-800 px-4 py-2 text-sm text-white placeholder-zinc-500"
                   placeholder="Type a command (e.g. say Hello world)">

            <button class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-emerald-400">
                Send
            </button>
        </form>

        @if (session('status'))
            <p class="text-sm text-emerald-300">{{ session('status') }}</p>
        @endif

        @if (session('error'))
            <p class="text-sm text-red-300">{{ session('error') }}</p>
        @endif
    </div>

</div>

{{-- Console JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const output = document.getElementById('console-output');

    const fetchLogs = () => {
        fetch("{{ route('servers.console.logs', $server) }}")
            .then(r => r.json())
            .then(data => {
                output.textContent = data.output || '';
                output.scrollTop = output.scrollHeight;
            })
            .catch(() => {
                output.textContent = 'Failed to load logs.';
            });
    };

    fetchLogs();
    const interval = setInterval(fetchLogs, 5000);

    window.addEventListener('beforeunload', () => clearInterval(interval));
});
</script>
@endsection
