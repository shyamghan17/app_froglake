import React from 'react';

interface LabelProps {
    children: React.ReactNode;
    htmlFor?: string;
    className?: string;
    required?: boolean;
    onClick?: (e: React.MouseEvent) => void;
    style?: React.CSSProperties;
}

export function Label({ children, htmlFor, className = '', required, onClick, style }: LabelProps) {
    return (
        <label
            htmlFor={htmlFor}
            onClick={onClick}
            style={style}
            className={`block text-sm font-medium text-gray-900 mb-2 ${className}`}
        >
            {children}
            {required && <span className="text-red-500 ml-1">*</span>}
        </label>
    );
}