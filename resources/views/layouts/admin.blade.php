{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @hasSection('title')
            @yield('title') · {{ config('app.name', 'Rado Hosting') }}
        @else
            {{ config('app.name', 'Rado Hosting') }} Admin
        @endif
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-950 text-zinc-50 antialiased min-h-screen flex flex-col">

    {{-- Top Header --}}
    <header class="border-b border-zinc-800 bg-zinc-900/70 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            {{-- Logo / Brand --}}
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-xl bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-center">
                    <span class="text-emerald-400 text-xl font-black">R</span>
                </div>
                <div class="leading-tight">
                    <div class="text-sm uppercase tracking-[0.15em] text-zinc-400">Admin</div>
                    <div class="text-lg font-extrabold">
                        <span class="text-emerald-400">Rado</span>
                        <span class="text-zinc-50">Hosting</span>
                    </div>
                </div>
            </a>

            {{-- Right side: user info / actions --}}
            <div class="flex items-center gap-3">

                {{-- Environment pill (optional) --}}
                @env('local')
                    <span class="hidden sm:inline-flex items-center rounded-full border border-amber-500/40 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-300">
                        Local Dev
                    </span>
                @endenv

                {{-- User info & actions --}}
                @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col items-end leading-tight">
                            <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-zinc-400">Admin Panel</span>
                        </div>

                        {{-- Back to main site (desktop) --}}
                        <a
                            href="{{ url('/') }}"
                            class="hidden sm:inline-flex items-center text-xs font-medium border border-zinc-700/80 bg-zinc-900/80 px-3 py-1.5 rounded-lg hover:border-emerald-500/70 hover:text-emerald-300 transition"
                        >
                            ← Back to main site
                        </a>

                        {{-- Logout --}}
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="hidden sm:block"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="text-xs font-medium border border-zinc-700/80 bg-zinc-900/80 px-3 py-1.5 rounded-lg hover:border-red-500/70 hover:text-red-300 transition"
                            >
                                Log out
                            </button>
                        </form>
                    </div>
                @endauth

            </div>
        </div>
    </header>

    <div class="flex-1 flex">

        {{-- Sidebar (desktop) --}}
        <aside class="hidden lg:flex lg:flex-col w-64 border-r border-zinc-800 bg-zinc-950/80">
            <nav class="flex-1 py-4 space-y-1 text-sm">
                <div class="px-4 text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-2">
                    Overview
                </div>

                {{-- Dashboard --}}
                <a href="{{ route('admin.index') }}"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 group-hover:bg-emerald-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Dashboard</span>
                        <span class="text-[0.7rem] text-zinc-400">Cluster overview & stats</span>
                    </span>
                </a>

                {{-- Servers --}}
                <a href="{{ route('admin.minecraft.index') }}"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 border border-zinc-700/70 text-zinc-300 group-hover:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Servers</span>
                        <span class="text-[0.7rem] text-zinc-400">Game & web instances</span>
                    </span>
                </a>

                {{-- Plans --}}
                <a href="#"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 border border-zinc-700/70 text-zinc-300 group-hover:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M12 8c-1.657 0-3 .843-3 1.875v4.25C9 15.157 10.343 16 12 16s3-.843 3-1.875v-4.25C15 8.843 13.657 8 12 8z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M6 10.5V9a6 6 0 1112 0v1.5"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Plans</span>
                        <span class="text-[0.7rem] text-zinc-400">Pricing tiers & resources</span>
                    </span>
                </a>

                {{-- Users --}}
                <a href="#"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 border border-zinc-700/70 text-zinc-300 group-hover:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M6 20c0-2.21 2.686-4 6-4s6 1.79 6 4"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Users</span>
                        <span class="text-[0.7rem] text-zinc-400">Accounts & permissions</span>
                    </span>
                </a>

                {{-- Tickets --}}
                <a href="#"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 border border-zinc-700/70 text-zinc-300 group-hover:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M9 5h6a2 2 0 012 2v10l-5-2-5 2V7a2 2 0 012-2z"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Tickets</span>
                        <span class="text-[0.7rem] text-zinc-400">Support queue</span>
                    </span>
                </a>

                {{-- Settings --}}
                <a href="#"
                   class="group flex items-center gap-2 px-4 py-2.5 border-l-2 border-transparent hover:border-emerald-500/80 hover:bg-zinc-900/80 transition">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 border border-zinc-700/70 text-zinc-300 group-hover:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M11.983 4.082a1 1 0 011.034 0l1.518.94a1 1 0 00.894.06l1.618-.647a1 1 0 011.285.548l.69 1.614a1 1 0 00.727.59l1.73.32a1 1 0 01.806.986v1.89a1 1 0 01-.806.986l-1.73.32a1 1 0 00-.727.59l-.69 1.614a1 1 0 01-1.285.548l-1.618-.647a1 1 0 00-.894.06l-1.518.94a1 1 0 01-1.034 0l-1.518-.94a1 1 0 00-.894-.06l-1.618.647a1 1 0 01-1.285-.548l-.69-1.614a1 1 0 00-.727-.59l-1.73-.32A1 1 0 013 12.485v-1.89a1 1 0 01.806-.986l1.73-.32a1 1 0 00.727-.59l.69-1.614A1 1 0 018.238 6.08l1.618.647a1 1 0 00.894-.06l1.518-.94z"/>
                            <circle cx="12" cy="11.54" r="2.5" />
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-medium">Settings</span>
                        <span class="text-[0.7rem] text-zinc-400">Platform config</span>
                    </span>
                </a>
            </nav>

            {{-- Footer --}}
            <div class="px-4 py-3 text-[0.7rem] text-zinc-500 border-t border-zinc-800">
                <div>© {{ now()->year }} Rado Hosting</div>
                <div class="text-zinc-600">Admin control panel</div>
            </div>
        </aside>

        {{-- Mobile nav (details/summary, no JS needed) --}}
        <div class="lg:hidden border-b border-zinc-800 bg-zinc-950/95">
            <details class="group">
                <summary class="flex items-center justify-between px-4 py-2 text-sm cursor-pointer">
                    <span class="text-zinc-300">Admin Navigation</span>
                    <span class="group-open:rotate-180 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </summary>
                <nav class="pb-2 text-sm">
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Dashboard</a>
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Servers</a>
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Plans</a>
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Users</a>
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Tickets</a>
                    <a href="#" class="block px-4 py-2 hover:bg-zinc-900">Settings</a>
                    @auth
                        <a href="{{ url('/') }}" class="block px-4 py-2 text-emerald-300 hover:bg-zinc-900">
                            ← Back to main site
                        </a>
                    @endauth
                    <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left text-red-300 hover:bg-zinc-900 text-sm"
                        >
                            Log out
                        </button>
                    </form>
                </nav>
            </details>
        </div>

        {{-- Main content --}}
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
