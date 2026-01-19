import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import SearchBox from '@/components/ui/search-box';
import { faUser, faUserMd, faCalendar, faMoneyBill } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Pagination from '@/components/pagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/receptionist/dashboard',
    },
    {
        title: 'Lista de Consultas',
        href: '/receptionist/consultations-list',
    },
];

interface Consultation {
    id: number;
    symptoms: string;
    diagnosis: string;
    notes: string;
    created_at: string;
    appointment: {
        id: number;
        appointment_date: string;
        status: string;
        value: number;
        patient: {
            id: number;
            name: string;
            email: string;
            phone: string;
        };
        doctor: {
            id: number;
            user: {
                name: string;
                email: string;
            };
        };
        receptionist: {
            id: number;
            user: {
                name: string;
            };
        };
    };
}

interface ConsultationsListProps {
    consultations: {
        data: Consultation[];
        links: PaginationLink[];
        meta: any;
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
    filters: {
        search: string;
    };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}


function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

export default function ConsultationsList({ consultations, filters }: ConsultationsListProps) {
    const [searchValue, setSearchValue] = useState(filters.search);

    useEffect(() => {
        setSearchValue(filters.search);
    }, [filters.search]);

    useEffect(() => {
        if (searchValue !== filters.search) {
            const delayedSearch = setTimeout(() => {
                router.get('/receptionist/consultations-list', { search: searchValue }, {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                });
            }, 300);

            return () => clearTimeout(delayedSearch);
        }
    }, [searchValue, filters.search]);

    return (
        <AppLayout breadcrumbs={breadcrumbs} userRole='receptionist'>
            <Head title="Lista de Consultas" />
            
            <div className="flex flex-col ml-20 mr-20 mt-10 items-center justify-center">
                <div className="w-full flex flex-col">
                    <div className="flex w-full justify-between items-center mb-2">
                        <div>
                            <h1 className="text-3xl font-bold text-foreground">Lista de Consultas</h1>
                            <p className="text-gray-500 text-base mt-1">Aqui estão todas as consultas já realizadas e registradas no sistema.</p>
                        </div>
                        <div className="w-1/3">
                            <SearchBox
                                placeHolder="Buscar por paciente ou doutor..."
                                value={searchValue}
                                onChange={setSearchValue}
                            />
                        </div>
                    </div>

                    <div className="overflow-x-auto rounded-xl border border-border bg-white shadow-sm">
                        <table className="min-w-full text-sm">
                            <thead className="sticky top-0 z-10 bg-foreground text-white">
                            <tr>
                                <th className="px-6 py-3 text-left font-semibold w-16">#</th>

                                <th className="px-6 py-3 text-left font-semibold">
                                <FontAwesomeIcon icon={faUser} className="mr-2 opacity-80" />
                                Paciente
                                </th>

                                <th className="px-6 py-3 text-left font-semibold">
                                <FontAwesomeIcon icon={faUserMd} className="mr-2 opacity-80" />
                                Doutor
                                </th>

                                <th className="px-6 py-3 text-left font-semibold">
                                <FontAwesomeIcon icon={faCalendar} className="mr-2 opacity-80" />
                                Data
                                </th>

                                <th className="px-6 py-3 text-right font-semibold">
                                <FontAwesomeIcon icon={faMoneyBill} className="mr-2 opacity-80" />
                                Valor
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            {consultations.data.length > 0 ? (
                                consultations.data.map((consultation) => (
                                <tr key={consultation.id} className="border-b last:border-b-0">
                                    <td className="px-6 py-3 font-medium text-darktext">
                                    {consultation.id}
                                    </td>

                                    <td className="px-6 py-3 text-darktext">
                                    {consultation.appointment.patient.name}
                                    </td>

                                    <td className="px-6 py-3 text-darktext">
                                    {consultation.appointment.doctor.user.name}
                                    </td>

                                    <td className="px-6 py-3 text-darktext whitespace-nowrap">
                                    {formatDate(consultation.appointment.appointment_date)}
                                    </td>

                                    <td className="px-6 py-3 text-right font-medium text-darktext whitespace-nowrap">
                                    R$ {consultation.appointment.value.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                    })}
                                    </td>
                                </tr>
                                ))
                            ) : (
                                <tr>
                                <td
                                    colSpan={5}
                                    className="px-6 py-12 text-center text-gray-500"
                                >
                                    {searchValue
                                    ? `Nenhuma consulta encontrada para "${searchValue}"`
                                    : 'Nenhuma consulta encontrada'}
                                </td>
                                </tr>
                            )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination
                        links={consultations.links}
                        currentPage={consultations.current_page}
                        lastPage={consultations.last_page}
                        total={consultations.total}
                        perPage={consultations.per_page}
                    />
                </div>
            </div>
        </AppLayout>
    );
}