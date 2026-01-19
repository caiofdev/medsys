<?php

namespace App\Application\Actions\Receptionist;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;

class UpdateReceptionist
{
    public function __construct(
        private ReceptionistRepositoryInterface $receptionistRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute receptionist update
     *
     * @param int $receptionistId Receptionist ID
     * @param array $data Validated data from ReceptionistUpdateRequest
     * @return void
     */
    public function execute(int $receptionistId, array $data): void
    {
        $receptionist = $this->receptionistRepository->findById($receptionistId);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        if (isset($data['photo']) && $data['photo']) {
            if ($receptionist->user->photo) {
                $this->fileUploadService->delete($receptionist->user->photo);
            }

            $updateData['photo'] = $this->fileUploadService->upload($data['photo'], 'photos');
        }

        $this->receptionistRepository->update($receptionist, $updateData);
    }
}