import { type BreadcrumbItem } from '@/types';
import Table from '../../components/table-template';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import Pagination from '../../components/pagination';
import SearchBox from '../../components/ui/search-box';
import { useState, useEffect } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tabela de Administradores',
        href: '/admin-table',
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    cpf: string;
    phone: string;
    photo: string | undefined;
    birth_date: Date;
}

interface Admin {
    id: number;
    is_master: boolean;
    user_id: number;
    user: User;
}

interface PaginatedAdmins {
    data: Admin[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
}

interface AdminTableProps {
    admins: PaginatedAdmins;
    filters: {
        search: string;
    };
    userRole: 'admin' | 'doctor' | 'receptionist' | 'patient';
}

export default function AdminTable({ admins, filters, userRole }: AdminTableProps) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');

    const tableData = admins.data.map(admin => ({
        id: admin.id,
        name: admin.user.name,
        email: admin.user.email,
        phone: admin.user.phone,
        cpf: admin.user.cpf,
        is_master: admin.is_master ? 'Sim' : 'Não',
        photo: admin.user.photo ? `/storage/${admin.user.photo}` : undefined,
        is_master_bool: admin.is_master,
        birth_date: admin.user.birth_date ? new Date(admin.user.birth_date) : new Date(),
    }));

    useEffect(() => {
        setSearchTerm(filters?.search || '');
    }, [filters?.search]);

    useEffect(() => {
        if (searchTerm !== (filters?.search || '')) {
            const delayedSearch = setTimeout(() => {
                router.get('/admin/admins', { search: searchTerm }, { 
                    preserveState: true,
                    preserveScroll: true 
                });
            }, 500);

            return () => clearTimeout(delayedSearch);
        }
    }, [searchTerm, filters?.search]);

    return(
        <AppLayout breadcrumbs={breadcrumbs} userRole={userRole}>
            <Head title="Admin Table" />
            <div className="flex flex-col space-y-6 justify-center mt-5">
                <div className='flex flex-row justify-between ml-25 mr-25 lg:ml-15 lg:mr-15'>
                    <SearchBox 
                        placeHolder="Buscar por nome do administrador..." 
                        value={searchTerm}
                        onChange={setSearchTerm}
                    />
                </div>
                
                <Table users={tableData} type={'admin'} />
                
                <Pagination 
                    links={admins.links}
                    currentPage={admins.current_page}
                    lastPage={admins.last_page}
                    total={admins.total}
                    perPage={admins.per_page}
                />
            </div>
        </AppLayout>
    );
}