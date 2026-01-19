import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { CalendarPlus, ClipboardPlus, Users } from 'lucide-react';
import DashboardCard from '@/components/dashboard/dashboard-card';
import DashboardHeader from '@/components/dashboard/dashboard-header';
import { ModalAppointment, ModalProvider } from '@/components/modals';
import {Dialog} from "@/components/ui/dialog"
import { useState } from 'react';
import StatusAppointmentsSummary from '@/components/dashboard/doctor/status-appointments-summary';
import DashboardSchedule from '@/components/dashboard/dashboard-schedule/dashboard-schedule';
import DashboardShortcut from '@/components/dashboard/doctor/dashboard-shortcut';
import DashboardPatientsList from '@/components/dashboard/doctor/dashboard-patients/patients-list-wrapper';
import {User} from '@/components/modals';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/receptionist-dashboard',
    },
];

interface ReceptionistDashboardProps {
    user: User;
    patients: User[];
    doctors: User[];
    daily_summary: {
        appointments_today: number;
        completed_today: number;
        pending_today: number;
        cancelled_today: number;
    };
    weekly_appointments: Array<{
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
}

export default function ReceptionistDashboard({ user, daily_summary, weekly_appointments, patients, doctors }: ReceptionistDashboardProps) {
    const [open, setOpen] = useState(false);
    return (
        <AppLayout breadcrumbs={breadcrumbs} userRole="receptionist">
            <Head title="Recepcionist Dashboard" />
            <div className="flex h-full flex-1 gap-6 rounded-xl p-6 pr-10 pl-10 overflow-x-auto">
                {/* Coluna principal à esquerda */}
                <div className="gap-4 grid grid-cols-1 lg:grid-cols-[1fr_330px] ">
                    <div className='flex flex-col gap-4'>
                        <DashboardHeader userName={user.name} />
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5 h-fit">
                            <DashboardShortcut shortcuts={[
                                { icon: ClipboardPlus, title: 'Visualizar Consultas', route: '/receptionist/consultations-list' },
                                { icon: Users, title: 'Gerenciar Pacientes', route: '/receptionist/patients' },
                            ]} />
                            <DashboardCard
                                title="Agendar Consulta"
                                description="Agende uma nova consulta para um paciente."
                                color="#0052cc"
                                icon={CalendarPlus}
                                onClick={() => setOpen(true)}
                            />
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <StatusAppointmentsSummary title="Status das Consultas da Semana" completed={daily_summary.completed_today} scheduled={daily_summary.appointments_today} canceled={daily_summary.cancelled_today} />
                            <DashboardSchedule events={weekly_appointments
                                .filter(appointment => appointment.status !== 'canceled' )
                                .map(appointment => ({
                                    id: appointment.id,
                                    date: appointment.appointment_date,
                                    time: new Date(appointment.appointment_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                    title: `Consulta: ${appointment.patient.name} - ${appointment.doctor.user.name}`,
                                }))
                            } />
                        </div>
                    </div>
                    {/* Coluna fixa à direita */}
                    <div>
                        <DashboardPatientsList 
                            isDoctors={false}
                            title='Pacientes'
                            patients={[
                                { name: 'Lucas Ferreira', birth_date: '1990-05-15', email: 'lucas.ferreira@example.com' },
                                { name: 'Mariana Silva', birth_date: '1985-08-22', email: 'mariana.silva@example.com' },
                                { name: 'Pedro Gomes', birth_date: '1978-11-30', email: 'pedro.gomes@example.com' },
                                { name: 'Ana Costa', birth_date: '1992-03-10', email: 'ana.costa@example.com' },
                                { name: 'Rafael Souza', birth_date: '1988-07-25', email: 'rafael.souza@example.com' },
                            ]} 
                        />
                    </div>
                </div>

            </div>
            <ModalProvider>
                <Dialog open={open} onOpenChange={setOpen}>
                    <ModalAppointment
                        receptionist={user}
                        patients={patients}
                        doctors={doctors}
                    />
                </Dialog>
            </ModalProvider>
        </AppLayout>
    );
}
