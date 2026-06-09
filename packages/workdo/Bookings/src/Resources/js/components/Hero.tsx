import React from 'react';
import { Link } from '@inertiajs/react';

interface HeroProps {
    title: string;
    subtitle: string;
    buttonText?: string;
    buttonLink?: string;
}

export default function Hero({ title, subtitle, buttonText, buttonLink }: HeroProps) {
    return (
        <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 className="text-4xl md:text-6xl font-bold mb-6">{title}</h1>
                <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">{subtitle}</p>
                {buttonText && buttonLink && (
                    <Link 
                        href={buttonLink}
                        className="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                    >
                        {buttonText}
                    </Link>
                )}
            </div>
        </div>
    );
}