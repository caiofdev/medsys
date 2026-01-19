import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { SelectField } from '@/components/select-field';
import { CalendarClock, Check, CircleDollarSign, CircleDot, ClipboardList, Clock, IdCard, Pause, Phone,  Play,  RotateCcw,  User,  UserRoundPlus, VenusAndMars } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/doctor/dashboard',
    },
    {
        title: 'Iniciar Atendimento',
        href: '/doctors/start-consultation',
    },
];

interface Patient {
    id: number;
    name: string;
    email: string;
    cpf: string;
    phone: string;
    birth_date: string;
    gender: string;
    emergency_contact: string;
    medical_history: string;
    avatar?: string;
}

interface Appointment {
    id: number;
    appointment_date: string;
    status: string;
    value: number | string;
    patient: Patient;
}

// interface ConsultationData {
//     symptoms: string;
//     diagnosis: string;
//     notes: string;
// }

interface StartConsultationProps {
    appointments: Appointment[];
    patients: Patient[];
    userRole: 'admin' | 'doctor' | 'receptionist' | 'patient';
    flash?: {
        success?: string;
        error?: string;
    };
}



function Timer() {
    const [time, setTime] = useState(0);
    const [isRunning, setIsRunning] = useState(false);

    useEffect(() => {
        let interval: NodeJS.Timeout;
        if (isRunning) {
            interval = setInterval(() => {
                setTime(time => time + 1);
            }, 1000);
        }
        return () => clearInterval(interval);
    }, [isRunning]);

    const formatTime = (seconds: number) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    const handleStart = () => setIsRunning(true);
    const handlePause = () => setIsRunning(false);
    const handleReset = () => {
        setIsRunning(false);
        setTime(0);
    };

    return (
        <div className="text-center space-y-6">
            <div className="relative">
                <div className="text-5xl font-mono font-bold text-foreground bg-digital-blue-200 p-6 rounded-radius shadow-lg border border-border ">
                    {formatTime(time)}
                </div>
            </div>
            <div className="flex flex-col gap-3">
                <Button 
                    onClick={handleStart} 
                    disabled={isRunning}
                    title='Iniciar'
                    className="text-lighttext disabled:opacity-50 py-3 px-5 text-base font-semibold shadow-lg hover:opacity-90 transition-all duration-300 cursor-pointer bg-foreground"
                >
                    {isRunning ? 'Em Andamento' : 'Iniciar'}
                    {!isRunning && <Play />}
                </Button>
                <div className="flex gap-2">
                    <Button 
                        onClick={handlePause} 
                        disabled={!isRunning}
                        variant="outline"
                        title='Pausar'
                        className="flex-1 border border-border text-foreground hover:border-foreground/50 hover:bg-digital-blue-100 transition-all duration-300 cursor-pointer"
                    >   
                        <Pause />
                        Pausar
                    </Button>
                    <Button 
                        onClick={handleReset}
                        variant="outline"
                        title='Resetar'
                        className="flex-1 border border-border text-foreground hover:border-foreground/50 hover:bg-digital-blue-100 transition-all duration-300 cursor-pointer"
                    >
                        <RotateCcw />
                        Resetar
                    </Button>
                </div>
            </div>
        </div>
    );
}

const formatCurrency = (value: number | string): string => {
    const numValue = Number(value || 0);
    return numValue.toFixed(2);
};

