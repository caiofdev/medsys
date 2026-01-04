<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateAdmin
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute administrator update
     *
     * @param int $adminId Administrator ID
     * @param array $data Validated data from AdminUpdateRequest (name, email, phone, photo)
     * @return void
     */
    public function execute(int $adminId, array $data): void
    {
        DB::transaction(function () use ($adminId, $data) {
            
            $admin = $this->adminRepository->findById($adminId);

            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {

                if ($admin->user->photo) {
                    $this->fileUploadService->delete($admin->user->photo);
                }

                 $data['photo'] = $this->fileUploadService->upload($data['photo'], 'photos');    
            } else {
                unset($data['photo']);
            }
            
            $this->adminRepository->update($admin, $data);
        });
    }
}