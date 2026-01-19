<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Models\Doctor;
use App\Domain\Models\Patient;
use App\Domain\Models\Receptionist;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $doctorIds = Doctor::pluck('id')->toArray();
        $patientIds = Patient::pluck('id')->toArray();
        $receptionistIds = Receptionist::pluck('id')->toArray();

        return [
            'appointment_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'canceled']),
            'value' => $this->faker->randomFloat(2, 50, 500),
            'doctor_id' => $this->faker->randomElement($doctorIds),
            'patient_id' => $this->faker->randomElement($patientIds),
            'receptionist_id' => $this->faker->randomElement($receptionistIds),
        ];
    }
}
