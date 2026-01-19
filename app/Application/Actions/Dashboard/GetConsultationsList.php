<?php

namespace App\Application\Actions\Dashboard;

use App\Domain\Contracts\DashboardRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetConsultationsList
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Execute consultations list retrieval with pagination
     *
     * @param string|null $search Search term for filtering consultations
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator Paginated consultations list
     */
    public function execute(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->dashboardRepository->getConsultationsList($search, $perPage);
    }
}