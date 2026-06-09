import React from 'react';

interface PageHeaderProps {
    title: string;
    description: string;
    bgColor?: string;
}

export default function PageHeader({ title, description, bgColor = 'bg-[#52816D]' }: PageHeaderProps) {
    return (
        <section className={`pt-24 md:pb-16 pb-12 md:pt-32 text-white`}  style={{ backgroundColor: `${bgColor}` }}>
            <div className="container mx-auto px-4">
                <div className="flex flex-col items-center text-center">
                    <h2 className="text-4xl md:text-5xl font-bold mb-4 capitalize">{title}</h2>
                    <p className="text-lg md:text-xl max-w-2xl">{description}</p>
                </div>
            </div>
        </section>
    );
}