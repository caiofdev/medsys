<?php
// filepath: app/Infrastructure/Repositories/DashboardRepository.php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Domain\Models\User;
use App\Domain\Models\Admin;
use App\Domain\Models\Appointment;
use App\Domain\Models\Consultation;
use App\Domain\Models\Doctor;
use App\Domain\Models\Receptionist;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getSystemUsersCounts(): array
    {
        return Cache::remember('dashboard_users_counts', 300, function() {
            return [
                'total_admins' => Admin::count(),
                'total_doctors' => Doctor::count(),
                'total_receptionists' => Receptionist::count(),
            ];
        });
    }

    public function getDoctorAppointmentCounts(int $doctorId): array
    {
        $counts = Appointment::where('doctor_id', $doctorId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN DATE(appointment_date) = CURDATE() THEN 1 END) as today')
            ->selectRaw('COUNT(CASE WHEN appointment_date BETWEEN ? AND ? THEN 1 END) as week', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])
            ->selectRaw('COUNT(CASE WHEN MONTH(appointment_date) = ? AND YEAR(appointment_date) = ? THEN 1 END) as month', [
                now()->month, 
                now()->year
            ])
            ->first();

        return [
            'today' => $counts->today ?? 0,
            'week' => $counts->week ?? 0,
            'month' => $counts->month ?? 0,
            'total' => $counts->total ?? 0,
        ];
    }

    public function getDailySummary(): array
    {
        $summary = Appointment::whereDate('appointment_date', today())
            ->selectRaw('COUNT(*) as appointments_today')
            ->selectRaw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_today')
            ->selectRaw('COUNT(CASE WHEN status = "scheduled" THEN 1 END) as pending_today')
            ->selectRaw('COUNT(CASE WHEN status = "canceled" THEN 1 END) as cancelled_today')
            ->first();

        return [
            'appointments_today' => $summary->appointments_today ?? 0,
            'completed_today' => $summary->completed_today ?? 0,
            'pending_today' => $summary->pending_today ?? 0,
            'cancelled_today' => $summary->cancelled_today ?? 0,
        ];
    }

    public function getMonthlyRevenueData(): array
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        $data = Appointment::where('status', 'completed')
            ->selectRaw('
                SUM(CASE WHEN appointment_date BETWEEN ? AND ? THEN value ELSE 0 END) as current_revenue,
                SUM(CASE WHEN appointment_date BETWEEN ? AND ? THEN value ELSE 0 END) as previous_revenue,
                COUNT(CASE WHEN appointment_date BETWEEN ? AND ? THEN 1 END) as current_count,
                COUNT(CASE WHEN appointment_date BETWEEN ? AND ? THEN 1 END) as previous_count
            ', [
                $currentMonth, now(),
                $previousMonth, $previousMonthEnd,
                $currentMonth, now(),
                $previousMonth, $previousMonthEnd,
            ])
            ->first();

        return [
            'current_revenue' => $data->current_revenue ?? 0,
            'previous_revenue' => $data->previous_revenue ?? 0,
            'current_count' => $data->current_count ?? 0,
            'previous_count' => $data->previous_count ?? 0,
            'current_month_name' => $currentMonth->format('F Y'),
            'previous_month_name' => $previousMonth->format('F Y'),
        ];
    }

    public function getLastCompletedConsultations(int $limit = 5): array
    {
        return Consultation::whereHas('appointment', function($query) {
                $query->where('status', 'completed');
            })
            ->with([
                'appointment.doctor.user:id,name',
                'appointment.patient:id,name',
                'appointment:id,value,appointment_date,doctor_id,patient_id,status'
            ])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function($consultation) {
                return [
                    'id' => $consultation->id,
                    'value' => $consultation->appointment->value,
                    'appointment_date' => $consultation->appointment->appointment_date,
                    'doctor_name' => $consultation->appointment->doctor->user->name,
                    'patient_name' => $consultation->appointment->patient->name,
                    'diagnosis' => $consultation->diagnosis,
                    'created_at' => $consultation->created_at,
                ];
            })
            ->toArray();
    }

    public function getWeeklyAppointments(): Collection
    {
        return Appointment::whereBetween('appointment_date', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_date')
            ->get();
    }

    public function getDoctorUpcomingAppointments(int $doctorId): Collection
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])
            ->with('patient')
            ->orderBy('appointment_date')
            ->get();
    }

    public function getConsultationsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Consultation::with(['appointment' => function($query) {
                $query->with(['doctor.user', 'patient', 'receptionist.user']);
            }])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('appointment.patient', function($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('appointment.doctor.user', function($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('diagnosis', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getSemesterRevenueData(): array
    {
        $labels = [];
        $data = [];
        $totalRevenue = 0;

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $label = $month->format('M');

            $revenue = Appointment::where('status', 'completed')
                ->whereBetween('appointment_date', [$month, $monthEnd])
                ->sum('value');

            $labels[] = $label;
            $data[] = (float) $revenue;
            $totalRevenue += $revenue;
        }

        return [
            'chart_labels' => $labels,
            'chart_data' => $data,
            'revenue' => $totalRevenue,
        ];
    }

    public function getAllSystemUsers(): array
    {
        return User::with(['admin', 'doctor', 'receptionist'])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->photo,
                    'role' => $user->admin ? 'admin' :
                            ($user->doctor ? 'doctor' :
                            ($user->receptionist ? 'receptionist' : 'patient'))
                ];
            })
            ->toArray();
    }
}