export default function StartConsultation({ appointments, patients, userRole, flash }: StartConsultationProps) {

    const [selectedPatient, setSelectedPatient] = useState<string>('');
    const [selectedAppointment, setSelectedAppointment] = useState<string>('');
    const [step, setStep] = useState(1);
    const [showFlash, setShowFlash] = useState(true);
    const getInitials = useInitials();

    const { data, setData, post, processing, errors, reset } = useForm({
        appointment_id: '',
        symptoms: '',
        diagnosis: '',
        notes: '',
    });

    const selectedPatientData = patients.find(p => p.id === parseInt(selectedPatient));
    const selectedAppointmentData = appointments.find(a => a.id === parseInt(selectedAppointment));

    useEffect(() => {
        if (selectedAppointment) {
            setData('appointment_id', selectedAppointment);
        }
    }, [selectedAppointment, setData]);

    useEffect(() => {
        if (step === 1) {
            reset();
        }
    }, [step, reset]);

    useEffect(() => {
        if (flash?.success || flash?.error) {
            setShowFlash(true);
            const timer = setTimeout(() => {
                setShowFlash(false);
            }, 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const handleFinishConsultation = (e: React.FormEvent) => {
        e.preventDefault();
        
        post(route('doctor.finish-consultation'), {
            onSuccess: () => {
                setStep(1);
                setSelectedPatient('');
                setSelectedAppointment('');
                reset();
            },
            onError: (errors) => {
                console.error('Erro ao finalizar consulta:', errors);
            }
        });
    };

    const handleBackToSelection = () => {
        setStep(1);
        reset();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs} userRole={userRole}>
            <Head title="Iniciar Atendimento" />
            <div className="min-h-screen p-6 bg-background">
                <div className="max-w-5xl mx-auto space-y-8">
                    {/* Flash messages */}
                    {showFlash && flash?.success && (
                        <div className="bg-green-50 border border-green-200 text-success px-4 py-3 rounded-lg flex items-center justify-between shadow">
                            <span>{flash.success}</span>
                            <button 
                                onClick={() => setShowFlash(false)}
                                className="text-success hover:text-green-700 ml-4"
                            >
                                ×
                            </button>
                        </div>
                    )}
                    {showFlash && flash?.error && (
                        <div className="bg-red-50 border border-red-200 text-error px-4 py-3 rounded-lg flex items-center justify-between shadow">
                            <span>{flash.error}</span>
                            <button 
                                onClick={() => setShowFlash(false)}
                                className="text-error hover:text-red-700 ml-4"
                            >
                                ×
                            </button>
                        </div>
                    )}

                    {/* Seleção de paciente e agendamento */}
                    {step === 1 && (
                        <div className="flex flex-col text-center space-y-6 justify-center">
                            <h1 className="text-4xl font-bold text-digital-blue-800 mb-2">Iniciar Atendimento</h1>
                            <p className="text-lg text-gray-600">Selecione o paciente e o agendamento para começar a consulta</p>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {/* Card Selecionar Paciente */}
                                <div className="flex flex-col overflow-hidden rounded-2xl border border-border bg-digital-blue-50 shadow-md">
                                    <div className="flex items-center gap-3 p-4 rounded-t-2xl">
                                        <UserRoundPlus size={32} className="text-digital-blue-800" />
                                        <p className="text-lg text-digital-blue-800 font-bold">Selecionar Paciente</p>
                                    </div>
                                    <div className="flex flex-col h-full justify-start items-center p-6 text-darktext">
                                        <div className="w-full">
                                            <SelectField
                                                name="patient"
                                                value={selectedPatient}
                                                label="Selecione um paciente"
                                                options={[
                                                    { value: '', label: 'Selecione um paciente' },
                                                    ...patients.map((patient) => ({
                                                        value: String(patient.id),
                                                        label: patient.name,
                                                    }))
                                                ]}
                                                onChange={(e) => setSelectedPatient(e.target.value)}
                                                bgColor='bg-digital-blue-100'
                                            />
                                        </div>
                                        {selectedPatientData && (
                                            <div className="w-full p-6 mt-4 border border-border rounded-radius bg-digital-blue-100 shadow">
                                                <div className="flex items-center gap-4 mb-4">
                                                    <Avatar className="h-16 w-16">
                                                        <AvatarImage src={selectedPatientData.avatar} alt={selectedPatientData.name} />
                                                        <AvatarFallback className="bg-digital-blue-200 text-digital-blue-800 text-lg font-bold">
                                                            {getInitials(selectedPatientData.name)}
                                                        </AvatarFallback>
                                                    </Avatar>
                                                    <div className="flex flex-col w-full items-start">
                                                        <h3 className="text-xl font-bold text-digital-blue-800">{selectedPatientData.name}</h3>
                                                        <p className="text-gray-600 font-mono">{selectedPatientData.email}</p>
                                                    </div>
                                                </div>
                                                <div className="flex flex-wrap gap-4 text-sm w-full items-center justify-between">
                                                    <div className="flex items-center gap-2">
                                                        <IdCard size={20}/>
                                                        <span><strong>CPF:</strong> {selectedPatientData.cpf}</span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        <Phone size={20}/>
                                                        <span><strong>Telefone:</strong> {selectedPatientData.phone}</span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        <VenusAndMars size={20}/>
                                                        <span><strong>Gênero:</strong> {selectedPatientData.gender === 'male' ? 'Masculino' : selectedPatientData.gender === 'female' ? 'Feminino' : 'Outro'}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                                {/* Card Selecionar Agendamento */}
                                <div className="flex flex-col overflow-hidden rounded-2xl border border-border bg-digital-blue-50 shadow-md">
                                    <div className="flex items-center gap-3 p-4 bg-primary rounded-t-2xl">
                                        <CalendarClock size={32} />
                                        <p className="text-lg text-foreground font-bold">Selecionar Agendamento</p>
                                    </div>
                                    <div className="flex flex-col h-full justify-start items-center p-6 text-darktext">
                                        <div className="w-full mb-4">
                                            <SelectField
                                                name="appointment"
                                                value={selectedAppointment}
                                                label={selectedPatient ? "Selecione um agendamento" : "Primeiro selecione um paciente"}
                                                options={[
                                                    { value: '', label: selectedPatient ? 'Selecione um agendamento' : 'Primeiro selecione um paciente' },
                                                    ...appointments
                                                        .filter(apt => selectedPatient ? apt.patient.id === parseInt(selectedPatient) : false)
                                                        .map((appointment) => ({
                                                            value: String(appointment.id),
                                                            label: `${new Date(appointment.appointment_date).toLocaleString('pt-BR')} - R$ ${formatCurrency(appointment.value)}`,
                                                        }))
                                                ]}
                                                onChange={(e) => setSelectedAppointment(e.target.value)}
                                                bgColor='bg-digital-blue-100'
                                            />
                                        </div>
                                        {selectedAppointmentData && (
                                            <div className="w-full p-6 border border-border rounded-xl bg-digital-blue-100 shadow">
                                                <h3 className="text-lg font-bold text-primary mb-4 flex items-center gap-2">
                                                    Detalhes do Agendamento
                                                </h3>
                                                <div className="space-y-3 text-sm text-darktext">
                                                    <div className="flex items-center gap-2">
                                                        <Clock size={20} />
                                                        <span><strong>Data/Hora:</strong> {new Date(selectedAppointmentData.appointment_date).toLocaleString('pt-BR')}</span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        <CircleDot size={20} />
                                                        <span><strong>Status:</strong> {selectedAppointmentData.status === 'completed' ? 'Concluído' : selectedAppointmentData.status === 'scheduled' ? 'Agendado' : 'Cancelado'}</span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        <CircleDollarSign size={20} />
                                                        <span><strong>Valor:</strong> R$ {formatCurrency(selectedAppointmentData.value)}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                        <div className="w-full mt-6">
                                            <Button 
                                                onClick={() => setStep(2)}
                                                disabled={!selectedPatient || !selectedAppointment}
                                                className="w-fit p-3 px-5 text-lg font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed bg-foreground cursor-pointer hover:bg-digital-blue-900 transition-all duration-300"
                                            >
                                                Iniciar Consulta
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Consulta em andamento */}
                    {step === 2 && selectedPatientData && selectedAppointmentData && (
                        <div className="space-y-8">
                            <div className="flex justify-between items-center">
                                <h2 className="text-3xl font-bold text-digital-blue-800">Consulta em Andamento</h2>
                                <Button 
                                    onClick={handleBackToSelection}
                                    variant="outline"
                                    className="border-2 text-lighttext bg-foreground border-border"
                                >
                                    <User/>
                                    Voltar
                                </Button>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                {/* Timer */}
                                <div className="flex flex-col overflow-hidden rounded-2xl border border-border bg-digital-blue-50 shadow-md">
                                    <div className="flex items-center gap-3 p-4 bg-primary rounded-t-2xl">
                                        <Clock />
                                        <p className="text-lg text-foreground font-bold">Tempo Decorrido</p>
                                    </div>
                                    <div className="p-6">
                                        <Timer />
                                    </div>
                                </div>
                                {/* Dados do Paciente */}
                                <div className="md:col-span-2 flex flex-col overflow-hidden rounded-2xl border border-border bg-digital-blue-50 shadow-md">
                                    <div className="flex items-center gap-3 p-4 text-foreground">
                                        <User />
                                        <p className="text-lg font-bold">Dados do Paciente</p>
                                    </div>
                                    <div className="p-6">
                                        <div className="flex items-center gap-6 mb-6">
                                            <Avatar className="h-20 w-20 border border-border">
                                                <AvatarImage src={selectedPatientData.avatar} alt={selectedPatientData.name} />
                                                <AvatarFallback className="text-white text-xl font-semibold bg-digital-blue-200">
                                                    {getInitials(selectedPatientData.name)}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div className="flex-1 text-darktext">
                                                <h2 className="text-2xl font-semibold">{selectedPatientData.name}</h2>
                                                <p className="text-gray-600 font-mono">{selectedPatientData.email}</p>
                                                <p className="text-sm bg-digital-blue-100 rounded-radius px-2 py-1 w-fit mt-1">
                                                    Consulta: {new Date(selectedAppointmentData.appointment_date).toLocaleString('pt-BR')}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div className="bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="block">CPF</strong>
                                                <p className="text-gray-800 text">{selectedPatientData.cpf}</p>
                                            </div>
                                            <div className="bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="block">Telefone</strong>
                                                <p className="text-gray-800 text">{selectedPatientData.phone}</p>
                                            </div>
                                            <div className="bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="block">Gênero</strong>
                                                <p className="text-gray-800 text">{selectedPatientData.gender === 'male' ? 'Masculino' : selectedPatientData.gender === 'female' ? 'Feminino' : 'Outro'}</p>
                                            </div>
                                            <div className="bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="block">Nascimento</strong>
                                                <p className="text-gray-800 text">{new Date(selectedPatientData.birth_date).toLocaleDateString('pt-BR')}</p>
                                            </div>
                                            <div className="col-span-2 bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="block">Contato de Emergência</strong>
                                                <p className="text-gray-800">{selectedPatientData.emergency_contact}</p>
                                            </div>
                                            <div className="col-span-2 bg-white/80 p-4 rounded-radius border border-border">
                                                <strong className="text-primary block">Histórico Médico</strong>
                                                <p className="text-gray-800 max-h-20 overflow-y-auto">{selectedPatientData.medical_history}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/* Registro da Consulta */}
                            <div className="flex flex-col overflow-hidden rounded-2xl border border-border bg-digital-blue-50 shadow-md">
                                <div className="flex items-center gap-3 p-4">
                                    <ClipboardList />
                                    <p className="text-lg font-bold">Registro da Consulta</p>
                                </div>
                                <div className="p-8 space-y-8">
                                    {errors.appointment_id && (
                                        <div className="bg-red-50 border border-red-200 text-error px-4 py-3 rounded-lg">
                                            {errors.appointment_id}
                                        </div>
                                    )}
                                    <form onSubmit={handleFinishConsultation}>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                            <div className="space-y-3">
                                                <Label className="text-lg font-semibold text-primary flex items-center gap-2">
                                                    <span className="w-2 h-2 rounded-full bg-primary"></span>
                                                    Sintomas
                                                </Label>
                                                <textarea
                                                    placeholder="Descreva os sintomas relatados pelo paciente..."
                                                    className="w-full min-h-[120px] p-4 border-2 border-border rounded-lg focus:border-white focus:outline-none focus:ring-2 focus:ring-foreground/30 transition-all duration-200 resize-none bg-white/80"
                                                    value={data.symptoms}
                                                    onChange={(e) => setData('symptoms', e.target.value)}
                                                    required
                                                />
                                                {errors.symptoms && <p className="text-red-500 text-sm">{errors.symptoms}</p>}
                                            </div>
                                            <div className="space-y-3">
                                                <Label className="text-lg font-semibold text-primary flex items-center gap-2">
                                                    <span className="w-2 h-2 rounded-full bg-primary"></span>
                                                    Diagnóstico
                                                </Label>
                                                <textarea
                                                    placeholder="Informe o diagnóstico ou suspeita diagnóstica..."
                                                    className="w-full min-h-[120px] p-4 border-2 border-border rounded-lg focus:border-white focus:outline-none focus:ring-2 focus:ring-foreground/30 transition-all duration-200 resize-none bg-white/80"
                                                    value={data.diagnosis}
                                                    onChange={(e) => setData('diagnosis', e.target.value)}
                                                    required
                                                />
                                                {errors.diagnosis && <p className="text-red-500 text-sm">{errors.diagnosis}</p>}
                                            </div>
                                        </div>
                                        <div className="space-y-3 mt-8">
                                            <Label className="text-lg font-semibold text-primary flex items-center gap-2">
                                                <span className="w-2 h-2 rounded-full bg-primary"></span>
                                                Notas e Observações
                                            </Label>
                                            <textarea
                                                placeholder="Adicione observações adicionais, prescrições, orientações, etc..."
                                                className="w-full min-h-[120px] p-4 border-2 border-border rounded-lg focus:border-white focus:outline-none focus:ring-2 focus:ring-foreground/30 transition-all duration-200 resize-none bg-white/80"
                                                value={data.notes}
                                                onChange={(e) => setData('notes', e.target.value)}
                                            />
                                            {errors.notes && <p className="text-red-500 text-sm">{errors.notes}</p>}
                                        </div>
                                        <div className="flex gap-4 pt-6 w-fit">
                                            <Button 
                                                type="submit"
                                                disabled={processing}
                                                className="flex-1 py-4 px-5 text-lg font-semibold text-lighttext hover:opacity-90 transition-all duration-300 cursor-pointer disabled:opacity-50 bg-foreground"
                                            >
                                                {processing ? 'Finalizando...' : 'Finalizar Consulta'}
                                                <Check />
                                            </Button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}