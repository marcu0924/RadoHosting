{{-- resources/views/admin/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="flex flex-col gap-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-50">
                    Admin Dashboard
                </h1>
                <p class="mt-1 text-sm text-zinc-400 max-w-xl">
                    Your host metrics and active servers are shown below. As you add more servers and features,
                    this panel will grow with you.
                </p>
            </div>
        </div>

        {{-- Host Resource Usage --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- CPU Usage --}}
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4">
                <p class="text-xs text-zinc-400 mb-1">CPU Usage</p>
                <p class="text-2xl font-bold text-zinc-100">{{ $stats['cpu_usage'] }}%</p>
            </div>

            {{-- Memory Usage --}}
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4">
                <p class="text-xs text-zinc-400 mb-1">Memory Usage</p>
                <p class="text-2xl font-bold text-zinc-100">{{ $stats['memory_usage'] }}%</p>
            </div>

            {{-- Disk Usage --}}
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4">
                <p class="text-xs text-zinc-400 mb-1">Disk Usage</p>
                <p class="text-2xl font-bold text-zinc-100">{{ $stats['disk_usage'] }}%</p>
            </div>

            {{-- Nodes --}}
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/80 p-4">
                <p class="text-xs text-zinc-400 mb-1">Nodes Online</p>
                <p class="text-2xl font-bold text-zinc-100">
                    {{ $stats['nodes_online'] }} / {{ $stats['nodes_total'] }}
                </p>
            </div>

        </section>

        {{-- Main Grid --}}
        <section class="grid gap-6 lg:grid-cols-3">

            {{-- Servers Table --}}
            <div class="lg:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-950/80 overflow-hidden">

                <div class="px-4 py-3 border-b border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-zinc-100">Servers</h2>
                        <p class="text-xs text-zinc-500">
                            All created servers will appear here with their IP:Port and status.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-[0.7rem] text-zinc-500">
                            {{ $stats['total_servers'] ?? $servers->count() }} total
                        </span>

                        {{-- All Servers button (goes to the Minecraft servers index page) --}}
                        <a href="{{ route('admin.minecraft.index') }}"
                           class="inline-flex items-center rounded-lg border border-zinc-700 bg-zinc-900/80 px-3 py-1.5 text-[0.75rem] font-medium text-zinc-200 hover:border-emerald-400 hover:text-emerald-200 transition">
                            All Servers
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-zinc-950/90 border-b border-zinc-800 text-xs uppercase text-zinc-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Address</th>
                                <th class="px-4 py-2 text-left">Plan</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Created</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-800/80">
                            @forelse ($servers as $server)
                                @php
                                    $name    = $server['name']       ?? 'Unnamed';
                                    $type    = $server['game']       ?? 'Minecraft';
                                    $address = $server['address']    ?? 'N/A';
                                    $plan    = $server['plan']       ?? '—';
                                    $status  = strtolower($server['status'] ?? 'unknown');
                                    $created = $server['created_at'] ?? null;

                                    $statusClasses = match ($status) {
                                        'running' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/40',
                                        'stopped' => 'bg-zinc-700/10 text-zinc-300 border-zinc-600/60',
                                        'error'   => 'bg-red-500/10 text-red-300 border-red-500/40',
                                        default   => 'bg-zinc-700/10 text-zinc-300 border-zinc-600/60',
                                    };

                                    $dotClass = match ($status) {
                                        'running' => 'bg-emerald-400',
                                        'error'   => 'bg-red-400',
                                        default   => 'bg-zinc-400',
                                    };

                                    $minecraftId = $server['minecraft_id'] ?? null;
                                    $rowUrl      = $minecraftId ? route('admin.minecraft.show', $minecraftId) : null;
                                @endphp

                                <tr class="hover:bg-zinc-900/60 transition {{ $rowUrl ? 'cursor-pointer' : '' }}"
                                    @if ($rowUrl) onclick="window.location='{{ $rowUrl }}'" @endif>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-zinc-100">
                                            @if ($rowUrl)
                                                <a href="{{ $rowUrl }}"
                                                   class="hover:text-emerald-300 transition cursor-pointer">
                                                    {{ $name }}
                                                </a>
                                            @else
                                                <span class="text-zinc-300">
                                                    {{ $name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[0.7rem] text-zinc-500">
                                            {{ $server['image'] ?? '' }}
                                            @unless($rowUrl)
                                                <span class="ml-1 text-[0.65rem] text-amber-400">
                                                    (unlinked container)
                                                </span>
                                            @endunless
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-zinc-300">
                                        {{ $type }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs text-emerald-300">
                                            {{ $address }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-zinc-300">
                                        {{ $plan }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[0.7rem] font-medium {{ $statusClasses }}">
                                            <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $dotClass }}"></span>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right text-zinc-400 text-xs">
                                        @if ($created)
                                            {{ \Illuminate\Support\Carbon::parse($created)->diffForHumans() }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-zinc-400">
                                        There are no servers at this time.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination controls --}}
                @if (method_exists($servers, 'links'))
                    <div class="px-4 py-3 border-t border-zinc-800 bg-zinc-950/90">
                        {{ $servers->links() }}
                    </div>
                @endif

            </div>

            {{-- Tickets --}}
            <div class="space-y-4">

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/80 overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-800">
                        <h2 class="text-sm font-semibold text-zinc-100">Tickets</h2>
                        <p class="text-xs text-zinc-500">Support tickets will appear here.</p>
                    </div>

                    <ul class="divide-y divide-zinc-800/80 text-sm">
                        @forelse ($openTickets as $ticket)
                            <li class="px-4 py-3">Ticket placeholder</li>
                        @empty
                            <li class="px-4 py-6 text-center text-xs text-zinc-500">
                                No tickets yet.
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Quick Actions --}}
                <div class="rounded-2xl border border-dashed border-emerald-500/40 bg-emerald-500/5 px-4 py-4">
                    <h2 class="text-sm font-semibold text-emerald-200">Quick Actions</h2>
                    <p class="mt-1 text-xs text-emerald-100/80">
                        Handy shortcuts as you build your panel.
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('admin.minecraft.create') }}"
                           class="rounded-xl border border-emerald-500/70 bg-emerald-500/10 px-3 py-1.5 text-[0.75rem] font-medium text-emerald-100 hover:bg-emerald-500/20">
                            + New Minecraft Server
                        </a>

                        <button class="rounded-xl border border-emerald-500/40 bg-transparent px-3 py-1.5 text-[0.75rem] font-medium text-emerald-100">
                            + New Plan
                        </button>

                        <button class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-1.5 text-[0.75rem] font-medium text-zinc-200 hover:border-emerald-500/60 hover:text-emerald-200">
                            View Users
                        </button>
                    </div>
                </div>

            </div>

        </section>
    </div>
@endsection
