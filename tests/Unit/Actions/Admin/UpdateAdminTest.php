<?php

namespace Tests\Unit\Actions\Admin;

use App\Application\Actions\Admin\UpdateAdmin;
use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\CannotRemoveLastMasterException;
use App\Domain\Models\Admin;
use App\Domain\Models\User;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class UpdateAdminTest extends TestCase
{
    private AdminRepositoryInterface $adminRepository;
    private FileUploadService $fileUploadService;
    private UpdateAdmin $updateAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminRepository = Mockery::mock(AdminRepositoryInterface::class);
        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        $this->updateAdmin = new UpdateAdmin($this->adminRepository, $this->fileUploadService);
    }

    public function test_can_update_admin_basic_info(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $data = [
            'name' => 'Nome Atualizado',
            'email' => 'novo@example.com',
            'phone' => '11999999999',
        ];

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('update')
            ->once()
            ->with($admin, $data)
            ->andReturn($admin);

        $this->updateAdmin->execute(1, $data);

        $this->assertTrue(true);
    }

    public function test_can_update_admin_with_new_photo(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = 'photos/old-photo.jpg';

        $newPhoto = UploadedFile::fake()->image('new-photo.jpg');
        $data = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'photo' => $newPhoto,
        ];

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->fileUploadService->shouldReceive('delete')
            ->once()
            ->with('photos/old-photo.jpg');

        $this->fileUploadService->shouldReceive('upload')
            ->once()
            ->with($newPhoto, 'photos')
            ->andReturn('photos/new-photo.jpg');

        $this->adminRepository->shouldReceive('update')
            ->once()
            ->with($admin, Mockery::on(function ($arg) {
                return $arg['photo'] === 'photos/new-photo.jpg';
            }))
            ->andReturn($admin);

        $this->updateAdmin->execute(1, $data);

        $this->assertTrue(true);
    }

    public function test_cannot_remove_master_status_from_last_master(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = true;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $data = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_master' => 'no', 
        ];

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('countMasters')
            ->once()
            ->andReturn(1); 

        $this->expectException(CannotRemoveLastMasterException::class);
        $this->updateAdmin->execute(1, $data);
    }

    public function test_can_remove_master_status_if_not_last_master(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = true;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $data = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_master' => 'no',
        ];

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('countMasters')
            ->once()
            ->andReturn(2); 

        $this->adminRepository->shouldReceive('update')
            ->once()
            ->with($admin, Mockery::on(function ($arg) {
                return $arg['is_master'] === false;
            }))
            ->andReturn($admin);

        $this->updateAdmin->execute(1, $data);

        $this->assertTrue(true);
    }

    public function test_does_not_update_photo_if_not_provided(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = 'photos/existing.jpg';

        $data = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ];

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->fileUploadService->shouldNotReceive('delete');
        $this->fileUploadService->shouldNotReceive('upload');

        $this->adminRepository->shouldReceive('update')
            ->once()
            ->with($admin, Mockery::on(function ($arg) {
                return !isset($arg['photo']); 
            }))
            ->andReturn($admin);

        $this->updateAdmin->execute(1, $data);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}