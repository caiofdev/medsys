import { router } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';
import { createElement } from 'react';

type DashboardCardProps = {
    icon: LucideIcon,
    title: string,
    color: string,
    route?: string,
};

export default function DashboardCard( { icon, title, color, route }: DashboardCardProps ) {
    return (
        <div 
            onClick={() => route && router.visit(route)}
            className="flex flex-col w-full h-full rounded-radius p-6 bg-digital-blue-50 border border-border gap-3 items-center justify-center" 
        >
            <div className='flex flex-col justify-center items-center'>
                {createElement(icon, { size: 45, color: `${color}`, strokeWidth: 1.5 })}
            </div>
            <div className='flex flex-col justify-center items-center'>
                <p className='font-medium text-md text-center'>{title}</p>
            </div>
        </div>
    );
}