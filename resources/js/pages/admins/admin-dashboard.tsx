import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import DashboardMonthlySales from '@/components/dashboard/admin/dashboard-monthly-sales';
import DashboardHeader from '@/components/dashboard/dashboard-header';
import ManageCard from '@/components/dashboard/admin/dashboard-manage-card';
import DashboardGoal from '@/components/dashboard/admin/dashboard-goal';
import DashboardPatientsTrend from '@/components/dashboard/admin/dashboard-patients-trend';
import DashboardSchedule from '@/components/dashboard/dashboard-schedule/dashboard-schedule';
import DashboardEmployees from '@/components/dashboard/admin/dashboard-employees/employees-wrapper';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin-dashboard',
    },
];

interface AdminDashboardProps {
    user: {
        name: string;
        avatar: string;
        role: string;
        is_master: boolean;
    };
    stats: {
        total_admins: number;
        total_doctors: number;
        total_receptionists: number;
        total_users: number;
    };
    completed_consultations: {
        labels: string[];
        data: number[];
        total_value: number;
    };
    semester_revenue: {
        revenue: number;
        chart_data: number[];
        chart_labels: string[];
    }
    monthly_revenue: {
        current_month: {
            revenue: number;
            consultations_count: number;
            month_name: string;
            formatted_revenue: string;
        };
        previous_month: {
            revenue: number;
            consultations_count: number;
            month_name: string;
            formatted_revenue: string;
        };
        comparison: {
            revenue_difference: number;
            revenue_growth_percentage: number;
            consultations_difference: number;
            formatted_difference: string;
        };
    };
}

export default function AdminDashboard({ 
    user,
    monthly_revenue,
    semester_revenue,
}: AdminDashboardProps) {
    return (
    <AppLayout breadcrumbs={breadcrumbs} userRole="admin">
        <Head title="Admin Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-5 rounded-xl p-4 md:p-6 md:pr-10 md:pl-10 overflow-x-auto">
                <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 min-h-screen p-4 md:p-0">
                    <div className="flex flex-col gap-4">
                        <DashboardHeader userName={user.name} />
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5 h-fit">
                            <div className='flex flex-col h-fit gap-5'>
                                <ManageCard title='Gerencie os usuários' description='Adicione, edite ou remova usuários do sistema' routes={["/admin/admins", "/admin/doctors", "/admin/receptionists"]}
                                />
                                <DashboardGoal 
                                    goalAmount={5000} 
                                    currentAmount={monthly_revenue?.current_month?.revenue || 0}
                                    previousAmount={monthly_revenue?.previous_month?.revenue || 0}
                                />
                            </div>
                            <DashboardMonthlySales
                                title="Receita do último semestre"
                                labels={semester_revenue?.chart_labels || ['Sem dados']}
                                data={semester_revenue?.chart_data || [0]}
                                currency={true}
                                currentTotal={semester_revenue?.revenue || 0} 
                            />
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <DashboardSchedule events={[
                                { id: 1, time: '09:00', title: 'Consulta Dr. Silva', description: 'Paciente Maria Costa', date: '2026-01-20' },
                                { id: 2, time: '10:30', title: 'Consulta Dra. Santos', description: 'Paciente João Oliveira', date: '2026-01-20' },
                                { id: 3, time: '14:00', title: 'Consulta Dr. Lima', description: 'Paciente Ana Souza', date: '2026-01-20' },
                                { id: 4, time: '15:30', title: 'Consulta Dra. Pereira', description: 'Paciente Carlos Mendes', date: '2026-01-20' },
                                { id: 5, time: '16:00', title: 'Consulta Dr. Costa', description: 'Paciente Paula Alves', date: '2026-01-20' },
                            ]} />
                            <DashboardPatientsTrend 
                                title="Evolução de pacientes"
                                labels={['Ago', 'Set', 'Out', 'Nov', 'Dez', 'Jan']}
                                data={[45, 52, 58, 63, 71, 78]}
                                totalPatients={78}
                            />
                        </div>
                    </div>
                    <DashboardEmployees users={[
                        { name: 'Lucas Ferreira', role: 'ADMINISTRADOR', avatar: '/avatars/lucas.png' },
                        { name: 'Mariana Silva', role: 'RECEPCIONISTA', avatar: '/avatars/mariana.png' },
                        { name: 'Pedro Gomes', role: 'MEDICO', avatar: '/avatars/pedro.png' },
                        { name: 'Ana Costa', role: 'RECEPCIONISTA', avatar: '/avatars/ana.png' },
                        { name: 'Rafael Souza', role: 'MEDICO', avatar: '/avatars/rafael.png' },
                    ]} />
                </div>
            </div>
        </AppLayout>
    );
}