<?php

namespace App\Application\Actions\Receptionist;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;

class CreateReceptionist
{
    public function __construct(
        private ReceptionistRepositoryInterface $receptionistRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute receptionist creation
     *
     * @param array $data Validated data from ReceptionistStoreRequest
     * @return void
     */
    public function execute(array $data): void
    {
        $photoPath = null;
        if (isset($data['photo']) && $data['photo']) {
            $photoPath = $this->fileUploadService->upload($data['photo'], 'photos');
        }

        $receptionistData = [
            'user_data' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $data['cpf'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'birth_date' => $data['birth_date'],
                'photo' => $photoPath,
            ],
            'registration_number' => $data['register_number'],
        ];

        $this->receptionistRepository->create($receptionistData);
    }
}