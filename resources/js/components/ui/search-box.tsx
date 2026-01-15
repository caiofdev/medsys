
import {faMagnifyingGlass} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

interface SearchBoxProps {
    placeHolder: string;
    value?: string;
    onChange?: (value: string) => void;
}

export default function SearchBox({ placeHolder, value = '', onChange }: SearchBoxProps) {
    return(
        <div className="w-full max-w-md">
            <label htmlFor="search" className="sr-only">Pesquisar</label>
            <div className="relative">
                <input
                    type="text"
                    id="search"
                    placeholder={placeHolder}
                    value={value}
                    onChange={(e) => onChange?.(e.target.value)}
                    className="w-full rounded-lg border border-neutral-300 bg-foreground px-4 py-2 text-sm text-white"
                />
                <button className="absolute right-2 top-1/2 -translate-y-1/2 mr-1 text-white cursor-pointer">
                    <FontAwesomeIcon icon={faMagnifyingGlass}/>
                </button>
            </div>          
        </div>
    );
}