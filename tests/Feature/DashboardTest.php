<?php

namespace Tests\Feature;

use App\Domain\Models\User;
use App\Domain\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_visit_the_dashboard()
    {
        $adminUser = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser)->get(route('admin.dashboard'));

        $this->get('/admin/dashboard')->assertOk();
    }
}
