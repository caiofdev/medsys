import { router } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';
import { createElement } from 'react';

type DashboardCardProps = {
    icon: LucideIcon,
    title: string,
    color: string,
    description?: string,
    route?: string,
    onClick?: () => void,
};

export default function DashboardCard({ icon, title, color, description, route, onClick }: DashboardCardProps) {
    return (
        <div
            onClick={() => {
                if (onClick) {
                    onClick();
                } else if (route) {
                    router.visit(route);
                }
            }}
            className="flex flex-col w-full h-full rounded-radius p-6 bg-digital-blue-50 border border-border gap-3 items-center justify-center shadow hover:shadow-lg transition-all duration-200 cursor-pointer group"
        >
            <div className="flex flex-col justify-center items-center mb-2">
                <div className="rounded-full bg-white/70 p-4 shadow group-hover:bg-white">
                    {createElement(icon, { size: 50, color: color, strokeWidth: 2 })}
                </div>
            </div>
            <div className="flex flex-col justify-center items-center">
                <p className="font-bold text-lg text-digital-blue-900 text-center">{title}</p>
                {description && (
                    <p className="text-sm text-gray-600 text-center max-w-[220px]">{description}</p>
                )}
            </div>
        </div>
    );
}