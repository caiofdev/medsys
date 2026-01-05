<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Models\Appointment;

class ConsultationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'symptoms' => $this->faker->sentence(),
            'diagnosis' => $this->faker->sentence(),
            'notes' => $this->faker->paragraph(),
            'appointment_id' => Appointment::factory(), 
        ];
    }
}
