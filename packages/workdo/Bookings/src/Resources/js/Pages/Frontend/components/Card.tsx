import React from 'react';

interface CardProps {
    children: React.ReactNode;
    className?: string;
    padding?: 'sm' | 'md' | 'lg';
    shadow?: boolean;
    rounded?: boolean;
}

export function Card({ children, className = '', padding = 'md', shadow = true, rounded = true }: CardProps) {
    const paddingClasses = {
        sm: 'p-4',
        md: 'p-6',
        lg: 'p-8'
    };
    
    return (
        <div className={`bg-white ${rounded ? 'rounded-xl' : ''} ${shadow ? 'shadow-lg' : ''} ${paddingClasses[padding]} ${className}`}>
            {children}
        </div>
    );
}