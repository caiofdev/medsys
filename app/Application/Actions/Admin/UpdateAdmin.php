<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\CannotRemoveLastMasterException;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class updateAdmin
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute administrator update
     *
     * @param int $adminId Administrator ID
     * @param array $data Validated data from AdminUpdateRequest
     * @return void
     * @throws CannotRemoveLastMasterException
     */
    public function execute(int $adminId, array $data): void
    {
        DB::transaction(function () use ($adminId, $data) {
            
            $admin = $this->adminRepository->findById($adminId);

            if ($admin->is_master && isset($data['is_master'])) {
                $isMasterNew = $data['is_master'] === 'yes';

                if (!$isMasterNew) {
                    $masterCount = $this->adminRepository->countMasters();
                    
                    if ($masterCount <= 1) {
                        throw new CannotRemoveLastMasterException();
                    }
                }
            }

            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {

                if ($admin->user->photo) {
                    $this->fileUploadService->delete($admin->user->photo);
                }

                 $data['photo'] = $this->fileUploadService->upload($data['photo'], 'photos');    
            } else {
                unset($data['photo']);
            }

            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                
                unset($data['password']);
            }

            if (isset($data['is_master'])) {
                $data['is_master'] = $data['is_master'] === 'yes';
            }
            
            $this->adminRepository->update($admin, $data);
        });
    }
}