import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { BookingLanguageSwitcher } from '../../../components/BookingLanguageSwitcher';
import { getImagePath } from '@/utils/helpers';
import { Image } from './Image';

interface HeaderProps {
    brandSettings: {
        logo?: string;
        site_title?: string;
        show_language_selector?: boolean;
        default_language?: string;
        userSlug?: string;
    };
    primaryColor: string;
    userSlug?: string;
    customPages?: Array<{
        id: number;
        title: string;
        slug: string;
    }>;
    currentLanguage: string;
    onLanguageChange: (lang: string) => void;
}

export default function Header({ 
    brandSettings, 
    primaryColor, 
    userSlug, 
    customPages = [],
    currentLanguage,
    onLanguageChange
}: HeaderProps) {
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    
    const defaultNavigationItems = [
        { text: 'Services', href: userSlug ? route('booking.services', { userSlug }) : '/booking/services' },
        { text: 'Contact Us', href: userSlug ? route('booking.contact', { userSlug }) : '/booking/contact' },
        { text: 'About Us', href: userSlug ? route('booking.about', { userSlug }) : '/booking/about' }
    ];
    
    const allNavigationItems = [
        ...defaultNavigationItems,
        ...customPages.map(page => ({
            text: page.title,
            href: userSlug ? route('booking.custom-page', { userSlug, slug: page.slug }) : `/booking/page/${page.slug}`
        }))
    ];

    return (
        <header className="fixed w-full bg-white shadow-md z-50 transition-all duration-300">
            <div className="container mx-auto px-4 py-3 flex justify-between items-center">
                <div className="flex-shrink-0 order-1 rtl:order-3">
                    <Link href={userSlug ? route('booking.home', { userSlug }) : '#'}>
                        <Image
                            src={brandSettings.logo ? getImagePath(brandSettings.logo) : getImagePath('packages/workdo/Bookings/src/assets/images/header-log.png')}
                            alt={brandSettings.site_title || 'Service Bookings Addon'}
                            className="h-8 md:h-10 object-scale-down"
                        />
                    </Link>
                </div>
                
                <nav className="hidden md:flex items-center gap-6 order-2">
                    {allNavigationItems.map((item, index) => (
                        <Link 
                            key={index}
                            href={item.href}
                            className="nav-link transition text-gray-700"
                            style={{ '--hover-color': primaryColor } as React.CSSProperties}
                            onMouseEnter={(e) => e.currentTarget.style.color = primaryColor}
                            onMouseLeave={(e) => e.currentTarget.style.color = '#374151'}
                        >
                            {item.text}
                        </Link>
                    ))}
                </nav>
                
                <div className="flex items-center gap-2 order-3 rtl:order-1">
                    {(brandSettings.show_language_selector !== false && brandSettings.show_language_selector !== 'false') && (
                        <BookingLanguageSwitcher 
                            primaryColor={primaryColor}
                            currentLanguage={currentLanguage}
                            onLanguageChange={onLanguageChange}
                        />
                    )}
                    <button
                        type="button"
                        className="md:hidden focus:outline-none p-2 rounded-md transition-all duration-300"
                        style={{ color: primaryColor }}
                        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                    >
                        <div className="relative w-6 h-6 flex flex-col justify-center items-center">
                            <span className={`block h-0.5 w-6 bg-current transition-all duration-300 absolute ${isMobileMenuOpen ? 'rotate-45' : '-translate-y-1.5'}`}></span>
                            <span className={`block h-0.5 w-6 bg-current transition-all duration-300 ${isMobileMenuOpen ? 'opacity-0' : 'opacity-100'}`}></span>
                            <span className={`block h-0.5 w-6 bg-current transition-all duration-300 absolute ${isMobileMenuOpen ? '-rotate-45' : 'translate-y-1.5'}`}></span>
                        </div>
                    </button>
                </div>
            </div>
            
            <div className={`md:hidden bg-white shadow-lg w-full absolute top-full left-0 py-3 px-4 transition-all duration-300 ${isMobileMenuOpen ? 'block' : 'hidden'}`}>
                <nav className="flex flex-col space-y-4">
                    {allNavigationItems.map((item, index) => (
                        <Link 
                            key={index}
                            href={item.href}
                            className="nav-link transition py-2 text-gray-700"
                            style={{ '--hover-color': primaryColor } as React.CSSProperties}
                            onMouseEnter={(e) => e.currentTarget.style.color = primaryColor}
                            onMouseLeave={(e) => e.currentTarget.style.color = '#374151'}
                            onClick={() => setIsMobileMenuOpen(false)}
                        >
                            {item.text}
                        </Link>
                    ))}
                </nav>
            </div>
        </header>
    );
}
