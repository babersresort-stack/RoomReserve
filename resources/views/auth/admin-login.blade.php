@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="hero-banner panel stack p-8 text-white">
            <div class="row">
                <span class="hero-logo-wrap"><img class="hero-logo" src="/assets/logo.jpg" alt="RoomReserve logo"></span>
                <span class="pill border-white/20 bg-white/15 text-white">Admin portal</span>
            </div>
            <div class="space-y-4 max-w-xl">
                <h1>Manage rooms, reservations, and feedback from one control center.</h1>
                <p class="text-white/85">Designed for staff oversight with clear status visibility, room photo support, and quick actions.</p>
            </div>
            <div class="trust-chips">
                <span class="trust-chip">Room operations</span>
                <span class="trust-chip">Live reservations</span>
                <span class="trust-chip">Feedback moderation</span>
            </div>
        </div>

        <form class="panel stack" method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <h2 class="page-title">Admin sign in</h2>
            <p class="page-subtitle">Access the backend to manage inventory and reservations.</p>
            <div>
                <label for="admin-email">Email</label>
                <input id="admin-email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label for="admin-password">Password</label>
                <input id="admin-password" name="password" type="password" required>
            </div>
            <div class="row" style="justify-content: space-between;">
                <label class="row" style="gap:0.5rem;margin:0;">
                    <input name="remember" type="checkbox" style="width:auto;">
                    Remember me
                </label>
                <a class="muted" href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <button class="button primary" type="submit">Log in as admin</button>
        </form>
    </section>
@endsection
