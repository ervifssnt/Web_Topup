<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_regular_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password_hash' => Hash::make('Password123!'),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('Password123!'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_middleware_redirects_non_admin_users(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        // Try to access various admin routes
        $adminRoutes = [
            '/admin/users',
            '/admin/games',
            '/admin/transactions',
            '/admin/audit-logs',
            '/admin/topup-requests',
            '/admin/password-reset-activity',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin');

        // Should redirect to login
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Users Management');
    }

    public function test_admin_can_access_game_management(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/games');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_audit_logs(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/audit-logs');

        $response->assertStatus(200);
        $response->assertSee('Audit Logs');
    }

    public function test_admin_can_access_password_reset_activity(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/password-reset-activity');

        $response->assertStatus(200);
        $response->assertSee('Password Reset Activity');
    }

    public function test_regular_user_gets_403_on_admin_post_actions(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        // Try to perform admin actions
        $response = $this->actingAs($user)
            ->post('/admin/games', [
                'name' => 'Test Game',
                'description' => 'Test',
                'image_url' => 'https://example.com/image.jpg',
            ]);

        $response->assertStatus(403);
    }
}
