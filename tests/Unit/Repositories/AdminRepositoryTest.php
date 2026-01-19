<?php

namespace Tests\Unit\Repositories;

use App\Domain\Exceptions\AdminNotFoundException;
use App\Domain\Models\Admin;
use App\Domain\Models\User;
use App\Infrastructure\Repositories\AdminRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AdminRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AdminRepository();
    }

    public function test_can_find_admin_by_id(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $user->id]);

        $found = $this->repository->findById($admin->id);

        $this->assertEquals($admin->id, $found->id);
        $this->assertEquals($user->name, $found->user->name);
    }

    public function test_throws_exception_when_admin_not_found(): void
    {
        $this->expectException(AdminNotFoundException::class);
        $this->repository->findById(999);
    }

    public function test_can_count_master_admins(): void
    {
        User::factory()->count(2)->create()->each(function ($user) {
            Admin::factory()->create([
                'user_id' => $user->id,
                'is_master' => true,
            ]);
        });

        User::factory()->count(3)->create()->each(function ($user) {
            Admin::factory()->create([
                'user_id' => $user->id,
                'is_master' => false,
            ]);
        });

        $count = $this->repository->countMasters();

        $this->assertEquals(2, $count);
    }

    public function test_can_create_admin(): void
    {
        $data = [
            'user_data' => [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'cpf' => '12345678901',
                'password' => bcrypt('password123'),
            ],
            'is_master' => true,
        ];

        $admin = $this->repository->create($data);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
        ]);
        $this->assertDatabaseHas('admins', [
            'user_id' => $admin->user->id,
            'is_master' => true,
        ]);
    }

    public function test_can_update_admin(): void
    {
        $user = User::factory()->create(['name' => 'Nome Antigo']);
        $admin = Admin::factory()->create([
            'user_id' => $user->id,
            'is_master' => false,
        ]);

        $updateData = [
            'name' => 'Nome Novo',
            'email' => 'novoemail@example.com',
        ];

        $updated = $this->repository->update($admin, $updateData);

        $this->assertEquals('Nome Novo', $updated->user->name);
        $this->assertEquals('novoemail@example.com', $updated->user->email);
    }

    public function test_can_delete_admin(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $user->id]);

        $this->repository->delete($admin);

        $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_can_search_admins_by_name(): void
    {
        $user1 = User::factory()->create(['name' => 'João Silva']);
        $user2 = User::factory()->create(['name' => 'Maria Santos']);
        $user3 = User::factory()->create(['name' => 'Pedro João']);

        Admin::factory()->create(['user_id' => $user1->id]);
        Admin::factory()->create(['user_id' => $user2->id]);
        Admin::factory()->create(['user_id' => $user3->id]);

        $results = $this->repository->search('João', 10);

        $this->assertEquals(2, $results->total());
        $this->assertTrue($results->contains(fn($admin) => $admin->user->name === 'João Silva'));
        $this->assertTrue($results->contains(fn($admin) => $admin->user->name === 'Pedro João'));
    }

    public function test_can_search_admins_by_email(): void
    {
        $user1 = User::factory()->create(['email' => 'teste@example.com']);
        $user2 = User::factory()->create(['email' => 'outro@example.com']);

        Admin::factory()->create(['user_id' => $user1->id]);
        Admin::factory()->create(['user_id' => $user2->id]);

        $results = $this->repository->search('teste', 10);

        $this->assertEquals(1, $results->total());
        $this->assertEquals('teste@example.com', $results->first()->user->email);
    }

    public function test_can_paginate_admins(): void
    {
        User::factory()->count(15)->create()->each(function ($user) {
            Admin::factory()->create(['user_id' => $user->id]);
        });

        $results = $this->repository->paginate(10);

        $this->assertEquals(10, $results->count());
        $this->assertEquals(15, $results->total());
        $this->assertEquals(2, $results->lastPage());
    }
}