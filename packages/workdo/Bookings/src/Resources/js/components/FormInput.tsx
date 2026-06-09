import React from 'react';
import { LucideIcon } from 'lucide-react';

interface FormInputProps {
    label: string;
    type?: string;
    name: string;
    placeholder: string;
    required?: boolean;
    icon?: LucideIcon;
    value?: string;
    onChange?: (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void;
    rows?: number;
    className?: string;
    primaryColor?: string;
}

export default function FormInput({
    label,
    type = 'text',
    name,
    placeholder,
    required = false,
    icon: Icon,
    value,
    onChange,
    rows,
    className = '',
    primaryColor = '#52816D'
}: FormInputProps) {
    const inputClasses = `w-full ${Icon ? 'pl-10' : 'pl-4'} pr-4 py-3 bg-gray-50 border border-gray-300 rounded-lg transition`;

    return (
        <div className={className}>
            <label htmlFor={name} className="block text-sm font-medium text-gray-700 mb-1">
                {label} {required && '*'}
            </label>
            <div className="relative">
                {Icon && (
                    <div className={`absolute ${type === 'textarea' ? 'top-3' : 'inset-y-0'} left-0 pl-3 flex items-center pointer-events-none`}>
                        <Icon className="w-4 h-4 text-gray-400" />
                    </div>
                )}
                {type === 'textarea' ? (
                    <textarea
                        id={name}
                        name={name}
                        rows={rows || 4}
                        placeholder={placeholder}
                        className={inputClasses}
                        style={{ '--focus-ring-color': primaryColor, '--focus-border-color': primaryColor } as React.CSSProperties}
                        onFocus={(e) => { e.target.style.borderColor = primaryColor; e.target.style.boxShadow = `0 0 0 1px ${primaryColor}`; }}
                        onBlur={(e) => { e.target.style.borderColor = '#d1d5db'; e.target.style.boxShadow = 'none'; }}
                        required={required}
                        value={value}
                        onChange={onChange}
                    />
                ) : (
                    <input
                        type={type}
                        id={name}
                        name={name}
                        placeholder={placeholder}
                        className={inputClasses}
                        style={{ '--focus-ring-color': primaryColor, '--focus-border-color': primaryColor } as React.CSSProperties}
                        onFocus={(e) => { e.target.style.borderColor = primaryColor; e.target.style.boxShadow = `0 0 0 1px ${primaryColor}`; }}
                        onBlur={(e) => { e.target.style.borderColor = '#d1d5db'; e.target.style.boxShadow = 'none'; }}
                        required={required}
                        value={value}
                        onChange={onChange}
                    />
                )}
            </div>
        </div>
    );
}