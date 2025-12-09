<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MassAssignmentSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that sensitive fields cannot be mass-assigned.
     */
    public function test_sensitive_fields_cannot_be_mass_assigned(): void
    {
        // Attempt to create a user with mass assignment including sensitive fields
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password_hash' => Hash::make('password'),
            'balance' => 999999999, // Attempt to set high balance
            'is_admin' => true,     // Attempt to set admin privilege
            'is_locked' => false,
            'google2fa_secret' => 'FAKESECRET123456',
            'google2fa_enabled' => true,
        ]);

        // Verify that only fillable fields were set
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('1234567890', $user->phone);

        // Verify that sensitive fields were NOT mass-assigned (use defaults)
        $this->assertEquals(0, $user->balance); // Default from migration
        $this->assertFalse($user->is_admin);    // Default from migration
        $this->assertFalse($user->is_locked);   // Default
        $this->assertNull($user->google2fa_secret);
        $this->assertFalse($user->google2fa_enabled);
    }

    /**
     * Test that balance cannot be updated via mass assignment.
     */
    public function test_balance_cannot_be_updated_via_mass_assignment(): void
    {
        $user = User::factory()->create([
            'balance' => 100000,
        ]);

        $originalBalance = $user->balance;

        // Attempt to update balance via mass assignment
        $user->update([
            'username' => 'updateduser',
            'balance' => 999999999, // Attempt to increase balance
        ]);

        $user->refresh();

        // Username should be updated (it's fillable)
        $this->assertEquals('updateduser', $user->username);

        // Balance should remain unchanged (it's guarded)
        $this->assertEquals($originalBalance, $user->balance);
    }

    /**
     * Test that is_admin cannot be updated via mass assignment.
     */
    public function test_is_admin_cannot_be_updated_via_mass_assignment(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        // Attempt to escalate privilege via mass assignment
        $user->update([
            'username' => 'hacker',
            'is_admin' => true, // Attempt privilege escalation
        ]);

        $user->refresh();

        // Username should be updated
        $this->assertEquals('hacker', $user->username);

        // is_admin should remain false (it's guarded)
        $this->assertFalse($user->is_admin);
    }

    /**
     * Test that 2FA fields cannot be manipulated via mass assignment.
     */
    public function test_2fa_fields_cannot_be_manipulated_via_mass_assignment(): void
    {
        $user = User::factory()->create([
            'google2fa_secret' => 'ORIGINALSECRET12345',
            'google2fa_enabled' => true,
        ]);

        // Attempt to disable 2FA via mass assignment
        $user->update([
            'username' => 'testuser',
            'google2fa_enabled' => false, // Attempt to disable 2FA
            'google2fa_secret' => 'NEWSECRET678910', // Attempt to change secret
        ]);

        $user->refresh();

        // Username should be updated
        $this->assertEquals('testuser', $user->username);

        // 2FA settings should remain unchanged (they're guarded)
        $this->assertTrue($user->google2fa_enabled);
        $this->assertEquals('ORIGINALSECRET12345', $user->google2fa_secret);
    }

    /**
     * Test that AdminController updateUser uses explicit assignment.
     */
    public function test_admin_update_user_uses_explicit_assignment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create([
            'balance' => 50000,
            'is_admin' => false,
        ]);

        $this->actingAs($admin);

        // Update user with both allowed and potentially malicious fields
        $response = $this->put(route('admin.users.update', $user->id), [
            'username' => 'updated_username',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'balance' => 100000,
            'is_admin' => 0,
            // These fields should be ignored even if sent:
            'google2fa_secret' => 'ATTACKERSECRET',
            'recovery_codes' => '["hack", "codes"]',
            'is_locked' => true,
        ]);

        $response->assertRedirect(route('admin.users'));

        $user->refresh();

        // Allowed fields should be updated
        $this->assertEquals('updated_username', $user->username);
        $this->assertEquals('updated@example.com', $user->email);
        $this->assertEquals('9876543210', $user->phone);
        $this->assertEquals(100000, $user->balance);
        $this->assertFalse($user->is_admin);

        // Sensitive fields should NOT be affected by extra data in request
        $this->assertNull($user->google2fa_secret);
        $this->assertNull($user->recovery_codes);
        $this->assertFalse($user->is_locked);
    }

    /**
     * Test that balance change is audit logged.
     */
    public function test_admin_balance_change_is_audit_logged(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['balance' => 50000]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $user->id), [
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'balance' => 100000, // Changed balance
            'is_admin' => 0,
        ]);

        $response->assertRedirect();

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_balance_change',
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);
    }

    /**
     * Test that admin privilege change is audit logged.
     */
    public function test_admin_privilege_change_is_audit_logged(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $user->id), [
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'balance' => $user->balance,
            'is_admin' => 1, // Changed to admin
        ]);

        $response->assertRedirect();

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_privilege_change',
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);
    }
}
