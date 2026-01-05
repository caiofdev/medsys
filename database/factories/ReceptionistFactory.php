<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Models\User;

class ReceptionistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_number' => $this->faker->unique()->numerify('REC-#####'),
            'user_id' => User::factory(),
        ];
    }
}
