<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDoctor
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute doctor creation
     *
     * @param array $data Validated data from DoctorStoreRequest
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

            $doctorData = [
                'user_data' => $userData,
                'crm' => $data['crm'],
            ];

            $this->doctorRepository->create($doctorData);
        });
    }
}