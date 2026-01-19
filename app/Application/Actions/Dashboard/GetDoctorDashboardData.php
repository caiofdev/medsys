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

        $summary = $this->dashboardRepository->getDailySummary();

        $consultationsSummary = $this->dashboardRepository->getDoctorConsultationsSummary($doctor->id);
        
        $weeklyAppointmentsStatus = $this->dashboardRepository->getDoctorWeeklyAppointmentsStatus($doctor->id);

        $patients = $this->dashboardRepository->getPatients($doctor->id);

        return [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/doctor-pic.png',
                'role' => 'DOUTOR',
                'crm' => $doctor->crm ?? null,
                'specialty' => 'Clínico Geral',
            ],
            'appointments' => $appointmentCounts,
            'upcoming_appointments' => $upcomingAppointments,
            'daily_summary' => $summary,
            'consultations_summary' => $consultationsSummary,
            'weekly_appointments_status' => $weeklyAppointmentsStatus,
            'patients' => $patients
        ];
    }
}