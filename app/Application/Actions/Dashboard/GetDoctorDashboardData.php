<?php

namespace App\Application\Actions\Dashboard;

use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Domain\Models\User;

class GetDoctorDashboardData
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Execute doctor dashboard data retrieval
     *
     * @param User $user Authenticated doctor user
     * @return array Dashboard data for doctor
     */
    public function execute(User $user): array
    {
        $user->loadMissing('doctor');
        $doctor = $user->doctor;

        $appointmentCounts = $this->dashboardRepository->getDoctorAppointmentCounts($doctor->id);

        $upcomingAppointments = $this->dashboardRepository->getDoctorUpcomingAppointments($doctor->id);

        return [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/doctor-pic.png',
                'role' => 'DOUTOR',
                'crm' => $doctor->crm ?? null,
                'specialty' => 'Clínico Geral', // ✅ Valor padrão
            ],
            'appointments' => $appointmentCounts,
            'upcoming_appointments' => $upcomingAppointments,
        ];
    }
}