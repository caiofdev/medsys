import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import DashboardHeader from '@/components/dashboard/dashboard-header';
import DashboardSchedule from '@/components/dashboard/dashboard-schedule/dashboard-schedule';
import { ClipboardList, Play } from 'lucide-react';
import DashboardShortcut from '@/components/dashboard/doctor/dashboard-shortcut';
import DashboardConsultationsSummary from '@/components/dashboard/doctor/dashboard-consultations-summary';
import DashboardPatientsList from '@/components/dashboard/doctor/dashboard-patients/patients-list-wrapper';
import StatusAppointmentsSummary from '@/components/dashboard/doctor/status-appointments-summary';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/doctor-dashboard',
    },
];

interface DoctorDashboardProps {
    user: {
        name: string;
        avatar: string;
        role: string;
        crm: string;
        specialty: string;
    };
    appointments: {
        today: number;
        week: number;
        month: number;
    };
    upcoming_appointments: {
        id: number;
        time: string;
        title: string;
        description: string;
    }[];
}

export default function DoctorDashboard({ user,  upcoming_appointments }: DoctorDashboardProps) {
    return (
    <AppLayout breadcrumbs={breadcrumbs} userRole="doctor">
        <Head title="Doctor Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-5 rounded-xl p-4 md:p-6 md:pr-10 md:pl-10 overflow-x-auto">
                <div className="grid grid-cols-1 lg:grid-cols-[1fr_330px] gap-5 min-h-screen p-4 md:p-0">
                    <div className="flex flex-col gap-4">
                        <DashboardHeader userName={user.name} />
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5 h-fit">
                                <DashboardShortcut shortcuts={[
                                    { icon: Play, title: 'Iniciar Consulta', route: '/doctor/start-consultation' },
                                    { icon: ClipboardList, title: 'Ver Prontuários', route: '/doctor/medical-record' },
                                ]} />
                                <DashboardConsultationsSummary
                                    todayConsultations={3}
                                    weekConsultations={11}
                                    monthConsultations={41}
                                />
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <DashboardSchedule events={upcoming_appointments} />
                            <StatusAppointmentsSummary  completed={5} scheduled={10} canceled={2} />
                        </div>
                    </div>
                    <DashboardPatientsList patients={[
                        { name: 'Lucas Ferreira', birth_date: '1990-05-15', email: 'lucas.ferreira@example.com' },
                        { name: 'Mariana Silva', birth_date: '1985-08-22', email: 'mariana.silva@example.com' },
                        { name: 'Pedro Gomes', birth_date: '1978-11-30', email: 'pedro.gomes@example.com' },
                        { name: 'Ana Costa', birth_date: '1992-03-10', email: 'ana.costa@example.com' },
                        { name: 'Rafael Souza', birth_date: '1988-07-25', email: 'rafael.souza@example.com' },
                    ]} />
                </div>
            </div>
        </AppLayout>
    );
}