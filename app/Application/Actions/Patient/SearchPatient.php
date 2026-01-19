<?php
// filepath: app/Application/Actions/Patient/SearchPatient.php

namespace App\Application\Actions\Patient;

use App\Domain\Contracts\PatientRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchPatient
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Search patients with pagination
     *
     * @param string|null $search Search term
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function execute(?string $search = null, int $perPage = 8): LengthAwarePaginator
    {
        return $this->patientRepository->search($search, $perPage);
    }
}