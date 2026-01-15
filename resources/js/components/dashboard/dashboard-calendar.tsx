import { useState } from "react";
import DashboardAppointment from "./dashboard-appointment";

type Appointment = {
    id: number;
    appointment_date: string;
    patient: {
        name: string;
    };
    doctor?: {
        user: {
            name: string;
        };
    };
    status: string;
}

type DashboardCalendarProps = {
    title: string;
    appointments?: Appointment[];
}

function getCurrentWeekDays() {
    const today = new Date();
    const dayOfWeek = today.getDay();
    const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;

    const days = [];

    for (let i = 0; i < 7; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + mondayOffset + i);

        const dayNumber = date.getDate();
        const weekDay = date.toLocaleDateString('pt-BR', { weekday: 'short' });
        const month = date.toLocaleDateString('pt-BR', { month: 'short' });
        const isToday = date.toDateString() === today.toDateString();
        const fullDate = date;

        days.push({ dayNumber, weekDay, isToday, month, fullDate });
    }

    return days;
}

function isSameDay(date1: Date, date2: Date): boolean {
    return date1.getDate() === date2.getDate() && date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
}

export default function DashboardCalendar({ title, appointments = [] }: DashboardCalendarProps) {
    const weekDays = getCurrentWeekDays();
    const [selectedDay, setSelectedDay] = useState<Date | null>(new Date());

    const getAppointmentsForDay = (day: Date | null) => {
        if (!day) return [];
        
        return appointments.filter(appointment => {
            const appointmentDate = new Date(appointment.appointment_date);
            return isSameDay(appointmentDate, day);
        });
    };

    const handleDayClick = (date: Date) => {
        setSelectedDay(date);
    };

    const appointmentsToShow = getAppointmentsForDay(selectedDay);

    return (
        <div className="flex flex-col col-span-1 overflow-hidden rounded-xl" style={{ backgroundColor: '#C0C4CE' }}>
            <div className='flex flex-col justify-center h-full'>
                <div className='flex p-2 pl-3 rounded-xl justify-between' style={{ backgroundColor: '#030D29' }}>
                    <p className='flex text-xl text-white font-bold'>{title}</p>
                    <p className='flex text-xl text-white font-light'>{weekDays[0].month.toUpperCase()}</p>
                </div>
                <div className='flex flex-col justify-center items-center h-full w-full'>
                    <div className='flex w-full h-full gap-2 p-2 rounded-b-2xl'>
                        {weekDays.map((item, idx) => (
                            <div key={idx} className="flex flex-col items-center gap-auto h-fit w-full">
                                <div 
                                    className="flex flex-col items-center gap-auto p-2 rounded-3xl cursor-pointer transition-colors" 
                                    onClick={() => handleDayClick(item.fullDate)}
                                    style={{ 
                                        backgroundColor: selectedDay && isSameDay(selectedDay, item.fullDate) 
                                            ? '#030D29' 
                                            : item.isToday 
                                            ? '#D3D3D3' 
                                            : 'transparent' 
                                    }}
                                >
                                    <span 
                                        className={`text-sm capitalize ${
                                            selectedDay && isSameDay(selectedDay, item.fullDate) ? 'text-white' : 'text-black'
                                        }`}
                                    >
                                        {item.weekDay}
                                    </span>
                                    <span 
                                        className={`text-2xl font-semibold ${
                                            selectedDay && isSameDay(selectedDay, item.fullDate) ? 'text-white' : 'text-black'
                                        }`}
                                    >
                                        {item.dayNumber}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                    
                    <div className='flex flex-col w-full h-full justify-self-start justify-center gap-0 rounded-b-2xl border-t-1 border-b-1 p-2' style={{ backgroundColor: '#F7F2EB' }}>
                        {appointmentsToShow.length > 0 ? (
                            appointmentsToShow.map((appointment: Appointment, index: number) => {
                                const appointmentDate = new Date(appointment.appointment_date);
                                const colors = ['D63384', '0D6EFD', 'FFC107', 'FD7E14', '198754'];
                                const color = colors[index % colors.length];
                                
                                // Para dashboard da recepcionista: mostrar paciente e doutor
                                // Para dashboard do doutor: mostrar apenas paciente
                                const title = appointment.doctor 
                                    ? `${appointment.patient.name} - Dr. ${appointment.doctor.user.name}`
                                    : `Consulta ${appointment.patient.name}`;
                                
                                return (
                                    <DashboardAppointment
                                        key={appointment.id}
                                        time={appointmentDate.toLocaleTimeString('pt-BR', { 
                                            hour: '2-digit', 
                                            minute: '2-digit' 
                                        })}
                                        title={title}
                                        color={color}
                                    />
                                );
                            })
                        ) : (
                            <div className="flex justify-center items-center h-full">
                                <p className="text-gray-500">Nenhuma consulta agendada para este dia</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}