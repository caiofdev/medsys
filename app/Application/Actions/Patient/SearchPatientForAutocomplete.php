<?php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;

class SearchPatientForAutocomplete
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Search patients for autocomplete (used in scheduling)
     *
     * @param string $query Search term
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function execute(string $query)
    {
        return $this->patientRepository->searchForAutocomplete($query, 10);
    }
}