interface SelectFieldProps {
    label: string;
    name: string;
    value: string;
    options: { label: string; value: string }[];
    onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
}

export function SelectField({ label, name, value, options, onChange }: SelectFieldProps) {
    return (
        <div className="flex flex-col w-full">
        <label className="font-bold">{label}</label>
        <select
            name={name}
            value={value}
            onChange={onChange}
            className="bg-input p-2 rounded-md text-darktext focus:outline-none cursor-pointer"
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