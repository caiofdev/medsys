import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import { router } from '@inertiajs/react';

type DashboardCardProps = {
    icon: IconDefinition,
    title: string,
    color: string,
    route?: string,
};

export default function DashboardCard( { icon, title, color, route }: DashboardCardProps ) {
    return (
        <div 
            onClick={() => route && router.visit(route)}
            className=" flex flex-col overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border h-full justify-center items-center gap-3 cursor-pointer hover:shadow-2xl hover:scale-102 bg-digital-blue-50" 
        >
            <div className='flex flex-col justify-center items-center'>
                <FontAwesomeIcon icon={icon} style={{ color: `#${color}` }} className="text-6xl" /> 
            </div>
            <div className='flex flex-col justify-center items-center'>
                <p>{title}</p>
            </div>
        </div>
    );
}