<?php

namespace Database\Seeders;

use App\Domain\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{    
    public function run(): void
    {
        Appointment::factory()->count(10)->create();
    }
}
