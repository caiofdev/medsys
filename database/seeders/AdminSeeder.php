<?php

namespace Database\Seeders;

use App\Domain\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::factory()->count(5)->create();
    }
}
