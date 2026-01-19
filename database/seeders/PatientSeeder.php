<?php

namespace Database\Seeders;

use App\Domain\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        Patient::factory()->count(10)->create();
    }
}
