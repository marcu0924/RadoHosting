@extends('layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-50">
                Support Tickets
            </h1>
            <p class="mt-1 text-sm text-zinc-400 max-w-xl">
                This page will display all support tickets submitted by users.
            </p>
        </div>
    </div>

    {{-- Tickets Container --}}
    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 backdrop-blur-lg overflow-hidden">

        @php
            $tickets = $tickets ?? collect();
        @endphp

        @if ($tickets->count())
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-900 text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium">ID</th>
                            <th class="px-6 py-3 text-left font-medium">User</th>
                            <th class="px-6 py-3 text-left font-medium">Subject</th>
                            <th class="px-6 py-3 text-left font-medium">Status</th>
                            <th class="px-6 py-3 text-left font-medium">Created</th>
                            <th class="px-6 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">
                        @foreach ($tickets as $ticket)
                            <tr class="hover:bg-zinc-800/40 transition">
                                <td class="px-6 py-4 text-zinc-300">
                                    #{{ $ticket->id }}
                                </td>

                                <td class="px-6 py-4 text-zinc-200">
                                    —
                                </td>

                                <td class="px-6 py-4 text-zinc-100 font-medium">
                                    {{ $ticket->subject ?? 'Ticket subject' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full border border-zinc-700 px-2.5 py-1 text-xs font-semibold text-zinc-400">
                                        Pending
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-zinc-400">
                                    —
                                </td>

                                <td class="px-6 py-4 text-right text-zinc-500">
                                    View
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="rounded-full bg-zinc-800 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M21 16a2 2 0 01-2 2H5l-4 4V4a2 2 0 012-2h16a2 2 0 012 2v12z" />
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-zinc-100">
                    No tickets yet
                </h3>
                <p class="mt-1 text-sm text-zinc-400 max-w-sm">
                    Support tickets will appear here once the system is live.
                </p>
            </div>
        @endif

    </div>
</div>
@endsection
