import { TrendingDown, TrendingUp } from "lucide-react";

type DashboardTotalProps = {
    currentTotal: number;
    previousTotal: number;
}

const formatoBRL = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

export default function DashboardTotal( {currentTotal, previousTotal}: DashboardTotalProps ) {
    const difference = currentTotal - previousTotal;
    const change = (difference / previousTotal) * 100;
    const sign = change >= 0 ? '+' : '';
    
    let result: string;
    let isPositive: boolean;
    
    if (previousTotal === 0 && currentTotal > 0) {
        result = '+100% vs mês anterior';
        isPositive = true;

    } else if (change === 0) {
        result = 'Sem alteração';
        isPositive = true;

    } else if (!Number.isFinite(change)) {
        result = 'Sem dados';
        isPositive = true;

    } else {
        result = `${sign}${change.toFixed(2)}% vs mês anterior`;
        isPositive = change >= 0;
    }

    
    return (
        <div className="flex items-center h-fit rounded-radius">
            <div>
                <div className='flex justify-center items-center h-fit gap-3'>
                    <div className='flex w-fit'>
                        <p className='text-3xl'>{formatoBRL.format(currentTotal)}</p>
                    </div>
                    <div className='flex flex-col w-fit justify-self-start gap-0'>
                        <p className={`font-light ${isPositive ? 'text-success' : 'text-error'}`}>
                            {result}
                        </p>
                        <div className="flex">
                            {isPositive ? 
                                <TrendingUp className="text-success" /> : 
                                <TrendingDown className="text-error" />
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}