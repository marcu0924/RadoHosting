<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Rado Hosting') }}</title>

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

            {{-- Auth Buttons --}}
            <nav class="flex items-center space-x-4">

                @auth
                    {{-- Admin Panel Link --}}
                    @if (auth()->user()->role === 'admin')
                        <a href="/admin"
                           class="px-4 py-2 rounded-lg border border-emerald-500 text-emerald-400 text-sm hover:bg-emerald-500 hover:text-black transition">
                            Admin Panel
                        </a>
                    @endif

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-sm">
                            Logout
                        </button>
                    </form>
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

            </nav>
        </div>
    </header>

    {{-- Admin Navigation --}}
    @auth
        @if (auth()->user()->role === 'admin')
            <nav class="w-full bg-zinc-900/40 border-b border-zinc-800">

                <div class="max-w-7xl mx-auto px-4 py-3 space-y-2">

                    {{-- Mobile: Collapsible admin menu --}}
                    <div class="md:hidden">
                        <details class="bg-zinc-900/70 border border-zinc-800 rounded-xl overflow-hidden">
                            <summary class="flex items-center justify-between px-4 py-2 cursor-pointer select-none">
                                <span class="text-sm font-medium text-zinc-200">
                                    Admin Menu
                                </span>
                                <span class="text-xs text-zinc-400">
                                    Tap to expand
                                </span>
                            </summary>

                            <div class="border-t border-zinc-800 flex flex-col">
                                <a href="/admin"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Dashboard
                                </a>
                                <a href="/admin/users"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Manage Users
                                </a>
                                <a href="/admin/tickets"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Tickets
                                </a>
                                <a href="/admin/servers"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Server Manager
                                </a>
                                <a href="/admin/gameservers"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Game Servers
                                </a>
                                <a href="/admin/logs"
                                class="px-4 py-2 text-sm hover:bg-zinc-800 hover:text-emerald-400 transition">
                                    Logs
                                </a>
                            </div>
                        </details>
                    </div>

                    {{-- Desktop: Horizontal admin nav --}}
                    <div class="hidden md:flex md:items-center md:space-x-6 text-sm">
                        <a href="/admin"
                        class="hover:text-emerald-400 transition">
                            Dashboard
                        </a>
                        <a href="/admin/users"
                        class="hover:text-emerald-400 transition">
                            Manage Users
                        </a>
                        <a href="/admin/tickets"
                        class="hover:text-emerald-400 transition">
                            Tickets
                        </a>
                        <a href="/admin/servers"
                        class="hover:text-emerald-400 transition">
                            Server Manager
                        </a>
                        <a href="/admin/gameservers"
                        class="hover:text-emerald-400 transition">
                            Game Servers
                        </a>
                        <a href="/admin/logs"
                        class="hover:text-emerald-400 transition">
                            Logs
                        </a>
                    </div>

                </div>
            </nav>
        @endif
    @endauth

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
