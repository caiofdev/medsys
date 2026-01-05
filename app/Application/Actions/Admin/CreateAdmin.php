<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdmin
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute administrator creation
     *
     * @param array $data Validated data from AdminStoreRequest
     * @return void
     */
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            
            $photoPath = null;

            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                $photoPath = $this->fileUploadService->upload($data['photo'], 'photos');
            }

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'password' => Hash::make($data['password']),
                'photo' => $photoPath,
            ];

            $adminData = [
                'user_data' => $userData,
                'is_master' => $data['is_master'] === 'yes',
            ];

            $this->adminRepository->create($adminData);
        });
    }
}