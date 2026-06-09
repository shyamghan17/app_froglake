import React from 'react';

interface CardProps {
    title: string;
    description: string;
    icon?: React.ReactNode;
    className?: string;
}

export default function Card({ title, description, icon, className = '' }: CardProps) {
    return (
        <div className={`bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow ${className}`}>
            {icon && (
                <div className="mb-4 text-blue-600">
                    {icon}
                </div>
            )}
            <h3 className="text-xl font-semibold mb-3 text-gray-900">{title}</h3>
            <p className="text-gray-600">{description}</p>
        </div>
    );
}