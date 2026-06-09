import React from 'react';
import { LucideIcon } from 'lucide-react';

interface ButtonProps {
    children: React.ReactNode;
    variant?: 'primary' | 'secondary' | 'outline';
    size?: 'sm' | 'md' | 'lg';
    icon?: LucideIcon;
    iconPosition?: 'left' | 'right';
    className?: string;
    onClick?: () => void;
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    fullWidth?: boolean;
    primaryColor?: string;
}

export default function Button({
    children,
    variant = 'primary',
    size = 'md',
    icon: Icon,
    iconPosition = 'left',
    className = '',
    onClick,
    type = 'button',
    disabled = false,
    fullWidth = false,
    primaryColor = '#52816D'
}: ButtonProps) {
    const baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-6 py-3 text-base',
        lg: 'px-8 py-4 text-lg'
    };
    
    const baseVariantClasses = {
        primary: 'text-white border-2',
        secondary: 'bg-white border-2',
        outline: 'bg-transparent border-2'
    };
    
    const classes = `
        ${baseClasses}
        ${sizeClasses[size]}
        ${baseVariantClasses[variant]}
        ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
        ${fullWidth ? 'w-full' : ''}
        ${className}
    `.trim();

    const getButtonStyle = () => {
        const baseStyle = {
            borderColor: primaryColor,
            '--focus-ring-color': primaryColor
        } as React.CSSProperties;

        if (variant === 'primary') {
            return { ...baseStyle, backgroundColor: primaryColor, color: 'white' };
        } else if (variant === 'secondary') {
            return { ...baseStyle, color: primaryColor };
        } else {
            return { ...baseStyle, color: primaryColor };
        }
    };

    return (
        <button
            type={type}
            className={classes}
            style={getButtonStyle()}
            onMouseEnter={(e) => {
                if (!disabled) {
                    if (variant === 'primary') {
                        e.currentTarget.style.backgroundColor = 'white';
                        e.currentTarget.style.color = primaryColor;
                    } else {
                        e.currentTarget.style.backgroundColor = primaryColor;
                        e.currentTarget.style.color = 'white';
                    }
                }
            }}
            onMouseLeave={(e) => {
                if (!disabled) {
                    if (variant === 'primary') {
                        e.currentTarget.style.backgroundColor = primaryColor;
                        e.currentTarget.style.color = 'white';
                    } else {
                        e.currentTarget.style.backgroundColor = variant === 'secondary' ? 'white' : 'transparent';
                        e.currentTarget.style.color = primaryColor;
                    }
                }
            }}
            onClick={onClick}
            disabled={disabled}
        >
            {Icon && iconPosition === 'left' && <Icon className="w-4 h-4 mr-2" />}
            {children}
            {Icon && iconPosition === 'right' && <Icon className="w-4 h-4 ml-2" />}
        </button>
    );
}