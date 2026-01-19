import {
   BarChart,
   Bar,
   XAxis,
   YAxis,
   Tooltip,
   ResponsiveContainer,
   Cell,
   CartesianGrid,
   LabelList,
} from 'recharts';

interface StatusAppointmentsSummaryProps {
   completed: number;
   scheduled: number;
   canceled: number;
   title: string;
}

const chartData = [
   {
   name: 'Concluídas',
   value: 0,
   color: '#002966',
   },
   {
   name: 'Agendadas',
   value: 0,
   color: '#0066ff',
   },
   {
   name: 'Canceladas',
   value: 0,
   color: '#99c2ff',
   },
];

export default function StatusAppointmentsSummary({
   completed,
   scheduled,
   canceled,
   title,
}: StatusAppointmentsSummaryProps) {
   const data = chartData.map((item) => {
   if (item.name === 'Concluídas') return { ...item, value: completed };
   if (item.name === 'Agendadas') return { ...item, value: scheduled };
   return { ...item, value: canceled };
   });

   return (
   <div className="bg-digital-blue-50 rounded-radius border border-border shadow-sm p-4 sm:p-6 w-full max-w-full flex flex-col min-h-[260px]">
      <h2 className="font-bold text-digital-blue-900 text-lg mb-4">
         {title}
      </h2>

      <div className="flex-1 min-h-[180px] max-w-full">
         <ResponsiveContainer width="100%" height={180} minWidth={0} minHeight={120}>
         <BarChart
            data={data}
            margin={{ top: 20, right: 16, left: 0, bottom: 0 }}
            barCategoryGap={20}
         >
            <defs>
               {data.map((item, index) => (
               <linearGradient
                  key={index}
                  id={`gradient-${index}`}
                  x1="0"
                  y1="0"
                  x2="0"
                  y2="1"
               >
                  <stop offset="0%" stopColor={item.color} stopOpacity={1} />
                  <stop offset="100%" stopColor={item.color} stopOpacity={0.5} />
               </linearGradient>
               ))}
            </defs>

            <CartesianGrid
               strokeDasharray="3 3"
               vertical={false}
               stroke="#e5e7eb"
            />

            <XAxis
               dataKey="name"
               tick={{ fill: '#1e3a8a', fontSize: 13, fontWeight: 500 }}
               axisLine={false}
               tickLine={false}
            />

            <YAxis
               allowDecimals={false}
               tick={{ fill: '#1e3a8a', fontSize: 12 }}
               axisLine={false}
               tickLine={false}
            />

            <Tooltip
               cursor={{ fill: '#eff6ff' }}
               contentStyle={{
               borderRadius: 10,
               border: '1px solid #e5e7eb',
               fontSize: 13,
               }}
               formatter={(value: number | undefined) => [`${value ?? 0} consultas`, 'Total']}
            />

            <Bar dataKey="value" radius={[8, 8, 0, 0]}>
               <LabelList
               dataKey="value"
               position="top"
               fill="#1e3a8a"
               fontSize={12}
               fontWeight={600}
               />
               {data.map((item, index) => (
               <Cell
                  key={`cell-${index}`}
                  fill={`url(#gradient-${index})`}
               />
               ))}
            </Bar>
         </BarChart>
         </ResponsiveContainer>
      </div>
   </div>
   );
}
