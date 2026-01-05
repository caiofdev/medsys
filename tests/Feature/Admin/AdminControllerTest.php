<?php

namespace Tests\Feature\Admin;

use App\Domain\Models\Admin;
use App\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_admin_list(): void
    {
        $adminUser = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser)->get(route('admin.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => 
            $page->component('tables/admin-table')
        );
    }

    public function test_admin_can_create_new_admin(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $data = [
            'name' => 'Novo Admin',
            'email' => 'novoadmin@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone' => '11987654321',
            'birth_date' => '1990-01-01',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_master' => 'no',
        ];

        $response = $this->actingAs($adminUser)->post(route('admin.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'email' => 'novoadmin@example.com',
            'name' => 'Novo Admin',
        ]);

        $this->assertDatabaseHas('admins', [
            'is_master' => false,
        ]);
    }

    public function test_admin_can_create_admin_with_photo(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $photo = UploadedFile::fake()->image('admin.jpg');

        $data = [
            'name' => 'Admin com Foto',
            'email' => 'comfoto@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_master' => 'no',
            'photo' => $photo,
        ];

        $response = $this->actingAs($adminUser)->post(route('admin.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user = User::where('email', 'comfoto@example.com')->first();
        $this->assertNotNull($user->photo);
        Storage::disk('public')->assertExists($user->photo);
    }

    public function test_admin_can_view_admin_details(): void
    {
        $adminUser = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $adminUser->id]);

        $targetUser = User::factory()->create();
        $targetAdmin = Admin::factory()->create(['user_id' => $targetUser->id]);

        $response = $this->actingAs($adminUser)->get(route('admin.show', $targetAdmin->id));

        $response->assertOk();
        $response->assertJson([
            'id' => $targetAdmin->id,
            'name' => $targetUser->name,
            'email' => $targetUser->email,
        ]);
    }

    public function test_admin_can_update_admin(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $targetUser = User::factory()->create(['name' => 'Nome Antigo']);
        $targetAdmin = Admin::factory()->create(['user_id' => $targetUser->id]);

        $data = [
            'name' => 'Nome Atualizado',
            'email' => $targetUser->email,
            'phone' => '11999999999',
        ];

        $response = $this->actingAs($adminUser)->put(route('admin.update', $targetAdmin->id), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nome Atualizado',
            'phone' => '11999999999',
        ]);
    }

    public function test_admin_can_update_admin_photo(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $targetUser = User::factory()->create();
        $targetAdmin = Admin::factory()->create(['user_id' => $targetUser->id]);

        $newPhoto = UploadedFile::fake()->image('new-photo.jpg');

        $data = [
            'name' => $targetUser->name,
            'email' => $targetUser->email,
            'photo' => $newPhoto,
        ];

        $response = $this->actingAs($adminUser)->put(route('admin.update', $targetAdmin->id), $data);

        $response->assertRedirect();
        $targetUser->refresh();
        
        $this->assertNotNull($targetUser->photo);
        Storage::disk('public')->assertExists($targetUser->photo);
    }

    public function test_admin_can_delete_non_master_admin(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $targetUser = User::factory()->create();
        $targetAdmin = Admin::factory()->create(['user_id' => $targetUser->id, 'is_master' => false]);

        $response = $this->actingAs($adminUser)->delete(route('admin.destroy', $targetAdmin->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('admins', ['id' => $targetAdmin->id]);
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_cannot_delete_last_master_admin(): void
    {
        $adminUser = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser)->delete(route('admin.destroy', $admin->id));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('admins', ['id' => $admin->id]);
        $this->assertDatabaseHas('users', ['id' => $adminUser->id]);
    }

    public function test_can_delete_master_if_not_last(): void
    {
        $adminUser1 = User::factory()->create();
        $admin1 = Admin::factory()->create(['user_id' => $adminUser1->id, 'is_master' => true]);

        $adminUser2 = User::factory()->create();
        $admin2 = Admin::factory()->create(['user_id' => $adminUser2->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser1)->delete(route('admin.destroy', $admin2->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('admins', ['id' => $admin2->id]);
    }

    public function test_admin_search_filters_by_name(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id]);

        $user1 = User::factory()->create(['name' => 'João Silva']);
        $user2 = User::factory()->create(['name' => 'Maria Santos']);

        Admin::factory()->create(['user_id' => $user1->id]);
        Admin::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($adminUser)->get(route('admin.index', ['search' => 'João']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => 
            $page->component('tables/admin-table')
                ->has('admins.data', 1)
        );
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('login'));
        $this->post(route('admin.store'), [])->assertRedirect(route('login'));
    }

    public function test_validates_required_fields_on_create(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser)->post(route('admin.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'cpf', 'password']);
    }

    public function test_validates_unique_email_on_create(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($adminUser)->post(route('admin.store'), [
            'name' => 'Novo Admin',
            'email' => 'existing@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_master' => 'no',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validates_password_confirmation_on_create(): void
    {
        $adminUser = User::factory()->create();
        Admin::factory()->create(['user_id' => $adminUser->id, 'is_master' => true]);

        $response = $this->actingAs($adminUser)->post(route('admin.store'), [
            'name' => 'Novo Admin',
            'email' => 'novo@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'is_master' => 'no',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}