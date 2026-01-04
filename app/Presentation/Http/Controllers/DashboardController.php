<?php

namespace App\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $userType = $user->getUserType();

        return match($userType) {
            'admin' => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            default => redirect()->route('login')->with('error', 'Usuário não identificado.'),
        };
    }

    public function adminDashboard()
    {
        $user = auth()->user();
        
        // Buscar dados dos relatórios diretamente
        $lastConsultationsData = $this->getLastFiveCompletedConsultations();
        $monthlyRevenueData = $this->getMonthlyRevenue();
        
        // Preparar dados das últimas consultas para o gráfico
        $consultationsChartData = [];
        $consultationsLabels = [];
        if (isset($lastConsultationsData['data'])) {
            foreach ($lastConsultationsData['data'] as $consultation) {
                $consultationsChartData[] = floatval($consultation['value']);
                $consultationsLabels[] = 'Consulta ' . $consultation['id'];
            }
        }
        
        // Preparar dados de receita mensal
        $monthlyRevenueValues = [];
        if (isset($monthlyRevenueData['data'])) {
            $monthlyRevenueValues = [
                floatval($monthlyRevenueData['data']['previous_month']['revenue']),
                floatval($monthlyRevenueData['data']['current_month']['revenue'])
            ];
        }
        
        $dashboardData = [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/admin-pic.png',
                'role' => 'ADMINISTRADOR',
                'is_master' => $user->admin->is_master ?? false,
            ],
            'stats' => [
                'total_admins' => \App\Domain\Models\Admin::count(),
                'total_doctors' => \App\Domain\Models\Doctor::count(),
                'total_receptionists' => \App\Domain\Models\Receptionist::count(),
                'total_users' => \App\Domain\Models\User::count(),
            ],
            'recent_activities' => [],
            'completed_consultations' => [
                'labels' => $consultationsLabels,
                'data' => $consultationsChartData,
                'total_value' => $lastConsultationsData['total_value'] ?? 0
            ],
            'monthly_revenue' => [
                'current_month' => $monthlyRevenueData['data']['current_month'] ?? null,
                'previous_month' => $monthlyRevenueData['data']['previous_month'] ?? null,
                'comparison' => $monthlyRevenueData['data']['comparison'] ?? null,
                'chart_data' => $monthlyRevenueValues,
                'chart_labels' => [
                    $monthlyRevenueData['data']['previous_month']['month_name'] ?? 'Mês Anterior',
                    $monthlyRevenueData['data']['current_month']['month_name'] ?? 'Mês Atual'
                ]
            ]
        ];

        return Inertia::render('dashboards/admin-dashboard', array_merge($dashboardData, ['userRole' => 'admin']));
    }

    public function doctorDashboard()
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        $dashboardData = [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/doctor-pic.png',
                'role' => 'DOUTOR',
                'crm' => $doctor->crm ?? null,
                'specialty' => $doctor->specialty->name ?? 'Não definido',
            ],
            'appointments' => [
                'today' => $doctor->appointments()->whereDate('appointment_date', today())->count(),
                'week' => $doctor->appointments()->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'month' => $doctor->appointments()->whereMonth('appointment_date', now()->month)->count(),
            ],
            'upcoming_appointments' => $doctor->appointments()
                ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->with('patient')
                ->orderBy('appointment_date')
                ->get(),
        ];

        return Inertia::render('dashboards/doctor-dashboard', array_merge($dashboardData, ['userRole' => 'doctor']));
    }

    public function receptionistDashboard()
    {
        $user = auth()->user();
        $receptionist = $user->receptionist;
        
        $dashboardData = [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/recepcionist-pic.png',
                'role' => 'RECEPCIONISTA',
                'registration_number' => $receptionist->registration_number ?? null,
            ],
            'daily_summary' => [
                'appointments_today' => \App\Domain\Models\Appointment::whereDate('appointment_date', today())->count(),
                'completed_today' => \App\Domain\Models\Appointment::whereDate('appointment_date', today())->where('status', 'completed')->count(),
                'pending_today' => \App\Domain\Models\Appointment::whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
                'cancelled_today' => \App\Domain\Models\Appointment::whereDate('appointment_date', today())->where('status', 'canceled')->count(),
            ],
            'weekly_appointments' => \App\Domain\Models\Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->with(['patient', 'doctor.user'])
                ->orderBy('appointment_date')
                ->get(),
        ];

        return Inertia::render('dashboards/receptionist-dashboard', array_merge($dashboardData, ['userRole' => 'receptionist']));
    }

    private function getLastFiveCompletedConsultations()
    {
        $consultations = \App\Domain\Models\Consultation::with(['appointment' => function($query) {
            $query->with(['doctor.user', 'patient'])
                  ->where('status', 'completed');
        }])
        ->whereHas('appointment', function($query) {
            $query->where('status', 'completed');
        })
        ->latest('created_at')
        ->take(5)
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
        });

        return [
            'success' => true,
            'data' => $consultations,
            'total_value' => $consultations->sum('value')
        ];
    }

    private function getMonthlyRevenue()
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        $currentMonthRevenue = \App\Domain\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$currentMonth, now()])
            ->sum('value');

        $previousMonthRevenue = \App\Domain\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$previousMonth, $previousMonthEnd])
            ->sum('value');

        $currentMonthConsultations = \App\Domain\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$currentMonth, now()])
            ->count();

        $previousMonthConsultations = \App\Domain\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$previousMonth, $previousMonthEnd])
            ->count();

        $revenueGrowth = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;

        return [
            'success' => true,
            'data' => [
                'current_month' => [
                    'revenue' => $currentMonthRevenue,
                    'consultations_count' => $currentMonthConsultations,
                    'month_name' => $currentMonth->format('F Y'),
                    'formatted_revenue' => 'R$ ' . number_format($currentMonthRevenue, 2, ',', '.')
                ],
                'previous_month' => [
                    'revenue' => $previousMonthRevenue,
                    'consultations_count' => $previousMonthConsultations,
                    'month_name' => $previousMonth->format('F Y'),
                    'formatted_revenue' => 'R$ ' . number_format($previousMonthRevenue, 2, ',', '.')
                ],
                'comparison' => [
                    'revenue_difference' => $currentMonthRevenue - $previousMonthRevenue,
                    'revenue_growth_percentage' => round($revenueGrowth, 2),
                    'consultations_difference' => $currentMonthConsultations - $previousMonthConsultations,
                    'formatted_difference' => 'R$ ' . number_format($currentMonthRevenue - $previousMonthRevenue, 2, ',', '.')
                ]
            ]
        ];
    }

    public function getLastFiveCompletedConsultationsApi()
    {
        $data = $this->getLastFiveCompletedConsultations();
        return response()->json($data);
    }

    public function getMonthlyRevenueApi()
    {
        $data = $this->getMonthlyRevenue();
        return response()->json($data);
    }

    public function consultationsList(Request $request)
    {
        $search = $request->get('search', '');
        
        $consultations = \App\Domain\Models\Consultation::with(['appointment' => function($query) {
            $query->with(['doctor.user', 'patient', 'receptionist.user']);
        }])
        ->when($search, function ($query) use ($search) {
            $query->whereHas('appointment.patient', function($patientQuery) use ($search) {
                $patientQuery->where('name', 'like', "%{$search}%");
            })->orWhereHas('appointment.doctor.user', function($doctorQuery) use ($search) {
                $doctorQuery->where('name', 'like', "%{$search}%");
            })->orWhere('diagnosis', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return Inertia::render('receptionists/consultations-list', [
            'consultations' => $consultations,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }
}