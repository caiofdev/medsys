<?php
// filepath: app/Application/Actions/Patient/DeletePatient.php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;

class DeletePatient
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Execute patient deletion
     *
     * @param int $patientId Patient ID
     * @return void
     */
    public function execute(int $patientId): void
    {
        $patient = $this->patientRepository->findById($patientId);
        $this->patientRepository->delete($patient);
    }
}