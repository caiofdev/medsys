<?php

namespace App\Application\Actions\Receptionist;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;

class DeleteReceptionist
{
    public function __construct(
        private ReceptionistRepositoryInterface $receptionistRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute receptionist deletion
     *
     * @param int $receptionistId Receptionist ID
     * @return void
     */
    public function execute(int $receptionistId): void
    {
        $receptionist = $this->receptionistRepository->findById($receptionistId);

        if ($receptionist->user->photo) {
            $this->fileUploadService->delete($receptionist->user->photo);
        }

        $this->receptionistRepository->delete($receptionist);
    }
}