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
      <div>
         <div className="bg-digital-blue-100 flex p-3 rounded-radius gap-2 items-center">
               <div className={`${color.replace('text', 'bg')} h-10 w-1 rounded-radius`}></div>
               <div className={`text-3xl font-semibold ${color}`}>{date}</div>
               <div>
                  <div className={`text-lg font-semibold ${color}`}>
                     {title}
                  </div>
                  <div className='text-gray-800 text-sm'>
                     {description}
                  </div>
               </div>
         </div>
      </div>
   );
}