<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Mandatory2FARegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration generates 2FA secret and redirects to mandatory setup.
     */
    public function test_registration_creates_user_with_2fa_secret_and_redirects_to_setup(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Should redirect to mandatory 2FA setup
        $response->assertRedirect(route('2fa.setup.required'));
        $response->assertSessionHas('success');

        // User should be created
        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
        ]);

        // User should have 2FA secret generated but not enabled yet
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user->google2fa_secret);
        $this->assertFalse($user->google2fa_enabled);

        // User should be authenticated
        $this->assertAuthenticated();
    }

    /**
     * Test that mandatory 2FA setup page loads for new users.
     */
    public function test_mandatory_2fa_setup_page_loads(): void
    {
        // Create user with 2FA secret but not enabled
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'google2fa_secret' => $secret,
            'google2fa_enabled' => false,
        ]);

        $response = $this->actingAs($user)->get(route('2fa.setup.required'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.two-factor-mandatory');
        $response->assertViewHas('qrCodeSvg');
        $response->assertViewHas('secret', $secret);
    }

    /**
     * Test that users with 2FA already enabled are redirected to home.
     */
    public function test_users_with_2fa_enabled_redirected_to_home(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        $response = $this->actingAs($user)->get(route('2fa.setup.required'));

        $response->assertRedirect(route('home'));
    }

    /**
     * Test that valid 2FA code completes mandatory setup.
     */
    public function test_valid_2fa_code_completes_mandatory_setup(): void
    {
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'username' => 'testuser',
            'google2fa_secret' => $secret,
            'google2fa_enabled' => false,
        ]);

        // Generate valid code
        $validCode = $google2fa->getCurrentOtp($secret);

        $response = $this->actingAs($user)->post(route('2fa.setup.verify'), [
            'code' => $validCode,
        ]);

        // Should show recovery codes page
        $response->assertStatus(200);
        $response->assertViewIs('auth.two-factor-recovery-codes');
        $response->assertViewHas('recoveryCodes');

        // User should now have 2FA enabled
        $user->refresh();
        $this->assertTrue($user->google2fa_enabled);
        $this->assertNotNull($user->recovery_codes);

        // Audit log should be created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => '2fa_enabled',
        ]);
    }

    /**
     * Test that invalid 2FA code is rejected.
     */
    public function test_invalid_2fa_code_rejected_during_mandatory_setup(): void
    {
        $user = User::factory()->create([
            'google2fa_secret' => 'TESTSECRET123456',
            'google2fa_enabled' => false,
        ]);

        $response = $this->actingAs($user)->post(route('2fa.setup.verify'), [
            'code' => '000000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('code');

        // User should NOT have 2FA enabled
        $user->refresh();
        $this->assertFalse($user->google2fa_enabled);
    }

    /**
     * Test that users without 2FA setup cannot access protected routes.
     */
    public function test_users_without_2fa_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'google2fa_secret' => 'TESTSECRET123456',
            'google2fa_enabled' => false,
        ]);

        // Try to access profile (protected route)
        $response = $this->actingAs($user)->get(route('profile.dashboard'));

        // Should be redirected to mandatory 2FA setup
        $response->assertRedirect(route('2fa.setup.required'));
        $response->assertSessionHas('warning');
    }

    /**
     * Test that users without 2FA can logout.
     */
    public function test_users_without_2fa_can_logout(): void
    {
        $user = User::factory()->create([
            'google2fa_secret' => 'TESTSECRET123456',
            'google2fa_enabled' => false,
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Test that users with 2FA can access protected routes.
     */
    public function test_users_with_2fa_can_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRET123456',
        ]);

        $response = $this->actingAs($user)->get(route('profile.dashboard'));

        $response->assertStatus(200);
    }
}
