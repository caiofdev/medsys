<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Models\User;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'is_master' => $this->faker->boolean(),
            'user_id' => User::factory(), 
        ];
    }
}
