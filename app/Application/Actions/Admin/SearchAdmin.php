<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchAdmin
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {}

    /**
     * Retrieve administrators with pagination
     *
     * @param string|null $search Search term
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function execute(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->adminRepository->search($search, $perPage);
    }
}