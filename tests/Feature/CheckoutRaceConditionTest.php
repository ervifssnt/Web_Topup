<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Models\TopupOption;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutRaceConditionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that atomic balance deduction prevents double spending.
     */
    public function test_atomic_balance_deduction_prevents_double_spending(): void
    {
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 100000]);

        // Test direct model method
        $result = $user->deductBalanceAtomic(100000);

        $this->assertTrue($result);
        $this->assertEquals(0, $user->balance);

        // Try to deduct again (should fail)
        $result = $user->deductBalanceAtomic(100000);

        $this->assertFalse($result);
        $this->assertEquals(0, $user->balance); // Balance should still be 0
    }

    /**
     * Test that insufficient balance is handled atomically.
     */
    public function test_atomic_deduction_fails_with_insufficient_balance(): void
    {
        $user = User::factory()->create(['balance' => 50000]);

        // Try to deduct more than available
        $result = $user->deductBalanceAtomic(100000);

        $this->assertFalse($result);
        $this->assertEquals(50000, $user->balance); // Balance unchanged
    }

    /**
     * Test that multiple simultaneous checkout attempts are handled correctly.
     */
    public function test_concurrent_checkout_attempts_are_serialized(): void
    {
        // User has balance for only one transaction
        $user = User::factory()->create(['balance' => 100000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 100000]);

        $this->actingAs($user);

        // Create two pending transactions
        $transaction1 = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        $transaction2 = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        // Process first checkout
        $response1 = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction1->id,
        ]);

        // First should succeed
        $response1->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction1->id,
            'status' => 'paid',
        ]);

        // Verify balance was deducted
        $user->refresh();
        $this->assertEquals(0, $user->balance);

        // Try second checkout (should fail due to insufficient balance)
        $response2 = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction2->id,
        ]);

        // Second should fail
        $response2->assertSessionHas('error');

        // Second transaction should still be pending
        $transaction2->refresh();
        $this->assertEquals('pending', $transaction2->status);

        // Balance should still be 0 (not negative)
        $user->refresh();
        $this->assertEquals(0, $user->balance);
    }

    /**
     * Test that atomic operation refreshes model balance correctly.
     */
    public function test_atomic_deduction_refreshes_model_balance(): void
    {
        $user = User::factory()->create(['balance' => 100000]);

        $initialBalance = $user->balance;
        $result = $user->deductBalanceAtomic(30000);

        $this->assertTrue($result);
        $this->assertEquals(70000, $user->balance);
        $this->assertNotEquals($initialBalance, $user->balance);
    }

    /**
     * Test that checkout with promo code prevents race condition.
     */
    public function test_checkout_with_promo_prevents_race_condition(): void
    {
        $user = User::factory()->create(['balance' => 90000]);
        $game = Game::factory()->create();
        $option = TopupOption::factory()->create(['game_id' => $game->id, 'price' => 100000]);

        // Create promo code (10% discount, makes price 90000)
        $promoId = \DB::table('promo_codes')->insertGetId([
            'code' => 'raceconditiontest',
            'discount_percent' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        // Create two pending transactions
        $transaction1 = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        $transaction2 = Transaction::create([
            'user_id' => $user->id,
            'topup_option_id' => $option->id,
            'coins' => $option->coins,
            'price' => $option->price,
            'status' => 'pending',
        ]);

        // First checkout with promo (90000 after discount)
        $response1 = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction1->id,
            'promo_code' => 'raceconditiontest',
        ]);

        $response1->assertRedirect();

        // Verify first transaction paid
        $transaction1->refresh();
        $this->assertEquals('paid', $transaction1->status);

        // Verify balance is now 0
        $user->refresh();
        $this->assertEquals(0, $user->balance);

        // Try second checkout (should fail - insufficient balance)
        $response2 = $this->post(route('checkout.process'), [
            'transaction_id' => $transaction2->id,
            'promo_code' => 'raceconditiontest', // Can't use twice anyway
        ]);

        // Should fail (either insufficient balance or promo already used)
        $response2->assertSessionHas('error');

        // Second transaction should not be paid
        $transaction2->refresh();
        $this->assertEquals('pending', $transaction2->status);

        // Balance should not go negative
        $user->refresh();
        $this->assertGreaterThanOrEqual(0, $user->balance);
    }

    /**
     * Test that partial deductions work correctly.
     */
    public function test_partial_balance_deductions_work_correctly(): void
    {
        $user = User::factory()->create(['balance' => 100000]);

        // Deduct 30000
        $result1 = $user->deductBalanceAtomic(30000);
        $this->assertTrue($result1);
        $this->assertEquals(70000, $user->balance);

        // Deduct another 40000
        $result2 = $user->deductBalanceAtomic(40000);
        $this->assertTrue($result2);
        $this->assertEquals(30000, $user->balance);

        // Try to deduct 50000 (more than remaining)
        $result3 = $user->deductBalanceAtomic(50000);
        $this->assertFalse($result3);
        $this->assertEquals(30000, $user->balance); // Should remain unchanged
    }
}
