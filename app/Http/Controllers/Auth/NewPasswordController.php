<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $resetToken) {
            return back()->withInput($request->only('email'))->withErrors([
                'code' => 'Invalid or expired reset code.',
            ]);
        }

        $createdAt = $resetToken->created_at ? Carbon::parse($resetToken->created_at) : null;
        $isExpired = ! $createdAt || $createdAt->addMinutes(15)->isPast();

        if ($isExpired || ! Hash::check($validated['code'], $resetToken->token)) {
            return back()->withInput($request->only('email'))->withErrors([
                'code' => 'Invalid or expired reset code.',
            ]);
        }

        $user = \App\Models\User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'We can\'t find a user with that email address.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password reset successful. You can now sign in.');
    }
}
