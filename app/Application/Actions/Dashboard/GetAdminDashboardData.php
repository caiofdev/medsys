<?php

namespace App\Application\Actions\Dashboard;

use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Domain\Models\User;
use App\Infrastructure\Services\DashboardStatisticsService;

class GetAdminDashboardData
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepository,
        private DashboardStatisticsService $statisticsService
    ) {}

    /**
     * Execute admin dashboard data retrieval
     *
     * @param User $user Authenticated admin user
     * @return array Dashboard data for admin
     */
    public function execute(User $user): array
    {
        $user->loadMissing('admin');

        $usersCounts = $this->dashboardRepository->getSystemUsersCounts();
        $consultations = $this->dashboardRepository->getLastCompletedConsultations(5);
        $revenueData = $this->dashboardRepository->getMonthlyRevenueData();

        $totalUsers = $this->statisticsService->calculateTotalUsers($usersCounts);
        $monthlyRevenue = $this->statisticsService->calculateMonthlyRevenueGrowth($revenueData);
        $consultationsChart = $this->statisticsService->prepareConsultationsChartData($consultations);
        $revenueChart = $this->statisticsService->prepareMonthlyRevenueChartData($monthlyRevenue);

        return [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/admin-pic.png',
                'role' => 'ADMINISTRADOR',
                'is_master' => $user->admin->is_master ?? false,
            ],
            'stats' => array_merge($usersCounts, [
                'total_users' => $totalUsers
            ]),
            'recent_activities' => [],
            'completed_consultations' => $consultationsChart,
            'monthly_revenue' => array_merge($monthlyRevenue, [
                'chart_data' => $revenueChart['data'],
                'chart_labels' => $revenueChart['labels'],
            ]),
        ];
    }
}