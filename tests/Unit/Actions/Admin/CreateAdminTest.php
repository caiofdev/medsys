<?php

namespace Tests\Unit\Actions\Admin;

use App\Application\Actions\Admin\CreateAdmin;
use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Models\Admin;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    private AdminRepositoryInterface $adminRepository;
    private FileUploadService $fileUploadService;
    private CreateAdmin $createAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRepository = Mockery::mock(AdminRepositoryInterface::class);
        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        $this->createAdmin = new CreateAdmin($this->adminRepository, $this->fileUploadService);
    }

    public function test_can_create_admin_without_photo(): void
    {
        $data = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone' => '11987654321',
            'birth_date' => '1990-01-01',
            'password' => 'password123',
            'is_master' => 'yes',
        ];

        $this->adminRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['user_data']['name'] === 'João Silva'
                    && $arg['user_data']['email'] === 'joao@example.com'
                    && $arg['user_data']['photo'] === null
                    && $arg['is_master'] === true;
            }))
            ->andReturn(Mockery::mock(Admin::class));

        $this->createAdmin->execute($data);

        $this->assertTrue(true);
    }

    public function test_can_create_admin_with_photo(): void
    {
        $photo = UploadedFile::fake()->image('admin.jpg');
        $data = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'is_master' => 'no',
            'photo' => $photo,
        ];

        $this->fileUploadService->shouldReceive('upload')
            ->once()
            ->with($photo, 'photos')
            ->andReturn('photos/admin-123.jpg');

        $this->adminRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['user_data']['photo'] === 'photos/admin-123.jpg'
                    && $arg['is_master'] === false;
            }))
            ->andReturn(Mockery::mock(Admin::class));

        $this->createAdmin->execute($data);

        $this->assertTrue(true);
    }

    public function test_converts_is_master_yes_to_true(): void
    {
        $data = [
            'name' => 'Admin Master',
            'email' => 'master@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'is_master' => 'yes', 
        ];

        $this->adminRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['is_master'] === true;
            }))
            ->andReturn(Mockery::mock(Admin::class));

        $this->createAdmin->execute($data);

        $this->assertTrue(true);
    }

    public function test_converts_is_master_no_to_false(): void
    {
        $data = [
            'name' => 'Admin Regular',
            'email' => 'regular@example.com',
            'cpf' => '12345678901',
            'password' => 'password123',
            'is_master' => 'no', 
        ];

        $this->adminRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['is_master'] === false; 
            }))
            ->andReturn(Mockery::mock(Admin::class));

        $this->createAdmin->execute($data);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}