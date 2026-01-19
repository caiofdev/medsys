import { UsersRound } from 'lucide-react';
import { AreaChart, Area, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts';

type PatientsTrendProps = {
   title: string;
   labels: string[];
   data: number[];
   totalPatients: number;
};

export default function DashboardPatientsTrend({ 
   title, 
   labels, 
   data,
   totalPatients,
}: PatientsTrendProps) {
   const chartData = labels.map((label, index) => ({
      name: label,
      value: data[index]
   }));

   const blueColor = '#003d99'; // --color-digital-blue-700
   const lightBlueColor = '#3385ff'; // --color-digital-blue-400

   const CustomTooltip = ({ active, payload }: any) => {
      if (active && payload && payload.length) {
            return (
               <div className="bg-white p-3 rounded-radius border border-border shadow-lg">
                  <p className="text-sm font-semibold">{payload[0].payload.name}</p>
                  <p className="text-sm text-digital-blue-700 font-medium">
                        {payload[0].value} {payload[0].value === 1 ? 'paciente' : 'pacientes'}
                  </p>
               </div>
            );
      }
      return null;
   };

   return (
      <div className="flex flex-col rounded-radius border border-border overflow-hidden p-5 w-full h-full bg-digital-blue-50">
            <div className='flex'>
               <p className='text-xl text-darktext font-bold mb-4'>{title}</p>
            </div>
            <div className='flex items-center gap-3 h-fit'>
               <div className='bg-digital-blue-200 rounded-radius p-1'><UsersRound size={25}/></div>
               <p className='text-xl text-darktext'>{totalPatients} {totalPatients === 1 ? 'paciente' : 'pacientes'}</p>
            </div>
            <div className='flex flex-col w-full justify-between flex-1'>
               <div className="flex flex-1 w-full">
                  <div className="flex flex-1 p-4 pl-0">
                     <ResponsiveContainer width="100%" height="100%">
                           <AreaChart
                              data={chartData}
                              margin={{ top: 10, right: 10, left: 20, bottom: 5 }}
                           >
                              <defs>
                                 <linearGradient id="patientsGradient" x1="0" y1="0" x2="0" y2="1">
                                       <stop offset="5%" stopColor={blueColor} stopOpacity={0.8} />
                                       <stop offset="95%" stopColor={lightBlueColor} stopOpacity={0.1} />
                                 </linearGradient>
                              </defs>
                              <XAxis
                                 dataKey="name"
                                 axisLine={false}
                                 tickLine={false}
                                 tick={{
                                       fill: '#000',
                                       fontSize: 12,
                                       fontWeight: 'bold',
                                 }}
                              />
                              <YAxis hide />
                              <Tooltip
                                 content={<CustomTooltip />}
                                 cursor={false}
                              />
                              <Area
                                 type="monotone"
                                 dataKey="value"
                                 stroke={blueColor}
                                 strokeWidth={3}
                                 fill="url(#patientsGradient)"
                                 dot={{
                                       r: 6,
                                       fill: blueColor,
                                       stroke: '#fff',
                                       strokeWidth: 2,
                                 }}
                                 activeDot={{
                                       r: 8,
                                       fill: blueColor,
                                       stroke: '#fff',
                                       strokeWidth: 2,
                                 }}
                              />
                           </AreaChart>
                     </ResponsiveContainer>
                  </div>
               </div>
            </div>
      </div>
   );
}
