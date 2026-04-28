@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="hero-banner panel stack p-8 text-white">
            <div class="row">
                <span class="hero-logo-wrap"><img class="hero-logo" src="/assets/logo.jpg" alt="RoomReserve logo"></span>
                <span class="pill border-white/20 bg-white/15 text-white">Guest registration</span>
            </div>
            <div class="space-y-4 max-w-xl">
                <h1>Create your booking account in under a minute.</h1>
                <p class="text-white/85">Set up an account to browse rooms, confirm dates, and receive booking updates.</p>
            </div>
            <div class="trust-chips">
                <span class="trust-chip">No walk-in required</span>
                <span class="trust-chip">Secure password reset</span>
                <span class="trust-chip">Instant confirmation</span>
            </div>
        </div>

        <form class="panel stack" method="POST" action="{{ route('register.store') }}">
            @csrf
            <h2 class="page-title">Create account</h2>
            <p class="page-subtitle">Use your real contact details so confirmations reach you on time.</p>
            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <div class="row" style="align-items:stretch;">
                    <input id="password" name="password" type="password" required>
                    <button class="button" type="button" id="password_toggle">Peek</button>
                </div>
            </div>
            <div>
                <label for="password_confirmation">Confirm password</label>
                <div class="row" style="align-items:stretch;">
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                    <button class="button" type="button" id="password_confirmation_toggle">Peek</button>
                </div>
            </div>
            <button class="button primary" type="submit">Create account</button>
        </form>
    </section>

    <script>
        function wirePasswordToggle(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);

            button.addEventListener('click', function () {
                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.textContent = shouldShow ? 'Hide' : 'Peek';
            });
        }

        wirePasswordToggle('password', 'password_toggle');
        wirePasswordToggle('password_confirmation', 'password_confirmation_toggle');
    </script>
@endsection
