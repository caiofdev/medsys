<?php
// filepath: app/Application/Actions/Patient/ShowPatient.php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;
use App\Domain\Models\Patient;

class ShowPatient
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Find patient by ID
     *
     * @param int $patientId Patient ID
     * @return Patient
     */
    public function execute(int $patientId): Patient
    {
        return $this->patientRepository->findById($patientId);
    }
}