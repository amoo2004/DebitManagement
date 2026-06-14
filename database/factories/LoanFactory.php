<?php
namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 1000, 500000);
        return [
            'customer_id' => Customer::factory(),
            'product_name' => fake()->word(),
            'loan_amount' => $amount,
            'paid_amount' => 0,
            'remaining_amount' => $amount,
            'loan_date' => now(),
            'due_date' => now()->addMonth(),
            'status' => 'paying',
            'created_by' => User::factory(),
        ];
    }
}
