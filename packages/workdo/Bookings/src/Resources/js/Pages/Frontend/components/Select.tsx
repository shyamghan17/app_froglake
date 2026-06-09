import React from 'react';

interface SelectOption {
    value: string | number;
    label: string;
}

interface SelectProps {
    value: string | number;
    onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    options: SelectOption[];
    placeholder?: string;
    className?: string;
    disabled?: boolean;
}

export function Select({ value, onChange, options, placeholder, className = '', disabled }: SelectProps) {
    return (
        <select
            value={value}
            onChange={onChange}
            className={`w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-1 focus:border-primary ${disabled ? 'bg-gray-100' : ''} ${className}`}
            disabled={disabled}
        >
            {placeholder && <option value="">{placeholder}</option>}
            {options.map(option => (
                <option key={option.value} value={option.value}>
                    {option.label}
                </option>
            ))}
        </select>
    );
}