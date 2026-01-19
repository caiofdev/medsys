import { Search } from 'lucide-react';

interface SearchBoxProps {
    placeHolder: string;
    value?: string;
    onChange?: (value: string) => void;
}

export default function SearchBox({ placeHolder, value = '', onChange }: SearchBoxProps) {
    return (
        <div className="w-full max-w-md">
            <label htmlFor="search" className="sr-only">Pesquisar</label>

            <div className="relative">
                <input
                    id="search"
                    type="text"
                    placeholder={placeHolder}
                    value={value}
                    onChange={(e) => onChange?.(e.target.value)}
                    className="
                        w-full
                        rounded-xl
                        border border-digital-blue-200
                        bg-digital-blue-50/70
                        px-11 py-2.5 text-digital-blue-800 text-medium
                        placeholder:text-digital-blue-700/50
                        transition
                        hover:border-digital-blue-300
                        focus:border-digital-blue-700
                        focus:outline-none
                        focus:ring-2
                        focus:ring-digital-blue-300/30
                    "
                />

                <span className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-digital-blue-600">
                    <Search size={20}/>
                </span>
            </div>
        </div>
    );
}
