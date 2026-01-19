<?php

namespace App\Application\Actions\Receptionist;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Domain\Models\Receptionist;

class ShowReceptionist
{
    public function __construct(
        private ReceptionistRepositoryInterface $receptionistRepository
    ) {}

    /**
     * Find receptionist by ID
     *
     * @param int $receptionistId Receptionist ID
     * @return Receptionist
     */
    public function execute(int $receptionistId): Receptionist
    {
        return $this->receptionistRepository->findById($receptionistId);
    }
}