<?php
namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'payment_date' => now(),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'mobile_money']),
            'reference_number' => fake()->optional()->numerify('REF######'),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
