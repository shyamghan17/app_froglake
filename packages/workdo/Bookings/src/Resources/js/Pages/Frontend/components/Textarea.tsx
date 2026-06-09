import React from 'react';

interface TextareaProps {
    value: string;
    onChange: (e: React.ChangeEvent<HTMLTextAreaElement>) => void;
    placeholder?: string;
    rows?: number;
    className?: string;
    required?: boolean;
    disabled?: boolean;
    name?: string;
}

export function Textarea({ value, onChange, placeholder, rows = 3, className = '', required, disabled, name }: TextareaProps) {
    return (
        <textarea
            value={value}
            onChange={onChange}
            placeholder={placeholder}
            rows={rows}
            className={`w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-1 focus:border-primary resize-none ${className}`}
            required={required}
            disabled={disabled}
            name={name}
        />
    );
}