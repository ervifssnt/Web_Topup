<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SensitiveDataLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that password reset tokens are NOT logged in plaintext.
     */
    public function test_password_reset_token_not_logged_in_plaintext(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Mock the Log facade to capture log calls
        Log::shouldReceive('info')
            ->never()
            ->with(\Mockery::pattern('/token/i'));

        Log::shouldReceive('info')
            ->andReturn(null);

        // Request password reset
        $response = $this->post('/password/email', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify token was created in database but not logged
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test that passwords are NOT logged in plaintext.
     */
    public function test_passwords_not_logged_in_plaintext(): void
    {
        // Mock the Log facade to capture log calls
        Log::shouldReceive('info')
            ->never()
            ->with(\Mockery::pattern('/password.*[A-Za-z0-9!@#$%^&*()]{8,}/'));

        Log::shouldReceive('info')
            ->andReturn(null);

        // Attempt registration with password
        $response = $this->post('/register', [
            'username' => 'testuser',
            'phone' => '1234567890',
            'email' => 'newuser@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        // Should succeed without logging the password
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    /**
     * Test that 2FA secrets are NOT logged.
     */
    public function test_2fa_secrets_not_logged(): void
    {
        $user = User::factory()->create();

        // Mock the Log facade
        Log::shouldReceive('info')
            ->never()
            ->with(\Mockery::pattern('/google2fa_secret/i'));

        Log::shouldReceive('info')
            ->andReturn(null);

        // Login and enable 2FA
        $this->actingAs($user);

        $response = $this->post('/2fa/enable');

        $response->assertRedirect();

        // Verify 2FA was enabled but secret not logged
        $user->refresh();
        $this->assertNotNull($user->google2fa_secret);
    }

    /**
     * Test that audit logs do NOT contain sensitive data.
     */
    public function test_audit_logs_do_not_contain_sensitive_data(): void
    {
        $user = User::factory()->create([
            'email' => 'audit@example.com',
        ]);

        // Request password reset which creates audit log
        $this->post('/password/email', [
            'email' => 'audit@example.com',
        ]);

        // Check audit logs table
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'password_reset_link_requested',
        ]);

        // Verify audit log description doesn't contain token
        $auditLog = \DB::table('audit_logs')
            ->where('action', 'password_reset_link_requested')
            ->latest('id')
            ->first();

        $this->assertStringNotContainsString('token', strtolower($auditLog->description ?? ''));
    }

    /**
     * Test that session data doesn't leak sensitive information in logs.
     */
    public function test_session_data_not_logged(): void
    {
        $user = User::factory()->create();

        // Mock Log to ensure session IDs and sensitive session data aren't logged
        Log::shouldReceive('info')
            ->never()
            ->with(\Mockery::pattern('/session.*id/i'));

        Log::shouldReceive('info')
            ->andReturn(null);

        // Login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password', // Default factory password
        ]);

        // Should not log session data
        $this->assertTrue($response->isRedirection());
    }
}
