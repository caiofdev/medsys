import { useEffect, useState } from 'react';
import DashboardTotal from './dashboard-monthly-total';

interface DashboardGoalProps {
   goalAmount: number;
   currentAmount: number;
   previousAmount?: number;
}

export default function DashboardGoal({ goalAmount, currentAmount, previousAmount = 0 }: DashboardGoalProps) {
   const formatoBRL = new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL',
   });

   const percentage = Math.min((currentAmount / goalAmount) * 100, 100);
   const [animatedPercentage, setAnimatedPercentage] = useState(0);

   useEffect(() => {
      const timer = setTimeout(() => {
         setAnimatedPercentage(percentage);
      }, 100);
      return () => clearTimeout(timer);
   }, [percentage]);

   return (
      <div className="flex flex-col bg-digital-blue-50 rounded-radius border border-border p-6 gap-5">
         <div className="flex justify-between items-center">
               <h3 className="text-xl font-bold text-darktext">Meta do mês</h3>
            <span className="text-md font-bold text-digital-blue-700 bg-digital-blue-100 rounded-2xl border border-digital-blue-700/50 px-2">
               {formatoBRL.format(goalAmount)}
            </span>
         </div>

         <div className="flex flex-col gap-2">
            <div className="flex justify-between items-center">
               <span className="text-sm text-gray-700">Progresso</span>
               <span className="text-xl font-medium text-gray-700">{percentage.toFixed(0)}%</span>
            </div>
            <div className="w-full h-3 bg-digital-blue-200 rounded-full overflow-hidden">
               <div 
                  className={`h-full rounded-full transition-all duration-1000 ease-out ${
                     percentage >= 100 ? 'bg-success' : 'bg-digital-blue-600'
                  }`}
                  style={{ width: `${animatedPercentage}%` }}
               />
            </div>
            <div className="mt-3">
               <DashboardTotal currentTotal={currentAmount} previousTotal={previousAmount} />
            </div>
         </div>
      </div>
   );
}  