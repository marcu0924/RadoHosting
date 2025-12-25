<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Rado Hosting'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-950 text-white antialiased min-h-screen flex flex-col">

    {{-- Top Navigation --}}
    <header class="w-full border-b border-zinc-800 bg-zinc-900/50 backdrop-blur-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            {{-- Logo --}}
            <a href="/" class="text-2xl font-extrabold tracking-wide">
                <span class="text-emerald-400">Rado</span>
                <span class="text-white">Hosting</span>
            </a>

            {{-- Mobile Pricing --}}
            <div class="md:hidden">
                <details class="relative">
                    <summary class="cursor-pointer text-sm text-zinc-300 hover:text-emerald-400">
                        Pricing
                    </summary>
                    <div class="absolute left-0 top-full w-56 rounded-xl border border-zinc-800 bg-zinc-900 shadow-xl">
                        <a href="{{ route('pricing.minecraft') }}"
                           class="block px-4 py-3 text-sm hover:bg-zinc-800 hover:text-emerald-400">
                            Minecraft Hosting
                        </a>
                        <span class="block px-4 py-3 text-sm text-zinc-500">
                            Web Hosting (Coming Soon)
                        </span>
                    </div>
                </details>
            </div>

            {{-- Desktop Navigation --}}
            <nav class="flex items-center space-x-6">

                {{-- Public Links --}}
                <div class="hidden md:flex items-center space-x-6 text-sm text-zinc-300">

                    {{-- Pricing Dropdown (FIXED) --}}
                    <div class="relative group">

                        <button
                            class="inline-flex items-center gap-1 hover:text-emerald-400 transition">
                            Pricing
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            class="absolute left-0 top-full w-56 rounded-xl border border-zinc-800
                                   bg-zinc-900 shadow-xl
                                   opacity-0 invisible
                                   group-hover:opacity-100 group-hover:visible
                                   transition">

                            <a href="{{ route('pricing.minecraft') }}"
                               class="block px-4 py-3 text-sm hover:bg-zinc-800 hover:text-emerald-400">
                                Minecraft Hosting
                            </a>

                            <span class="block px-4 py-3 text-sm text-zinc-500 cursor-not-allowed">
                                Web Hosting (Coming Soon)
                            </span>
                        </div>
                    </div>

                    <a href="#" class="hover:text-emerald-400 transition">Status</a>
                    <a href="#" class="hover:text-emerald-400 transition">Docs</a>
                </div>

                {{-- Auth / Profile --}}
                <div class="flex items-center space-x-4">

                    @auth

                        {{-- Admin Panel --}}
                        @if (auth()->user()->role === 'admin')
                            <a href="/admin"
                               class="px-4 py-2 rounded-lg border border-emerald-500 text-emerald-400 text-sm
                                      hover:bg-emerald-500 hover:text-black transition">
                                Admin Panel
                            </a>
                        @endif

                        {{-- Profile Dropdown (FIXED) --}}
                        <div class="relative group">

                            <button
                                class="flex items-center gap-2 rounded-lg bg-zinc-800 px-3 py-2 text-sm
                                       hover:bg-zinc-700 transition">

                                <span class="flex h-7 w-7 items-center justify-center rounded-full
                                             bg-emerald-500 text-xs font-bold text-zinc-900">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>

                                <span class="hidden sm:inline text-zinc-200">
                                    {{ auth()->user()->name }}
                                </span>

                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                class="absolute right-0 top-full w-56 rounded-xl border border-zinc-800
                                       bg-zinc-900 shadow-xl
                                       opacity-0 invisible
                                       group-hover:opacity-100 group-hover:visible
                                       transition">

                                <a href="{{ route('profile.show') }}"
                                   class="block px-4 py-3 text-sm hover:bg-zinc-800 hover:text-emerald-400">
                                    Profile
                                </a>

                                @auth
                                <a href="{{ route('servers.index') }}"
                                class="block px-4 py-3 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    My Servers
                                </a>
                                @endauth

                                <span class="block px-4 py-3 text-sm text-zinc-500 cursor-not-allowed">
                                    Billing (Coming Soon)
                                </span>

                                <div class="border-t border-zinc-800"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-zinc-800">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-sm">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm">
                            Register
                        </a>
                    @endguest

                </div>
            </nav>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full border-t border-zinc-800 py-6 text-center text-zinc-500 text-sm">
        © {{ date('Y') }} Rado Hosting — All Rights Reserved.
    </footer>

</body>
</html>
