<?php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;

class CreatePatient
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Execute patient creation
     *
     * @param array $data Validated data from PatientStoreRequest
     * @return void
     */
    public function execute(array $data): void
    {
        $this->patientRepository->create($data);
    }
}