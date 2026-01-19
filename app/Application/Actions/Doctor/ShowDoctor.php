<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Models\Doctor;

class ShowDoctor
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Retrieve doctor by ID
     *
     * @param int $doctorId Doctor ID
     * @return Doctor
     */
    public function execute(int $doctorId): Doctor
    {
        return $this->doctorRepository->findById($doctorId);
    }
}