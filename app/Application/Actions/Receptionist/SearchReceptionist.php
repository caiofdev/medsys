<?php

namespace App\Application\Actions\Receptionist;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchReceptionist
{
    public function __construct(
        private ReceptionistRepositoryInterface $receptionistRepository
    ) {}

    /**
     * Search receptionists with pagination
     *
     * @param string|null $search Search term
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function execute(?string $search = null, int $perPage = 8): LengthAwarePaginator
    {
        return $this->receptionistRepository->search($search, $perPage);
    }
}