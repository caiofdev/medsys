import { Pie } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
    plugins,
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

type PieChartProps = {
    title: string;
    labels: string[];
    data: number[];
    colors: string[];
};

export default function DashboardPieChart({ title, labels, data, colors }: PieChartProps) {
    const chartData = {
        labels,
        datasets: [
            {
                data,
                backgroundColor: colors.map(color => `#${color}`),
            },
        ],
    };
    const options = {
        plugins: {
            legend: {
                position: 'right' as const,
                labels: {
                    color: 'gray',
                    boxWidth: 15,
                    font: {
                        weight: 'bold' as const,
                    },
                },
            },
        },
        maintainAspectRatio: false,
        responsive: true,
    };
    return (
        <div className="flex flex-col col-span-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border h-50" style={{ backgroundColor: '#F7F2EB' }}>
            <div className='flex flex-col justify-center'>
                <div className='flex p-2 pl-3 rounded-xl h-full' style={{ backgroundColor: '#030D29' }}>
                    <p className='text-xl text-white font-bold'>{title}</p>
                </div>
                <div className='flex flex-col h-full justify-center items-center'>
                    <div className='w-2/3 h-full'>
                        <Pie data={chartData} options={options} />
                    </div>
                </div>
            </div>
        </div>
    );
}
