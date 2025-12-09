<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordReset2FATest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users without 2FA enabled cannot use simple reset.
     */
    public function test_user_without_2fa_cannot_use_simple_reset(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'google2fa_enabled' => false,
        ]);

        $response = $this->post(route('password.reset.simple'), [
            'email' => $user->email,
            'username' => $user->username,
            'phone' => $user->phone,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        // Should be redirected back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        // Password should NOT be changed
        $user->refresh();
        $this->assertTrue(Hash::check('password', $user->password_hash)); // Still old password
    }

    /**
     * Test that users with 2FA enabled are redirected to 2FA verification.
     */
    public function test_user_with_2fa_redirected_to_verification(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        $response = $this->post(route('password.reset.simple'), [
            'email' => $user->email,
            'username' => $user->username,
            'phone' => $user->phone,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        // Should be redirected to 2FA verification
        $response->assertRedirect(route('password.reset.verify2fa'));

        // Session should have user ID and new password
        $this->assertTrue(session()->has('password_reset_user_id'));
        $this->assertTrue(session()->has('password_reset_new_password'));
        $this->assertTrue(session()->has('password_reset_initiated_at'));

        // Password should NOT be changed yet
        $user->refresh();
        $this->assertTrue(Hash::check('password', $user->password_hash));
    }

    /**
     * Test that 2FA verification form requires valid session.
     */
    public function test_2fa_verification_form_requires_session(): void
    {
        $response = $this->get(route('password.reset.verify2fa'));

        // Should be redirected back without valid session
        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('error');
    }

    /**
     * Test that 2FA verification form loads with valid session.
     */
    public function test_2fa_verification_form_loads_with_valid_session(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
        ]);

        // Set up session
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => 'NewPassword123!',
            'password_reset_initiated_at' => now(),
        ]);

        $response = $this->get(route('password.reset.verify2fa'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password-verify-2fa');
    }

    /**
     * Test that invalid 2FA code is rejected.
     */
    public function test_invalid_2fa_code_is_rejected(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => 'NewPassword123!',
            'password_reset_initiated_at' => now(),
        ]);

        $response = $this->post(route('password.reset.verify2fa.post'), [
            '2fa_code' => '000000', // Invalid code
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('2fa_code');

        // Password should NOT be changed
        $user->refresh();
        $this->assertTrue(Hash::check('password', $user->password_hash));
    }

    /**
     * Test that valid 2FA code completes password reset.
     */
    public function test_valid_2fa_code_completes_password_reset(): void
    {
        // Create Google2FA instance
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'username' => 'testuser',
            'google2fa_enabled' => true,
            'google2fa_secret' => $secret,
        ]);

        $newPassword = 'NewPassword123!';

        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => $newPassword,
            'password_reset_initiated_at' => now(),
        ]);

        // Generate valid 2FA code
        $validCode = $google2fa->getCurrentOtp($secret);

        $response = $this->post(route('password.reset.verify2fa.post'), [
            '2fa_code' => $validCode,
        ]);

        // Should redirect to login with success message
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Password should be changed
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password_hash));

        // Session should be cleared
        $this->assertFalse(session()->has('password_reset_user_id'));
        $this->assertFalse(session()->has('password_reset_new_password'));
        $this->assertFalse(session()->has('password_reset_initiated_at'));

        // Audit log should be created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'password_reset_simple',
        ]);
    }

    /**
     * Test that expired reset session is rejected.
     */
    public function test_expired_reset_session_is_rejected(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        // Set session with timestamp 11 minutes ago (expired)
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => 'NewPassword123!',
            'password_reset_initiated_at' => now()->subMinutes(11),
        ]);

        $response = $this->post(route('password.reset.verify2fa.post'), [
            '2fa_code' => '123456',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('error');

        // Session should be cleared
        $this->assertFalse(session()->has('password_reset_user_id'));
    }

    /**
     * Test that simple reset is rate limited.
     */
    public function test_simple_reset_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'google2fa_enabled' => true,
        ]);

        // Send 6 requests (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('password.reset.simple'), [
                'email' => $user->email,
                'username' => $user->username,
                'phone' => $user->phone,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

            if ($i < 5) {
                $response->assertRedirect(route('password.reset.verify2fa'));
            } else {
                // 6th request should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /**
     * Test that 2FA verification is rate limited.
     */
    public function test_2fa_verification_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => 'NewPassword123!',
            'password_reset_initiated_at' => now(),
        ]);

        // Send 6 verification attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('password.reset.verify2fa.post'), [
                '2fa_code' => '000000',
            ]);

            if ($i < 5) {
                $response->assertRedirect();
            } else {
                // 6th request should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /**
     * Test that incorrect user info returns generic error.
     */
    public function test_incorrect_user_info_returns_generic_error(): void
    {
        $response = $this->post(route('password.reset.simple'), [
            'email' => 'nonexistent@example.com',
            'username' => 'nonexistent',
            'phone' => '9999999999',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('does not match', session('errors')->first('email'));
    }
}
