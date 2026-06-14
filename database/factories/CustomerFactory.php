<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'national_id' => fake()->unique()->numerify('ID######'),
            'notes' => fake()->sentence(),
        ];
    }
}
