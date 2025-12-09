<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_registration_page_loads(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Register');
    }

    public function test_registration_with_valid_data_succeeds(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_registration_without_email_fails(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');

        // Verify user was not created
        $this->assertDatabaseMissing('users', [
            'username' => 'newuser',
        ]);
    }

    public function test_registration_with_weak_password_fails(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_with_duplicate_email_fails(): void
    {
        // Create existing user
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_username_validation_alphanumeric_and_underscore_only(): void
    {
        // Valid username with alphanumeric and underscore
        $response1 = $this->post('/register', [
            'username' => 'valid_user123',
            'email' => 'valid@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response1->assertRedirect('/');

        // Invalid username with special characters
        $response2 = $this->post('/register', [
            'username' => 'invalid@user!',
            'email' => 'invalid@example.com',
            'phone' => '0987654321',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response2->assertSessionHasErrors('username');
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_phone_number_validation(): void
    {
        // Valid phone number (10-15 digits)
        $response1 = $this->post('/register', [
            'username' => 'user1',
            'email' => 'user1@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response1->assertRedirect('/');

        // Invalid phone number (too short)
        $response2 = $this->post('/register', [
            'username' => 'user2',
            'email' => 'user2@example.com',
            'phone' => '123',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response2->assertSessionHasErrors('phone');
    }

    public function test_new_user_starts_with_initial_balance(): void
    {
        $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();

        // Application gives 500000 as initial balance
        $this->assertEquals(500000, $user->balance);
    }

    public function test_new_user_is_not_admin_by_default(): void
    {
        $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();

        // Cast to boolean for comparison (is_admin is stored as 0/1 in database)
        $this->assertFalse((bool)$user->is_admin);
    }

    public function test_password_is_hashed_on_registration(): void
    {
        $this->post('/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();

        // Password should be hashed (bcrypt)
        $this->assertNotEquals('Password123!', $user->password_hash);
        $this->assertTrue(Hash::check('Password123!', $user->password_hash));
    }
}
