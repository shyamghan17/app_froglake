import React from 'react';

interface InputProps {
    type?: string;
    value?: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    placeholder?: string;
    className?: string;
    required?: boolean;
    disabled?: boolean;
    name?: string;
    id?: string;
    checked?: boolean;
}

export function Input({ type = 'text', value, onChange, placeholder, className = '', required, disabled, name, id, checked }: InputProps) {
    return (
        <input
            type={type}
            value={value}
            onChange={onChange}
            placeholder={placeholder}
            className={`w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-1 focus:border-primary ${className}`}
            required={required}
            disabled={disabled}
            name={name}
            id={id}
            checked={checked}
        />
    );
}