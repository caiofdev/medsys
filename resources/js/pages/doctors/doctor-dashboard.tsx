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
    upcoming_appointments: Array<{
        id: number;
        appointment_date: string;
        patient: {
            name: string;
        };
        doctor: {
            user: {
                name: string;
            };
        };
        status: string;
    }>;
    patients: Array<{
        id: number;
        name: string;
        birth_date: string;
        email: string;
    }>;
    consultations_summary: {
        today: number;
        week: number;
        month: number;
    };
    weekly_appointments_status: {
        completed: number;
        scheduled: number;
        canceled: number;
    };
}

export default function DoctorDashboard({ user,  upcoming_appointments, patients = [], consultations_summary, weekly_appointments_status }: DoctorDashboardProps) {
    return (
    <AppLayout breadcrumbs={breadcrumbs} userRole="doctor">
        <Head title="Doctor Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-5 rounded-xl p-4 md:p-6 md:pr-10 md:pl-10 overflow-x-auto">
                <div className="grid grid-cols-1 lg:grid-cols-[1fr_330px] gap-5 min-h-screen p-4 md:p-0">
                    <div className="flex flex-col gap-4">
                        <DashboardHeader userName={user.name} />
                        <div className="grid grid-cols-1 xl:grid-cols-2 gap-5 h-fit">
                                <DashboardShortcut shortcuts={[
                                    { icon: Play, title: 'Iniciar Consulta', route: '/doctor/start-consultation' },
                                    { icon: ClipboardList, title: 'Ver Prontuários', route: '/doctor/medical-record' },
                                ]} />
                                <DashboardConsultationsSummary
                                    todayConsultations={consultations_summary.today}
                                    weekConsultations={consultations_summary.week}
                                    monthConsultations={consultations_summary.month}
                                />
                        </div>
                        <div className="grid grid-cols-1 xl:grid-cols-2 gap-5">
                            <DashboardSchedule events={upcoming_appointments
                                .filter(appointment => appointment.status !== 'canceled' )
                                .map(appointment => ({
                                    id: appointment.id,
                                    date: appointment.appointment_date,
                                    time: new Date(appointment.appointment_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                    title: `Consulta: ${appointment.patient.name}`,
                                }))
                            } />
                            <StatusAppointmentsSummary title="Status das Consultas da Semana" completed={weekly_appointments_status.completed} scheduled={weekly_appointments_status.scheduled} canceled={weekly_appointments_status.canceled} />
                        </div>
                    </div>
                    <DashboardPatientsList 
                        isDoctors={true}
                        title="Seus Pacientes"
                        patients={patients}
                    />
                </div>
            </div>
        </AppLayout>
    );
}