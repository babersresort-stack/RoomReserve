@extends('layouts.app')

@section('content')
    <section class="section grid cols-2">
        <div class="panel stack">
            <span class="pill">Password recovery</span>
            <h1>Reset access quickly</h1>
            <p class="muted">Enter your account email and we will send a 6-digit reset code.</p>
        </div>
        <form class="panel stack" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>
            <button class="button primary" type="submit">Send reset code</button>
        </form>
    </section>
@endsection
