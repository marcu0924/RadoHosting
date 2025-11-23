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
</div>
@endsection
