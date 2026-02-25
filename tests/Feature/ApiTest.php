<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    // User Tests

    public function test_can_create_a_user(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_create_user_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422);
    }

    // Wallet Tests

    public function test_can_create_a_wallet(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/wallets', [
            'user_id' => $user->id,
            'name'    => 'Savings',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('wallets', ['user_id' => $user->id, 'name' => 'Savings']);
    }

    // Transaction Tests

    public function test_can_add_a_transaction_to_a_wallet(): void
    {
        $wallet = Wallet::factory()->create();

        $response = $this->postJson('/api/v1/transactions', [
            'wallet_id'   => $wallet->id,
            'type'        => 'income',
            'amount'      => 5000,
            'description' => 'Salary',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', ['wallet_id' => $wallet->id, 'amount' => 5000]);
    }

    public function test_transaction_rejects_invalid_type(): void
    {
        $wallet = Wallet::factory()->create();

        $response = $this->postJson('/api/v1/transactions', [
            'wallet_id' => $wallet->id,
            'type'      => 'transfer',
            'amount'    => 100,
        ]);

        $response->assertStatus(422);
    }

    // Balance Calculation Tests

    public function test_wallet_balance_is_calculated_correctly(): void
    {
        $wallet = Wallet::factory()->create();

        // Add income and expense transactions
        Transaction::factory()->create(['wallet_id' => $wallet->id, 'type' => 'income',  'amount' => 1000]);
        Transaction::factory()->create(['wallet_id' => $wallet->id, 'type' => 'income',  'amount' => 500]);
        Transaction::factory()->create(['wallet_id' => $wallet->id, 'type' => 'expense', 'amount' => 300]);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}");

        $response->assertOk();

        // Balance should be 1000 + 500 - 300 = 1200
        $balance = $response->json('data.balance');
        $this->assertEquals(1200, $balance);
    }

    public function test_user_profile_shows_wallets_and_total_balance(): void
    {
        $user    = User::factory()->create();
        $wallet1 = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'Personal']);
        $wallet2 = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'Business']);

        Transaction::factory()->create(['wallet_id' => $wallet1->id, 'type' => 'income',  'amount' => 2000]);
        Transaction::factory()->create(['wallet_id' => $wallet2->id, 'type' => 'income',  'amount' => 3000]);
        Transaction::factory()->create(['wallet_id' => $wallet2->id, 'type' => 'expense', 'amount' => 500]);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertOk();

        // Wallet 1: 2000, Wallet 2: 3000 - 500 = 2500, Total: 4500
        $data = $response->json('data');
        $this->assertCount(2, $data['wallets']);
        $this->assertEquals(4500, $data['total_balance']);
    }
}
