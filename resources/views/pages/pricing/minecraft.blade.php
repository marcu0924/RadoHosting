@extends('layouts.main')

@section('title', 'Minecraft Server Plans')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-16 space-y-16">

    {{-- Page Header --}}
    <header class="text-center space-y-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white">
            Minecraft Server Hosting
        </h1>
        <p class="max-w-2xl mx-auto text-lg text-zinc-400">
            High-performance Minecraft servers with instant setup, SSD storage,
            and full control from your Rado Hosting panel.
        </p>
    </header>

    {{-- Plans Grid --}}
    <section class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

        @foreach ($plans as $plan)
            <div
                class="relative flex flex-col rounded-2xl border border-zinc-800 bg-zinc-900/60 backdrop-blur
                       p-6 transition hover:border-emerald-500/60 hover:shadow-lg hover:shadow-emerald-500/10">

                {{-- Popular Badge --}}
                @if (!empty($plan['popular']) && $plan['popular'])
                    <span class="absolute -top-3 right-4 rounded-full bg-emerald-500 px-3 py-1 text-xs font-semibold text-zinc-900">
                        Most Popular
                    </span>
                @endif

                {{-- Plan Name --}}
                <h2 class="text-xl font-bold text-white">
                    {{ $plan['name'] }}
                </h2>

                {{-- Price --}}
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-4xl font-extrabold text-white">
                        ${{ number_format($plan['price'], 2) }}
                    </span>
                    <span class="text-sm text-zinc-400">
                        /month
                    </span>
                </div>

                {{-- Description --}}
                <p class="mt-3 text-sm text-zinc-400">
                    {{ $plan['description'] }}
                </p>

                {{-- Features --}}
                <ul class="mt-6 space-y-3 text-sm text-zinc-300">
                    <li>🧠 <strong>{{ $plan['ram'] }}</strong> RAM</li>
                    <li>⚡ <strong>{{ $plan['cpu'] }}</strong> CPU</li>
                    <li>💾 <strong>{{ $plan['storage'] }}</strong> SSD Storage</li>
                    <li>🌍 <strong>{{ $plan['slots'] }}</strong> Player Slots</li>
                    <li>📡 DDoS Protection</li>
                    <li>🔧 Full FTP & Console Access</li>
                </ul>

                {{-- CTA --}}
                <div class="mt-auto pt-6">
                    <a
                        href="{{ route('checkout.index', $plan['slug']) }}"
                        class="inline-flex w-full items-center justify-center rounded-xl
                               bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-900
                               transition hover:bg-emerald-400 focus:outline-none focus:ring-2
                               focus:ring-emerald-500/50">
                        Deploy Server
                    </a>
                </div>
            </div>
        @endforeach

    </section>

    {{-- Footer Note --}}
    <footer class="text-center text-sm text-zinc-500">
        All plans include instant setup, free migrations, and 24/7 uptime monitoring.
    </footer>

</div>
@endsection
