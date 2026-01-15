<?php
// filepath: app/Domain/Contracts/DashboardRepositoryInterface.php

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
}