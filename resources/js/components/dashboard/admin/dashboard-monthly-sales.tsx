import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, Cell } from 'recharts';

type BarChartProps = {
    title: string;
    labels: string[];
    data: number[];
    totalValue?: number;
    currency?: boolean;
    currentTotal: number;
};

export default function DashboardMonthlySales({ 
    title, 
    labels, 
    data,  
    totalValue, 
    currency = false,
    currentTotal,
}: BarChartProps) {
    const formatoBRL = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });

    const chartData = labels.map((label, index) => ({
        name: label,
        value: data[index]
    }));

    const colorScale = [
        'var(--color-digital-blue-200)',
        'var(--color-digital-blue-300)',
        'var(--color-digital-blue-400)',
        'var(--color-digital-blue-500)',
        'var(--color-digital-blue-600)',
        'var(--color-digital-blue-700)',
        'var(--color-digital-blue-800)',
    ];

    const getColorByValue = (value: number, maxValue: number, minValue: number) => {
        if (maxValue === minValue) return colorScale[4];
        
        const normalizedValue = (value - minValue) / (maxValue - minValue);
        const colorIndex = Math.floor(normalizedValue * (colorScale.length - 1));
        
        return colorScale[colorIndex];
    };

    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);

    const CustomTooltip = ({ active, payload }: any) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-white p-3 rounded-radius border border-border shadow-lg">
                    <p className="text-sm font-semibold">{payload[0].payload.name}</p>
                    <p className="text-sm text-digital-blue-700 font-medium">
                        {currency ? formatoBRL.format(payload[0].value) : payload[0].value}
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
            <div className='flex flex-col w-full justify-between flex-1'>
                <div className='w-fit flex items-top'>
                    <div className='flex w-fit'>
                        <p className='text-3xl'>{formatoBRL.format(currentTotal)}</p>
                    </div>
                </div>
                <div className='flex flex-1 w-full'>
                    <div className='flex flex-1 justify-between p-4 pl-0'>
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={chartData} barGap={8}>
                                <Tooltip content={<CustomTooltip />} cursor={{ fill: 'transparent' }} />
                                <XAxis 
                                    dataKey="name" 
                                    tick={{ fill: '#000', fontSize: 12, fontWeight: 'bold' }}
                                    axisLine={false}
                                    tickLine={false}
                                />
                                <YAxis hide />
                                <Bar 
                                    dataKey="value" 
                                    radius={[12, 12, 4, 4]}
                                >
                                    {chartData.map((entry, index) => (
                                        <Cell 
                                            key={`cell-${index}`} 
                                            fill={getColorByValue(entry.value, maxValue, minValue)} 
                                        />
                                    ))}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                        {totalValue !== undefined && (
                            <div className='text-center mt-2'>
                                <p className='text-lg font-bold text-darktext'>
                                    Total: {currency ? formatoBRL.format(totalValue) : totalValue}
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}