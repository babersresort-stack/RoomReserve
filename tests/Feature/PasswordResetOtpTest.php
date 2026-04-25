<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_otp_and_stores_hashed_code(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect(route('password.reset', ['email' => $user->email]));

        $tokenRow = DB::table('password_reset_tokens')->where('email', $user->email)->first();

        $this->assertNotNull($tokenRow);
        $this->assertNotSame('', (string) $tokenRow->token);
        $this->assertFalse(preg_match('/^\d{6}$/', (string) $tokenRow->token) === 1);

        Notification::assertSentTo($user, PasswordResetCodeNotification::class);
    }

    public function test_user_can_reset_password_with_valid_otp(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make('123456'),
                'created_at' => now(),
            ]
        );

        $this->post(route('password.update'), [
            'email' => $user->email,
            'code' => '123456',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_user_cannot_reset_password_with_expired_otp(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make('123456'),
                'created_at' => now()->subMinutes(16),
            ]
        );

        $this->from(route('password.reset'))
            ->post(route('password.update'), [
                'email' => $user->email,
                'code' => '123456',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHasErrors(['code']);

        $user->refresh();

        $this->assertTrue(Hash::check('old-password-123', $user->password));
    }
}
