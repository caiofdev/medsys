<?php

namespace Database\Seeders;

use App\Domain\Models\Receptionist;
use Illuminate\Database\Seeder;

class ReceptionistSeeder extends Seeder
{
    public function run(): void
    {
        Receptionist::factory()->count(5)->create();
    }
}
