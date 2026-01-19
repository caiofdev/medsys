interface ConsultationsSummaryProps {
   todayConsultations: number;
   weekConsultations: number;
   monthConsultations: number;
}

export default function DashboardConsultationsSummary({ todayConsultations, weekConsultations, monthConsultations }: ConsultationsSummaryProps) {
   const cards = [
      { label: 'Consultas hoje', value: todayConsultations, bg: 'bg-digital-blue-700/90', text: 'text-white' },
      { label: 'Esta semana', value: weekConsultations, bg: 'bg-digital-blue-400/90', text: 'text-white' },
      { label: 'Este mês', value: monthConsultations, bg: 'bg-digital-blue-200/90', text: 'text-digital-blue-900' },
   ];

   return (
      <div className="bg-digital-blue-50 rounded-radius shadow p-6 flex flex-col gap-4">
         <p className="font-bold text-digital-blue-900 text-xl mb-1">Resumo de Consultas</p>
         <div className="flex gap-4 justify-between">
            {cards.map((card) => (
               <div
                  key={card.label}
                  className={`flex flex-col items-center justify-center w-28 h-24 rounded-lg border border-border ${card.bg} ${card.text}`}
               >
                  <span className="text-xs font-medium opacity-90 mb-1">{card.label}</span>
                  <span className="text-4xl font-bold">{card.value}</span>
               </div>
            ))}
         </div>
      </div>
   );
}