@extends('layouts.app')

@section('content')
    <section class="section grid cols-2">
        <div class="panel stack">
            <span class="pill">Set new password</span>
            <h1>Choose a new password</h1>
            <p class="muted">Use the 6-digit code from your email to complete the password update.</p>
        </div>
        <form class="panel stack" method="POST" action="{{ route('password.update') }}">
            @csrf
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autofocus>
            </div>
            <div>
                <label for="code">Reset code</label>
                <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" value="{{ old('code') }}" required>
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
            <button class="button primary" type="submit">Reset password</button>
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
