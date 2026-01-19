<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Actions\Dashboard\GetAdminDashboardData;
use App\Application\Actions\Dashboard\GetDoctorDashboardData;
use App\Application\Actions\Dashboard\GetReceptionistDashboardData;
use App\Application\Actions\Dashboard\GetConsultationsList;
use App\Infrastructure\Services\DashboardStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private GetAdminDashboardData $getAdminDashboardData,
        private GetDoctorDashboardData $getDoctorDashboardData,
        private GetReceptionistDashboardData $getReceptionistDashboardData,
        private GetConsultationsList $getConsultationsList,
        private DashboardStatisticsService $statisticsService
    ) {}

    public function index(): RedirectResponse
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

    public function adminDashboard(): Response
    {
        $user = auth()->user();
        $dashboardData = $this->getAdminDashboardData->execute($user);

        return Inertia::render('dashboards/admin-dashboard', array_merge(
            $dashboardData, 
            ['userRole' => 'admin']
        ));
    }

    public function doctorDashboard(): Response
    {
        $user = auth()->user();
        $dashboardData = $this->getDoctorDashboardData->execute($user);

        return Inertia::render('dashboards/doctor-dashboard', array_merge(
            $dashboardData, 
            ['userRole' => 'doctor']
        ));
    }

    public function receptionistDashboard(): Response
    {
        $user = auth()->user();
        $dashboardData = $this->getReceptionistDashboardData->execute($user);

        return Inertia::render('dashboards/receptionist-dashboard', array_merge(
            $dashboardData, 
            ['userRole' => 'receptionist']
        ));
    }

    public function getLastFiveCompletedConsultationsApi(): JsonResponse
    {
        $data = $this->statisticsService->getLastFiveCompletedConsultations();
        
        return response()->json([
            'success' => true,
            'data' => $data['consultations'],
            'total_value' => $data['total_value'],
        ]);
    }

    public function getMonthlyRevenueApi(): JsonResponse
    {
        $data = $this->statisticsService->calculateMonthlyRevenue();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function consultationsList(Request $request): Response
    {
        $search = $request->get('search', '');
        $consultations = $this->getConsultationsList->execute($search, 10);

        return Inertia::render('receptionists/consultations-list', [
            'consultations' => $consultations,
            'filters' => ['search' => $search],
            'userRole' => 'receptionist',
        ]);
    }
}