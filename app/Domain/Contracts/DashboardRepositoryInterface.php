<?php

namespace App\Domain\Contracts;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function getSystemUsersCounts(): array;

    public function getDoctorAppointmentCounts(int $doctorId): array;

    public function getDailySummary(): array;

    public function getMonthlyRevenueData(): array;

    public function getLastCompletedConsultations(int $limit = 5): array;

    public function getWeeklyAppointments(): Collection;

    public function getDoctorUpcomingAppointments(int $doctorId): Collection;

    public function getConsultationsList(?string $search = null, int $perPage = 10);

    public function getSemesterRevenueData(): array;

    public function getAllSystemUsers(): array;

    public function getPatients(int $doctorId): array;

    public function getDoctorConsultationsSummary(int $doctorId): array;

    public function getDoctorWeeklyAppointmentsStatus(int $doctorId): array;
}