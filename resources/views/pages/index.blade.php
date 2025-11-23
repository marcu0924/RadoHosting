@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-16">

    {{-- Hero Section --}}
    <div class="text-center mb-16">
        <h1 class="text-5xl font-extrabold tracking-tight">
            <span class="text-emerald-400">Game Servers</span>
            <span class="text-white">Made Simple</span>
        </h1>
        <p class="mt-4 text-lg text-zinc-400 max-w-2xl mx-auto">
            Rent blazing-fast game servers powered by our optimized hosting network.
            Launch, manage, and customize with ease — starting with Minecraft.
        </p>
    </div>

    {{-- Minecraft Section --}}
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-6">
            Minecraft Servers
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- 4 GB Plan --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-lg hover:border-emerald-500 transition hover:cursor-pointer">
                <h3 class="text-xl font-semibold mb-2">4 GB Plan</h3>
                <p class="text-zinc-400 mb-6">Great for small groups and basic modpacks.</p>

                <ul class="text-zinc-300 text-sm space-y-2 mb-6">
                    <li>✔ Up to ~10 players</li>
                    <li>✔ Basic plugins</li>
                    <li>✔ Lag-free performance</li>
                </ul>

                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-semibold">
                    Rent 4 GB Server
                </button>
            </div>

            {{-- 8 GB Plan --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-lg hover:border-emerald-500 transition hover:cursor-pointer">
                <h3 class="text-xl font-semibold mb-2">8 GB Plan</h3>
                <p class="text-zinc-400 mb-6">Perfect for medium communities and heavier modpacks.</p>

                <ul class="text-zinc-300 text-sm space-y-2 mb-6">
                    <li>✔ Up to ~20 players</li>
                    <li>✔ Modpacks & plugins</li>
                    <li>✔ Strong performance</li>
                </ul>

                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-semibold">
                    Rent 8 GB Server
                </button>
            </div>

            {{-- 16 GB Plan --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-lg hover:border-emerald-500 transition hover:cursor-pointer">
                <h3 class="text-xl font-semibold mb-2">16 GB Plan</h3>
                <p class="text-zinc-400 mb-6">Best for large servers, modded packs, and heavy plugins.</p>

                <ul class="text-zinc-300 text-sm space-y-2 mb-6">
                    <li>✔ Up to ~50 players</li>
                    <li>✔ Heavily modded</li>
                    <li>✔ Premium performance</li>
                </ul>

                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-semibold">
                    Rent 16 GB Server
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
