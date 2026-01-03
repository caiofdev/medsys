<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\CannotDeleteLastMasterException;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DeleteAdmin
{
    public function __construct (
        private AdminRepositoryInterface $adminRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute administrator deletion
     *
     * @param int $adminId Administrator ID
     * @return void
     * @throws CannotDeleteLastMasterException
     */
    public function execute(int $adminId): void
    {
        DB::transaction(function () use ($adminId) {

            $admin = $this->adminRepository->findById($adminId);

            if ($admin->is_master) {
                $masterCount = $this->adminRepository->countMasters();

                if ($masterCount <= 1) {
                    throw new CannotDeleteLastMasterException();
                }
            }

            if ($admin->user->photo) {
                $this->fileUploadService->delete($admin->user->photo);
            }

            $this->adminRepository->delete($admin);
        });
    }
}