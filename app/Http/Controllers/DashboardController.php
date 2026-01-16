<?php

namespace App\Http\Controllers;

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
        
        $lastConsultationsData = $this->getMonthlyCompletedConsultations();
        $monthlyRevenueData = $this->getMonthlyRevenue();
        
        $consultationsChartData = [];
        $consultationsLabels = [];
        if (isset($lastConsultationsData['data'])) {
            foreach ($lastConsultationsData['data'] as $consultation) {
                $consultationsChartData[] = floatval($consultation['value']);
                $consultationsLabels[] = 'Consulta ' . $consultation['id'];
            }
        }
        
        $semesterRevenueValues = [];
        $semesterRevenueLabels = [];
        $semesterTotalRevenue = 0;
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = \App\Models\Appointment::where('status', 'completed')
                ->whereBetween('appointment_date', [$monthStart, $monthEnd])
                ->sum('value');
            
                $semesterRevenueValues[] = floatval($revenue);
                $semesterRevenueLabels[] = ucfirst($month->locale('pt_BR')->isoFormat('MMM'));
                $semesterTotalRevenue += floatval($revenue);
        }
        
        $dashboardData = [
            'user' => [
                'name' => $user->name,
                'avatar' => $user->photo ? asset('storage/' . $user->photo) : '/admin-pic.png',
                'role' => 'ADMINISTRADOR',
                'is_master' => $user->admin->is_master ?? false,
            ],
            'stats' => [
                'total_admins' => \App\Models\Admin::count(),
                'total_doctors' => \App\Models\Doctor::count(),
                'total_receptionists' => \App\Models\Receptionist::count(),
                'total_users' => \App\Models\User::count(),
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
            ],
            'semester_revenue' =>[
                'chart_data' => $semesterRevenueValues,
                'chart_labels' => $semesterRevenueLabels,
                'revenue' => $semesterTotalRevenue,

            ]
        ];

        return Inertia::render('admins/admin-dashboard', array_merge($dashboardData, ['userRole' => 'admin']));
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

        return Inertia::render('doctors/doctor-dashboard', array_merge($dashboardData, ['userRole' => 'doctor']));
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
                'appointments_today' => \App\Models\Appointment::whereDate('appointment_date', today())->count(),
                'completed_today' => \App\Models\Appointment::whereDate('appointment_date', today())->where('status', 'completed')->count(),
                'pending_today' => \App\Models\Appointment::whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
                'cancelled_today' => \App\Models\Appointment::whereDate('appointment_date', today())->where('status', 'canceled')->count(),
            ],
            'weekly_appointments' => \App\Models\Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->with(['patient', 'doctor.user'])
                ->orderBy('appointment_date')
                ->get(),
        ];

        return Inertia::render('receptionists/receptionist-dashboard', array_merge($dashboardData, ['userRole' => 'receptionist']));
    }

    private function getMonthlyCompletedConsultations()
    {
        $currentMonth = now()->startOfMonth();
        
        $consultations = \App\Models\Consultation::with(['appointment' => function($query) {
            $query->with(['doctor.user', 'patient'])
                  ->where('status', 'completed');
        }])
        ->whereHas('appointment', function($query) use ($currentMonth) {
            $query->where('status', 'completed')
                  ->whereBetween('appointment_date', [$currentMonth, now()]);
        })
        ->latest('created_at')
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
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $previousMonthStart = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        $currentMonthRevenue = \App\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('value');

        $previousMonthRevenue = \App\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$previousMonthStart, $previousMonthEnd])
            ->sum('value');

        $currentMonthConsultations = \App\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $previousMonthConsultations = \App\Models\Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$previousMonthStart, $previousMonthEnd])
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
                    'month_name' => now()->format('F Y'),
                    'formatted_revenue' => 'R$ ' . number_format($currentMonthRevenue, 2, ',', '.')
                ],
                'previous_month' => [
                    'revenue' => $previousMonthRevenue,
                    'consultations_count' => $previousMonthConsultations,
                    'month_name' => now()->subMonth()->format('F Y'),
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

    public function getMonthlyCompletedConsultationsApi()
    {
        $data = $this->getMonthlyCompletedConsultations();
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
        
        $consultations = \App\Models\Consultation::with(['appointment' => function($query) {
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
        ->paginate(9)
        ->withQueryString();

        return Inertia::render('receptionists/consultations-list', [
            'consultations' => $consultations,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }
}