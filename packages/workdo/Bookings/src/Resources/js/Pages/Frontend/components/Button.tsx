import React from 'react';

interface ButtonProps {
    children: React.ReactNode;
    onClick?: () => void;
    type?: 'button' | 'submit';
    variant?: 'primary' | 'secondary' | 'outline' | 'custom';
    size?: 'sm' | 'md' | 'lg';
    disabled?: boolean;
    className?: string;
    primaryColor?: string;
    style?: React.CSSProperties;
    onMouseEnter?: (e: React.MouseEvent<HTMLButtonElement>) => void;
    onMouseLeave?: (e: React.MouseEvent<HTMLButtonElement>) => void;
}

export function Button({ 
    children, 
    onClick, 
    type = 'button', 
    variant = 'primary', 
    size = 'md', 
    disabled, 
    className = '',
    primaryColor = '#52816D',
    style: customStyle,
    onMouseEnter,
    onMouseLeave
}: ButtonProps) {
    const baseClasses = 'font-semibold rounded-lg transition-colors flex items-center justify-center';
    
    const sizeClasses = {
        sm: 'px-4 py-2 text-sm',
        md: 'px-6 py-3',
        lg: 'px-8 py-4 text-lg'
    };
    
    const variantClasses = {
        primary: `text-white hover:opacity-90`,
        secondary: 'bg-gray-100 text-gray-800 hover:bg-gray-200',
        outline: `border-2 hover:text-white transition-colors`,
        custom: ''
    };
    
    const defaultStyle = variant === 'primary' ? { backgroundColor: primaryColor } : 
                        variant === 'outline' ? { borderColor: primaryColor, color: primaryColor } : 
                        variant === 'custom' ? {} : {};
    const style = { ...defaultStyle, ...customStyle };
    
    return (
        <button
            type={type}
            onClick={onClick}
            disabled={disabled}
            className={`${variant !== 'custom' ? baseClasses : ''} ${variant !== 'custom' ? sizeClasses[size] : ''} ${variantClasses[variant]} ${disabled ? 'opacity-50 cursor-not-allowed' : ''} ${className}`}
            style={style}
            onMouseEnter={onMouseEnter || (variant === 'outline' ? (e) => {
                e.currentTarget.style.backgroundColor = primaryColor;
                e.currentTarget.style.color = 'white';
            } : undefined)}
            onMouseLeave={onMouseLeave || (variant === 'outline' ? (e) => {
                e.currentTarget.style.backgroundColor = 'transparent';
                e.currentTarget.style.color = primaryColor;
            } : undefined)}
        >
            {children}
        </button>
    );
}