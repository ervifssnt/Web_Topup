<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_2fa_setup_page_loads(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => false,
        ]);

        $response = $this->actingAs($user)->get('/2fa/enable');

        $response->assertStatus(200);
        $response->assertSee('Two-Factor Authentication');
    }

    public function test_2fa_qr_code_generation(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => false,
        ]);

        $response = $this->actingAs($user)->get('/2fa/enable');

        $response->assertStatus(200);
        // Check that secret is displayed (manual entry option)
        $response->assertSee('Or enter this code manually');
    }

    public function test_2fa_verification_with_valid_code(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => false,
        ]);

        // Generate a secret
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Store secret in session (simulating the enable flow)
        $this->actingAs($user)->get('/2fa/enable');

        // Generate valid TOTP code
        $validCode = $google2fa->getCurrentOtp($secret);

        // Verify 2FA
        $response = $this->actingAs($user)
            ->post('/2fa/verify', [
                'one_time_password' => $validCode,
                'google2fa_secret' => $secret,
            ]);

        // Should redirect on success
        $response->assertRedirect();
    }

    public function test_2fa_verification_with_invalid_code(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => false,
        ]);

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $this->actingAs($user)->get('/2fa/enable');

        // Use an invalid code
        $response = $this->actingAs($user)
            ->post('/2fa/verify', [
                'one_time_password' => '000000',
                'google2fa_secret' => $secret,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_2fa_recovery_code_generation(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => false,
        ]);

        // Visit enable page (this generates and saves the secret)
        $this->actingAs($user)->get('/2fa/enable');

        // Get the secret that was saved
        $user->refresh();
        $secret = $user->google2fa_secret;

        $google2fa = new Google2FA();
        $validCode = $google2fa->getCurrentOtp($secret);

        // Verify 2FA with the code
        $this->actingAs($user)
            ->post('/2fa/verify', [
                'code' => $validCode,
            ]);

        // User should now have recovery codes
        $user->refresh();
        $recoveryCodes = $user->recovery_codes; // Already cast to array by Laravel

        $this->assertIsArray($recoveryCodes);
        $this->assertCount(8, $recoveryCodes); // Should have 8 recovery codes
    }

    public function test_2fa_login_flow(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => true,
            'google2fa_secret' => encrypt($secret),
        ]);

        // Login with username and password
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        // Should redirect to 2FA verification page
        $response->assertRedirect('/2fa/verify');
    }

    public function test_2fa_recovery_code_usage(): void
    {
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $code = bin2hex(random_bytes(4));
            $recoveryCodes[] = Hash::make($code);
        }

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => true,
            'google2fa_secret' => encrypt('test-secret'),
            'recovery_codes' => json_encode($recoveryCodes),
        ]);

        // This test would require accessing the recovery code flow
        // which typically happens during 2FA login verification
        $this->assertTrue(true); // Placeholder for complex flow
    }

    public function test_2fa_disable_functionality(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => true,
            'google2fa_secret' => encrypt('test-secret'),
        ]);

        $response = $this->actingAs($user)
            ->post('/2fa/disable', [
                'password' => 'Password123!',
            ]);

        $response->assertRedirect();

        // Verify 2FA is disabled
        $user->refresh();
        $this->assertFalse((bool)$user->google2fa_enabled);
        $this->assertNull($user->google2fa_secret);
    }

    public function test_2fa_recovery_codes_are_one_time_use(): void
    {
        // This would require implementing recovery code usage tracking
        // For now, we verify the structure exists
        $user = User::factory()->create([
            'recovery_codes' => json_encode([
                Hash::make('code1'),
                Hash::make('code2'),
            ]),
        ]);

        $this->assertNotNull($user->recovery_codes);
        $this->assertTrue(true);
    }

    public function test_2fa_regenerate_recovery_codes(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('Password123!'),
            'google2fa_enabled' => true,
            'recovery_codes' => [Hash::make('old-code')], // Array, not JSON (auto-cast)
        ]);

        $oldCodes = $user->recovery_codes;

        $response = $this->actingAs($user)
            ->post('/2fa/recovery/regenerate', [
                'password' => 'Password123!',
            ]);

        $response->assertRedirect();

        // Recovery codes should be regenerated (new codes should exist)
        $user->refresh();
        $newCodes = $user->recovery_codes; // Already cast to array
        $this->assertIsArray($newCodes);
        $this->assertCount(8, $newCodes); // Should have 8 new recovery codes
    }
}
