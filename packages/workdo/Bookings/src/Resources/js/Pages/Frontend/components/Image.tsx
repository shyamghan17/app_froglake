import React from 'react';
import { getImagePath } from '@/utils/helpers';

interface ImageProps {
    src?: string;
    alt: string;
    className?: string;
    fallback?: string;
    pageProps?: any;
}

export function Image({ src, alt, className = '', fallback, pageProps }: ImageProps) {
    const imageSrc = src ? getImagePath(src, pageProps) : fallback;
    
    return (
        <img
            src={imageSrc}
            alt={alt}
            className={className}
            loading="lazy"
        />
    );
}