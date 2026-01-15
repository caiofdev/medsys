<?php
// filepath: app/Infrastructure/Services/DashboardStatisticsService.php

namespace App\Infrastructure\Services;

class DashboardStatisticsService
{
    public function calculateTotalUsers(array $counts): int
    {
        return ($counts['total_admins'] ?? 0) 
             + ($counts['total_doctors'] ?? 0) 
             + ($counts['total_receptionists'] ?? 0);
    }

    public function calculateMonthlyRevenueGrowth(array $revenueData): array
    {
        $currentRevenue = $revenueData['current_revenue'];
        $previousRevenue = $revenueData['previous_revenue'];
        $currentCount = $revenueData['current_count'];
        $previousCount = $revenueData['previous_count'];

        $revenueGrowth = $previousRevenue > 0 
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        return [
            'current_month' => [
                'revenue' => $currentRevenue,
                'consultations_count' => $currentCount,
                'month_name' => $revenueData['current_month_name'],
                'formatted_revenue' => $this->formatCurrency($currentRevenue),
            ],
            'previous_month' => [
                'revenue' => $previousRevenue,
                'consultations_count' => $previousCount,
                'month_name' => $revenueData['previous_month_name'],
                'formatted_revenue' => $this->formatCurrency($previousRevenue),
            ],
            'comparison' => [
                'revenue_difference' => $currentRevenue - $previousRevenue,
                'revenue_growth_percentage' => round($revenueGrowth, 2),
                'consultations_difference' => $currentCount - $previousCount,
                'formatted_difference' => $this->formatCurrency($currentRevenue - $previousRevenue),
            ],
        ];
    }

    public function prepareConsultationsChartData(array $consultations): array
    {
        $data = [];
        $labels = [];
        $totalValue = 0;

        foreach ($consultations as $consultation) {
            $data[] = floatval($consultation['value']);
            $labels[] = 'Consulta ' . $consultation['id'];
            $totalValue += floatval($consultation['value']);
        }

        return [
            'data' => $data,
            'labels' => $labels,
            'total_value' => $totalValue,
        ];
    }

    public function prepareMonthlyRevenueChartData(array $monthlyRevenue): array
    {
        return [
            'data' => [
                floatval($monthlyRevenue['previous_month']['revenue']),
                floatval($monthlyRevenue['current_month']['revenue'])
            ],
            'labels' => [
                $monthlyRevenue['previous_month']['month_name'],
                $monthlyRevenue['current_month']['month_name']
            ],
        ];
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}