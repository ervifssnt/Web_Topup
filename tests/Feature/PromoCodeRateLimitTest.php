<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Models\TopupOption;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PromoCodeRateLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that checkout route has rate limiting.
     */
    public function test_checkout_route_has_rate_limiting(): void
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        $this->actingAs($user);

        // Create a transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        // Send 11 requests rapidly (limit is 10 per minute)
        $successCount = 0;
        $tooManyRequestsCount = 0;

        for ($i = 0; $i < 12; $i++) {
            // Create new transaction for each attempt
            $trans = Transaction::create([
                'user_id' => $user->id,
                'topup_option_id' => $option->id,
                'coins' => $option->coins,
                'price' => $option->price,
                'status' => 'pending',
            ]);

            $response = $this->post(route('checkout.process'), [
                'transaction_id' => $trans->id,
            ]);

            if ($response->status() === 429) {
                $tooManyRequestsCount++;
            } elseif ($response->isRedirection()) {
                $successCount++;
            }
        }

        // Should hit rate limit after 10 requests
        $this->assertGreaterThan(0, $tooManyRequestsCount, 'Rate limiting should block requests after limit');
    }

    /**
     * Test that invalid promo codes return generic error message.
     */
    public function test_invalid_promo_code_returns_generic_error(): void
    {
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        $this->actingAs($user);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        $response = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction->id,
            'promo_code' => 'INVALID_CODE_123',
        ]);

        // Should return generic error message
        $response->assertSessionHasErrors();
        $this->assertStringContainsString('Promo code invalid or not applicable', session('error') ?? '');
    }

    /**
     * Test that already-used promo codes return the same generic error.
     */
    public function test_already_used_promo_returns_same_generic_error(): void
    {
        $user = User::factory()->create(['balance' => 200000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        // Create a promo code
        $promoId = DB::table('promo_codes')->insertGetId([
            'code' => 'testpromo2024',
            'discount_percent' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark promo as already used
        DB::table('promo_code_usage')->insert([
            'user_id' => $user->id,
            'promo_code_id' => $promoId,
            'transaction_id' => 1,
            'used_at' => now(),
        ]);

        $this->actingAs($user);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        $response = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction->id,
            'promo_code' => 'testpromo2024',
        ]);

        // Should return the SAME generic error as invalid code
        $this->assertStringContainsString('Promo code invalid or not applicable', session('error') ?? '');
    }

    /**
     * Test that failed promo attempts are tracked.
     */
    public function test_failed_promo_attempts_are_tracked(): void
    {
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        $this->actingAs($user);

        Cache::flush(); // Clear cache before test

        // Try 3 invalid promo codes
        for ($i = 0; $i < 3; $i++) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'topup_option_id' => $option->id,
                'coins' => $option->coins,
                'price' => $option->price,
                'status' => 'pending',
            ]);

            $this->post(route('checkout.process'), [
                'transaction_id' => $transaction->id,
                'promo_code' => 'INVALID_' . $i,
            ]);
        }

        // Verify cache counter incremented
        $failCount = Cache::get("promo_fail:{$user->id}");
        $this->assertEquals(3, $failCount);
    }

    /**
     * Test that suspicious activity is logged after 5 failed attempts.
     */
    public function test_suspicious_promo_activity_is_logged_after_threshold(): void
    {
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        $this->actingAs($user);

        Cache::flush();

        // Try 6 invalid promo codes (threshold is 5)
        for ($i = 0; $i < 6; $i++) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'topup_option_id' => $option->id,
                'coins' => $option->coins,
                'price' => $option->price,
                'status' => 'pending',
            ]);

            $this->post(route('checkout.process'), [
                'transaction_id' => $transaction->id,
                'promo_code' => 'INVALID_' . $i,
            ]);
        }

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'suspicious_promo_attempts',
        ]);
    }

    /**
     * Test that successful promo use clears failed attempt counter.
     */
    public function test_successful_promo_use_clears_fail_counter(): void
    {
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 10000]);

        // Create a valid promo code
        $promoId = DB::table('promo_codes')->insertGetId([
            'code' => 'validpromo',
            'discount_percent' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        Cache::flush();

        // Set a fail counter
        Cache::put("promo_fail:{$user->id}", 3, now()->addMinutes(10));

        // Use valid promo code
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        $response = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction->id,
            'promo_code' => 'validpromo',
        ]);

        // Verify counter was cleared
        $failCount = Cache::get("promo_fail:{$user->id}");
        $this->assertNull($failCount);
    }
}
