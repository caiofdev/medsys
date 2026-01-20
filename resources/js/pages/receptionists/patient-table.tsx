import { type BreadcrumbItem } from '@/types';
import Table from '../../components/table-template';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import Pagination from '../../components/pagination';
import SearchBox from '../../components/ui/search-box';
import { useState, useEffect } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tabela de Pacientes',
        href: '/patient-table',
    },
];

export interface Patient {
    id: number;
    name: string;
    email: string;
    cpf: string;
    phone: string;
    gender: string;
    birth_date: string;
    emergency_contact: string;
    medical_history?: string;
}

interface PaginatedPatients {
    data: Patient[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
}

interface PatientTableProps {
    patients: PaginatedPatients;
    filters: {
        search: string;
    };
    userRole: 'admin' | 'doctor' | 'receptionist' | 'patient';
}

export default function PatientTable({ patients, filters, userRole }: PatientTableProps) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');

    const tableData = patients.data.map(patient => ({
        id: patient.id,
        name: patient.name,
        email: patient.email,
        phone: patient.phone,
        cpf: patient.cpf,
        gender: patient.gender,
        birth_date: patient.birth_date,
        emergency_contact: patient.emergency_contact,
        medical_history: patient.medical_history,
        photo: undefined
    }));

    useEffect(() => {
        setSearchTerm(filters?.search || '');
    }, [filters?.search]);

    useEffect(() => {
        if (searchTerm !== (filters?.search || '')) {
            const delayedSearch = setTimeout(() => {
                router.get('/receptionist/patients', { search: searchTerm }, { 
                    preserveState: true,
                    preserveScroll: true 
                });
            }, 500);

            return () => clearTimeout(delayedSearch);
        }
    }, [searchTerm]);

    return(
        <AppLayout breadcrumbs={breadcrumbs} userRole={userRole}>
            <Head title="Patient Table" />
            <div className="flex flex-col space-y-6 justify-center mt-5">
                <div className='flex flex-row justify-between ml-25 mr-25 lg:ml-15 lg:mr-15'>
                    <SearchBox 
                        placeHolder="Buscar por um paciente..." 
                        value={searchTerm}
                        onChange={setSearchTerm}
                    />
                </div>
                
                <Table users={tableData} type='patient'/>
                
                <Pagination 
                    links={patients.links}
                    currentPage={patients.current_page}
                    lastPage={patients.last_page}
                    total={patients.total}
                    perPage={patients.per_page}
                />
            </div>
        </AppLayout>
    );
}