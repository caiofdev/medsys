interface SelectFieldProps {
    label: string;
    name: string;
    value: string;
    bgColor?: string;
    options: { label: string; value: string }[];
    onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
}

export function SelectField({ label, name, value, bgColor, options, onChange }: SelectFieldProps) {
    return (
        <div className="flex flex-col w-full">
        <label className="font-bold mb-2">{label}</label>
        <select
            name={name}
            value={value}
            onChange={onChange}
            className={`p-2 rounded-md text-darktext focus:outline-none cursor-pointer ${bgColor || 'bg-input'}`}
        >
            {options.map((opt) => (
            <option key={opt.value} value={opt.value}>
                {opt.label}
            </option>
            ))}
        </select>
        </div>
    );
}