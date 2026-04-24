@extends('layouts.app')

@section('content')
    <section class="auth-shell">
        <div class="panel auth-hero auth-gate stack p-8 md:p-10 text-white">
            <div class="row">
                <span class="hero-logo-wrap"><img class="hero-logo" src="{{ Vite::asset('resources/asset/logo.jpg') }}" alt="RoomReserve logo"></span>
            </div>
            <div class="auth-gate-copy">
                <h1>Welcome back.</h1>
                <p>Enter the gate and continue your RoomReserve journey.</p>
            </div>
        </div>

        <form class="panel stack auth-form" method="POST" action="{{ route('login.store') }}">
            @csrf
            <h2 class="page-title">Welcome back</h2>
            <p class="page-subtitle">Sign in with your account credentials.</p>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label for="password">Password</label>
                <div class="row" style="align-items:stretch;">
                    <input id="password" name="password" type="password" required>
                    <button class="button" type="button" id="password_toggle">Peek</button>
                </div>
            </div>
            <div class="row" style="justify-content: space-between;">
                <label class="row" style="gap:0.5rem;margin:0;">
                    <input name="remember" type="checkbox" style="width:auto;">
                    Remember me
                </label>
                <a class="muted" href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <button class="button primary" type="submit">Log in</button>
        </form>
    </section>

    <script>
        const loginPassword = document.getElementById('password');
        const loginPasswordToggle = document.getElementById('password_toggle');

        loginPasswordToggle.addEventListener('click', function () {
            const shouldShow = loginPassword.type === 'password';
            loginPassword.type = shouldShow ? 'text' : 'password';
            loginPasswordToggle.textContent = shouldShow ? 'Hide' : 'Peek';
        });
    </script>
@endsection
