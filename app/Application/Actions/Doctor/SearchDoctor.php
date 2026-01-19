<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchDoctor
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Retrieve doctors with pagination
     *
     * @param string|null $search Search term
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function execute(?string $search = null, int $perPage = 8): LengthAwarePaginator
    {
        return $this->doctorRepository->search($search, $perPage);
    }
}