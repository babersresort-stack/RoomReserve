<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'RoomReserve') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <header x-data="{ menuOpen: false }" class="topbar topbar-surface">
            <div class="shell flex w-full items-center justify-between gap-4">
                <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('home') }}" class="brand">
                    <img class="brand-logo" src="{{ Vite::asset('resources/asset/logo.jpg') }}" alt="RoomReserve logo">
                    <span>
                        <strong>RoomReserve</strong>
                        Resort booking and room operations
                    </span>
                </a>

                <nav class="nav hidden md:flex">
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a class="chip {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="chip {{ request()->routeIs('admin.rooms.*') ? 'is-active' : '' }}" href="{{ route('admin.rooms.index') }}">Rooms</a>
                            <a class="chip {{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}" href="{{ route('admin.bookings.index') }}">Reservations</a>
                            <a class="chip {{ request()->routeIs('admin.feedback.*') ? 'is-active' : '' }}" href="{{ route('admin.feedback.index') }}">Feedback</a>
                        @else
                            <a class="chip {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Home</a>
                            <a class="chip {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            <a class="chip {{ request()->routeIs('rooms.*') ? 'is-active' : '' }}" href="{{ route('rooms.index') }}">Rooms</a>
                            <a class="chip {{ request()->routeIs('bookings.*') ? 'is-active' : '' }}" href="{{ route('bookings.index') }}">Bookings</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button danger" type="submit">Log out</button>
                        </form>
                    @else
                        <a class="chip" href="{{ route('login') }}">Sign in</a>
                        <a class="button primary" href="{{ route('register') }}">Register</a>
                    @endauth
                </nav>

                <button type="button" class="button secondary mobile-menu md:hidden" @click="menuOpen = !menuOpen">
                    Menu
                </button>
            </div>

            <div x-cloak x-show="menuOpen" x-transition class="shell mt-3 md:hidden">
                <div class="panel stack">
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" href="{{ route('admin.rooms.index') }}">Rooms</a>
                            <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">Reservations</a>
                            <a class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}" href="{{ route('admin.feedback.index') }}">Feedback</a>
                        @else
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Rooms</a>
                            <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">Bookings</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button danger w-full" type="submit">Log out</button>
                        </form>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                        <a class="button primary w-full" href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="shell pb-10 pt-6 md:pt-8">
            @if (session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error">
                    <strong>Please fix the following:</strong>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
