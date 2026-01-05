<?php

namespace Tests\Unit\Actions\Admin;

use App\Application\Actions\Admin\DeleteAdmin;
use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\CannotDeleteLastMasterException;
use App\Domain\Models\Admin;
use App\Domain\Models\User;
use App\Infrastructure\Services\FileUploadService;
use Mockery;
use Tests\TestCase;

class DeleteAdminTest extends TestCase
{
    private AdminRepositoryInterface $adminRepository;
    private FileUploadService $fileUploadService;
    private DeleteAdmin $deleteAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminRepository = Mockery::mock(AdminRepositoryInterface::class);
        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        $this->deleteAdmin = new DeleteAdmin($this->adminRepository, $this->fileUploadService);
    }

    public function test_can_delete_non_master_admin(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('delete')
            ->once()
            ->with($admin);

        $this->deleteAdmin->execute(1);

        $this->assertTrue(true);
    }

    public function test_can_delete_master_admin_if_not_last(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = true;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('countMasters')
            ->once()
            ->andReturn(2); 

        $this->adminRepository->shouldReceive('delete')
            ->once()
            ->with($admin);

        $this->deleteAdmin->execute(1);

        $this->assertTrue(true);
    }

    public function test_cannot_delete_last_master_admin(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = true;

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->adminRepository->shouldReceive('countMasters')
            ->once()
            ->andReturn(1); 

        $this->expectException(CannotDeleteLastMasterException::class);
        $this->deleteAdmin->execute(1);
    }

    public function test_deletes_admin_photo_if_exists(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = 'photos/admin-photo.jpg';

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->fileUploadService->shouldReceive('delete')
            ->once()
            ->with('photos/admin-photo.jpg');

        $this->adminRepository->shouldReceive('delete')
            ->once()
            ->with($admin);

        $this->deleteAdmin->execute(1);

        $this->assertTrue(true);
    }

    public function test_does_not_delete_photo_if_admin_has_none(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->is_master = false;
        $admin->user = Mockery::mock(User::class);
        $admin->user->photo = null;

        $this->adminRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($admin);

        $this->fileUploadService->shouldNotReceive('delete');

        $this->adminRepository->shouldReceive('delete')
            ->once()
            ->with($admin);

        $this->deleteAdmin->execute(1);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}