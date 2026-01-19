import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/sidebar/app-sidebar';
import { AppSidebarHeader } from '@/components/sidebar/app-sidebar-header';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren } from 'react';

type UserRole = 'admin' | 'doctor' | 'receptionist' | 'patient';

interface AppSidebarLayoutProps {
    breadcrumbs?: BreadcrumbItem[];
    userRole?: UserRole;
}

export default function AppSidebarLayout({ children, breadcrumbs = [], userRole = 'admin' }: PropsWithChildren<AppSidebarLayoutProps>) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar userRole={userRole} />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
