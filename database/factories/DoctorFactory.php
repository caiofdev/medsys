<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Models\User;
use App\Models\Specialty;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'crm' => $this->faker->unique()->numberBetween(100000, 999999),
            'user_id' => User::factory(),
        ];
    }
}
