@extends('layouts.admin')

@section('title', 'Minecraft Servers')

@section('content')
<div class="flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-50">
                Minecraft Servers
            </h1>
            <p class="text-sm text-zinc-400 mt-1">
                View and manage your deployed Minecraft instances.
            </p>
        </div>

        <a href="{{ route('admin.minecraft.create') }}"
           class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 font-semibold text-sm">
           + New Server
        </a>
    </div>

    {{-- Server List --}}
    @if($servers->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($servers as $server)
                <a href="{{ route('admin.minecraft.show', $server->id) }}"
                   class="group rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5 hover:border-emerald-500 transition duration-200">
                    <div class="flex flex-col gap-3">
                        {{-- Server Name --}}
                        <h2 class="text-xl font-bold text-white group-hover:text-emerald-300 transition">
                            {{ $server->name }}
                        </h2>

                        {{-- Short Stats --}}
                        <div class="text-sm text-zinc-400">
                            <p>Status:
                                <span class="font-semibold text-{{ $server->running ? 'emerald-400' : 'red-400' }}">
                                    {{ $server->running ? 'Running' : 'Stopped' }}
                                </span>
                            </p>
                            <p>RAM: {{ $server->ram }} GB</p>
                            <p>CPU: {{ $server->cpu }} cores</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-10 text-center">
            <p class="text-zinc-400 text-lg">No Minecraft servers created yet.</p>
            <p class="text-zinc-500 text-sm mt-1">Start by deploying your first server.</p>
        </div>
    @endif
</div>
@endsection
