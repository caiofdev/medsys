<?php
namespace App\Application\Actions\Dashboard;

use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Domain\Models\User;

class GetReceptionistDashboardData
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Execute receptionist dashboard data retrieval
     *
     * @param User $user Authenticated receptionist user
     * @return array Dashboard data for receptionist
     */
    public function execute(User $user): array
    {
        $user->loadMissing('receptionist');
        $receptionist = $user->receptionist;

        $dailySummary = $this->dashboardRepository->getDailySummary();

        $weeklyAppointments = $this->dashboardRepository->getWeeklyAppointments();

        $patients = $this->dashboardRepository->getPatients(null);

        return [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/recepcionist-pic.png',
                'role' => 'RECEPCIONISTA',
                'registration_number' => $receptionist->registration_number ?? null,
            ],
            'daily_summary' => $dailySummary,
            'weekly_appointments' => $weeklyAppointments,
            'patients' => $patients
        ];
    }
}