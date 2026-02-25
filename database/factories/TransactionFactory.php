<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'wallet_id'   => Wallet::factory(),
            'type'        => fake()->randomElement(['income', 'expense']),
            'amount'      => fake()->randomFloat(2, 10, 5000),
            'description' => fake()->sentence(),
        ];
    }
}
