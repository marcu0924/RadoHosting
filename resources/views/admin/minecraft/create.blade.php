@extends('layouts.admin')

@section('title', 'Create Minecraft Server')

@section('content')
    <div class="max-w-xl space-y-6">

        <h1 class="text-2xl font-bold text-zinc-50">
            Create Minecraft Server
        </h1>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/60 bg-red-500/10 p-4 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-xl border border-emerald-500/60 bg-emerald-500/10 p-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.minecraft.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-zinc-200 mb-1">Server Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-sm text-zinc-100"
                    required
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-200 mb-1">
                        Port
                        <span class="text-zinc-400 text-xs">(25565–25650)</span>
                    </label>

                    @if (!empty($availablePorts))
                        <select
                            name="port"
                            class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-sm text-zinc-100"
                            required
                        >
                            @foreach ($availablePorts as $port)
                                <option value="{{ $port }}"
                                    {{ (string) old('port') === (string) $port ? 'selected' : '' }}>
                                    {{ $port }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-zinc-500">
                            Only free ports in your allowed range are shown.
                        </p>
                    @else
                        <input
                            type="number"
                            class="w-full rounded-lg bg-zinc-900 border border-red-500/70 px-3 py-2 text-sm text-zinc-100"
                            value="No ports available"
                            disabled
                        >
                        <p class="mt-1 text-xs text-red-400">
                            All ports from 25565 to 25650 are currently in use.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-200 mb-1">Memory (GB)</label>
                    <input
                        type="number"
                        name="memory"
                        value="{{ old('memory', 4) }}"
                        min="1"
                        max="32"
                        class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-sm text-zinc-100"
                        required
                    >
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-200 mb-1">Seed (optional)</label>
                <input
                    type="text"
                    name="seed"
                    value="{{ old('seed') }}"
                    class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-sm text-zinc-100"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-200 mb-1">Minecraft Version (optional)</label>
                <input
                    type="text"
                    name="version"
                    value="{{ old('version') }}"
                    placeholder="e.g. 1.21.1"
                    class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-3 py-2 text-sm text-zinc-100"
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-emerald-400 transition
                    {{ empty($availablePorts) ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ empty($availablePorts) ? 'disabled' : '' }}
                >
                    Create & Start Server
                </button>
            </div>
        </form>
    </div>
@endsection
