import { NavMain } from '@/components/sidebar/nav-main';
import { NavUser } from '@/components/sidebar/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/sidebar/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { LayoutGrid, Users, UserCheck, Stethoscope, Shield, ClipboardList, Play, ClipboardPlus } from 'lucide-react';
import AppLogo from '@/components/app-logo';

type UserRole = 'admin' | 'doctor' | 'receptionist' | 'patient';

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Admins',
        href: '/admin/admins',
        icon: Shield,
    },
    {
        title: 'Médicos',
        href: '/admin/doctors',
        icon: Stethoscope,
    },
    {
        title: 'Recepcionistas',
        href: '/admin/receptionists',
        icon: UserCheck,
    },
];

const doctorNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/doctor/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Iniciar Atendimento',
        href: '/doctor/start-consultation',
        icon: Play,
    },
    {
        title: 'Prontuários',
        href: '/doctor/medical-record',
        icon: ClipboardList,
    },
];

const receptionistNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/receptionist/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Pacientes',
        href: '/receptionist/patients',
        icon: Users,
    },
    {
        title: 'Visualizar Consultas',
        href: '/receptionist/consultations-list',
        icon: ClipboardPlus,
    },
    
];


function getNavItemsByRole(userRole: UserRole): NavItem[] {
    switch (userRole) {
        case 'admin':
            return adminNavItems;
        case 'doctor':
            return doctorNavItems;
        case 'receptionist':
            return receptionistNavItems;
        default:
            return adminNavItems; // fallback
    }
}

interface AppSidebarProps {
    userRole: UserRole;
}

export function AppSidebar({ userRole }: AppSidebarProps) {
    const navItems = getNavItemsByRole(userRole);
    
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={navItems} />
            </SidebarContent>
            
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
