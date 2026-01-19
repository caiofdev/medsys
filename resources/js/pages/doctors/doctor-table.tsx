import { type BreadcrumbItem } from '@/types';
import Table from '../../components/table-template';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import Pagination from '../../components/pagination';
import SearchBox from '../../components/ui/search-box';
import { useState, useEffect } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tabela de Doutores',
        href: '/doctor-table',
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    cpf: string;
    phone: string;
    photo: string | undefined;
    birth_date?: Date;
}

interface Doctor {
    id: number;
    user_id: number;
    crm: string;
    user: User;
}

interface PaginatedDoctors {
    data: Doctor[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
}

interface DoctorTableProps {
    doctors: PaginatedDoctors;
    filters: {
        search: string;
    };
    userRole: 'admin' | 'doctor' | 'receptionist' | 'patient';
}

export default function DoctorTable({ doctors, filters, userRole }: DoctorTableProps) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');

    const tableData = doctors.data.map(doctor => ({
        id: doctor.id,
        name: doctor.user.name,
        email: doctor.user.email,
        phone: doctor.user.phone,
        cpf: doctor.user.cpf,
        photo: doctor.user.photo ? `/storage/${doctor.user.photo}` : undefined,
        birth_date: doctor.user.birth_date ? new Date(doctor.user.birth_date) : new Date(),
        crm: doctor.crm,
    }));

    useEffect(() => {
        setSearchTerm(filters?.search || '');
    }, [filters?.search]);

    useEffect(() => {
        if (searchTerm !== (filters?.search || '')) {
            const delayedSearch = setTimeout(() => {
                router.get('/admin/doctors', { search: searchTerm }, { 
                    preserveState: true,
                    preserveScroll: true 
                });
            }, 500);

            return () => clearTimeout(delayedSearch);
        }
    }, [searchTerm]);

    return(
        <AppLayout breadcrumbs={breadcrumbs} userRole={userRole}>
            <Head title="Doctor Table" />
            <div className="flex flex-col space-y-6 justify-center mt-5">
                <div className='flex flex-row justify-between ml-25 mr-25 lg:ml-15 lg:mr-15'>
                    <SearchBox 
                        placeHolder="Buscar por nome do doutor..." 
                        value={searchTerm}
                        onChange={setSearchTerm}
                    />
                </div>
                
                <Table users={tableData} type='doctor' />
                
                <Pagination 
                    links={doctors.links}
                    currentPage={doctors.current_page}
                    lastPage={doctors.last_page}
                    total={doctors.total}
                    perPage={doctors.per_page}
                />
            </div>
        </AppLayout>
    );
}