export interface DashboardScheduleEventProps {
   date: string;
   title: string;
   description: string;
   index: number;
}

const colorVariants = [
   'text-digital-blue-400',
   'text-digital-blue-600',
   'text-digital-blue-800',
];

export default function DashboardScheduleEvent({ date, title, description, index }: DashboardScheduleEventProps) {
   const color = colorVariants[index % colorVariants.length];

   return (
      <div className="w-full">
         <div className="bg-digital-blue-100 flex p-3 rounded-radius gap-3 items-center w-full">
            <div className={`${color.replace('text', 'bg')} h-10 w-1 min-w-1 max-w-1 rounded-radius`}></div>
            <div className={`text-2xl font-semibold ${color} min-w-[60px]`}>{date}</div>
            <div className="flex-1 min-w-0">
               <div className={`font-semibold ${color} `}>{title}</div>
               <div className="text-gray-800 text-sm truncate">{description}</div>
            </div>
         </div>
      </div>
   );
}