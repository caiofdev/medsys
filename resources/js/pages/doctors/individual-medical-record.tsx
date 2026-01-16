
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { BookPlus, Cake, IdCard,  MessageSquarePlus, Phone, Stethoscope, User, VenusAndMars } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/doctor/dashboard',
    },
    {
        title: 'Visualizar Prontuários',
        href: '/doctor/medical-record',
    },
    {
        title: 'Prontuário Individual',
        href: '#',
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

interface Consultation {
    id: number;
    date: string;
    type: string;
    diagnosis: string;
    symptoms: string;
    notes: string;
}

interface MedicalHistory {
    allergies: string[];
    medications: string[];
    conditions: string[];
    surgeries: string[];
}

export default function IndividualMedicalRecord({ 
    patient, 
    consultations = [], 
}: { 
    patient: Patient;
    consultations?: Consultation[];
    medicalHistory?: MedicalHistory;
}) {
    const calculateAge = (birthDate: string): number => {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    };

    const patientAge = calculateAge(patient.birth_date);

    return (
        <AppLayout breadcrumbs={breadcrumbs} userRole='doctor'>
            <Head title={`Prontuário do ${patient.name}`} />
            <div className="container mx-auto p-6">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-foreground">Prontuário do Paciente</h1>
                    <p className="text-foreground/70 mt-2 text-lg">{patient.name}</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div className="flex flex-col h-full">
                        <div className="bg-digital-blue-50 rounded-radius shadow-lg p-6 h-full">
                            <div className="text-center mb-6">
                                <div className="w-24 h-24 bg-digital-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <User size={50}/>
                                </div>
                                <h2 className="text-xl font-bold text-foreground">{patient.name}</h2>
                            </div>
                            <div className="space-y-4 flex-1 text-darktext">
                                <div className="flex items-center justify-between bg-white/70 p-3 rounded-radius">
                                    <div className="flex items-center">
                                        <Cake size={20} className='mr-2'/>
                                        <span className="font-medium">Idade</span>
                                    </div>
                                    <span className="font-semibold">{patientAge} anos</span>
                                </div>

                                <div className="flex items-center justify-between bg-white/70 p-3 rounded-radius">
                                    <div className="flex items-center">
                                        <VenusAndMars size={20} className='mr-2' />
                                        <span className="font-medium">Gênero</span>
                                    </div>
                                    <span className="font-semibold">
                                        {patient.gender === 'male' ? 'Masculino' : patient.gender === 'female' ? 'Feminino' : 'Outro'}
                                    </span>
                                </div>

                                <div className="flex items-center justify-between bg-white/70 p-3 rounded-radius">
                                    <div className="flex items-center">
                                        <Phone size={20} className='mr-2'/>
                                        <span className="font-medium">Telefone</span>
                                    </div>
                                    <span className="font-semibold">{patient.phone}</span>
                                </div>

                                <div className="flex items-center justify-between bg-white/70 p-3 rounded-radius">
                                    <div className="flex items-center">
                                        <IdCard size={20} className="mr-2" />
                                        <span className="font-medium">CPF</span>
                                    </div>
                                    <span className="font-semibold">{patient.cpf}</span>
                                </div>

                                <div className="flex items-center justify-between bg-white/70 p-3 rounded-radius">
                                    <div className="flex items-center">
                                        <MessageSquarePlus size={20} className="mr-2" />
                                        <span className="font-medium">Contato de Emergência</span>
                                    </div>
                                    <span className="font-semibold">
                                        {patient.emergency_contact}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-col space-y-6">
                        {patient.medical_history && (
                            <div className="bg-digital-blue-50 rounded-radius shadow-lg p-6">
                                <h3 className="text-lg font-bold mb-4 flex items-center">
                                    <BookPlus size={20} className="mr-2" />
                                    Histórico Médico Detalhado
                                </h3>
                                <div className="bg-white/80 p-4 rounded-radius">
                                    <p className="whitespace-pre-wrap leading-relaxed">{patient.medical_history}</p>
                                </div>
                            </div>
                        )}

                        <div className="bg-digital-blue-50 rounded-radius shadow-lg p-6">
                            <h3 className="text-lg font-bold mb-6 flex items-center">
                                <Stethoscope size={20} className="mr-2" />
                                Consultas Realizadas
                            </h3>
                            
                            <div className="space-y-4">
                                {consultations.length > 0 ? (
                                    consultations.map((consultation) => (
                                        <div key={consultation.id} className="bg-white/80 rounded-radius p-4">
                                            <div className="flex justify-between items-start mb-3">
                                                <h4 className="font-semibold text-lg">{consultation.type}</h4>
                                                <span className="text-sm bg-digital-blue-300/30 px-3 py-1 rounded-full font-medium">
                                                    {new Date(consultation.date).toLocaleDateString('pt-BR')}
                                                </span>
                                            </div>
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div className="bg-gray-200/60 p-3 rounded-radius">
                                                    <p className="text-sm font-medium mb-2">Sintomas:</p>
                                                    <p className="text-sm">{consultation.symptoms}</p>
                                                </div>
                                                <div className="bg-gray-200/60 p-3 rounded-radius">
                                                    <p className="text-sm font-medium mb-2">Diagnóstico:</p>
                                                    <p className="text-sm">{consultation.diagnosis}</p>
                                                </div>
                                                {consultation.notes && (
                                                    <div className="md:col-span-2 bg-gray-200/60 p-3 rounded-radius">
                                                        <p className="text-sm font-medium mb-2">Observações:</p>
                                                        <p className="text-sm">{consultation.notes}</p>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center py-8 bg-white/80 rounded-radius">
                                        <h4 className="text-lg font-medium mb-2">Nenhuma consulta encontrada</h4>
                                        <p>Este paciente ainda não possui consultas registradas.</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}