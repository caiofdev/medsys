<?php
// filepath: app/Application/Actions/Patient/UpdatePatient.php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;

class UpdatePatient
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Execute patient update
     *
     * @param int $patientId Patient ID
     * @param array $data Validated data from PatientUpdateRequest
     * @return void
     */
    public function execute(int $patientId, array $data): void
    {
        $patient = $this->patientRepository->findById($patientId);
        $this->patientRepository->update($patient, $data);
    }
}