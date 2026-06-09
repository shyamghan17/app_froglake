import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import SocialLinks from '@/components/SocialLinks';
import { BookingLanguageSwitcher } from './BookingLanguageSwitcher';
import { useTranslation } from 'react-i18next';
import { Calendar, Phone, Mail, MapPin, Globe, Menu, X, ChevronUp } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';
import { Image } from '../Pages/Frontend/components/Image';
import { Button } from '../Pages/Frontend/components/Button';

interface LayoutProps {
    title: string;
    userSlug?: string;
    children: React.ReactNode;
    brandSettings: {
        logo?: string;
        footer_logo?: string;
        favicon?: string;
        site_title?: string;
        show_language_selector?: boolean;
        default_language?: string;
        footer_description?: string;
        footer_copyright?: string;
        footer_contact_title?: string;
        footer_address?: string;
        footer_phone?: string;
        footer_email?: string;
        footer_hours?: string;
        userSlug?: string;
    };
    colorSettings?: {
        primary_color?: string;
        secondary_color?: string;
    };
    socialLinks?: Array<{
        name: string;
        icon: string;
        link?: string;
    }>;
    customPages?: Array<{
        id: number;
        title: string;
        slug: string;
    }>;
}

export default function Layout({ title, userSlug, children, brandSettings = {}, colorSettings = {}, socialLinks = [], customPages = [] }: LayoutProps) {
    const colors = colorSettings;
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';
    const [showBackToTop, setShowBackToTop] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const { i18n, t } = useTranslation();
    const [currentLanguage, setCurrentLanguage] = useState(i18n.language || brandSettings.default_language || 'en');
    
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
    
    const changeLanguage = (languageCode: string) => {
        setCurrentLanguage(languageCode);
        i18n.changeLanguage(languageCode);
        
        // Set document direction based on language
        const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        const isRTL = rtlLanguages.includes(languageCode);
        document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
        document.documentElement.lang = languageCode;
    };
    
    // Load default language on mount
    useEffect(() => {
        const defaultLang = brandSettings.default_language || 'en';
        if (i18n.language !== defaultLang) {
            i18n.changeLanguage(defaultLang);
            setCurrentLanguage(defaultLang);
        }
        
        const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        const isRTL = rtlLanguages.includes(defaultLang);
        document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
        document.documentElement.lang = defaultLang;
    }, [brandSettings.default_language, i18n]);

    useEffect(() => {
        const handleScroll = () => {
            setShowBackToTop(window.scrollY > 300);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return (
        <>
            <Head title={brandSettings?.site_title || title}>
                {brandSettings.favicon && (
                    <link rel="icon" type="image/x-icon" href={getImagePath(brandSettings.favicon)} />
                )}
            </Head>
            <div className="min-h-screen bg-white">
                <header className="fixed w-full bg-white shadow-md z-50 transition-all duration-300">
                    <div className="container mx-auto px-4 py-3 flex justify-between items-center">
                        {/* Logo */}
                        <div className="flex-shrink-0 order-1 rtl:order-3">
                            <Link href={userSlug ? route('booking.home', { userSlug: userSlug }) : '#'}>
                                <Image
                                    src={brandSettings.logo ? getImagePath(brandSettings.logo) : getImagePath('packages/workdo/Bookings/src/assets/images/header-log.png')}
                                    alt={brandSettings.site_title || 'Service Bookings Addon'}
                                    className="h-8 md:h-10 object-scale-down"
                                />
                            </Link>
                        </div>
                        
                        {/* Desktop Navigation */}
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
                        
                        {/* Language Switcher & Mobile Menu */}
                        <div className="flex items-center gap-2 order-3 rtl:order-1">
                            {(brandSettings.show_language_selector !== false && brandSettings.show_language_selector !== 'false') && (
                                <BookingLanguageSwitcher 
                                    primaryColor={primaryColor}
                                    currentLanguage={currentLanguage}
                                    onLanguageChange={changeLanguage}
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
                    
                    {/* Mobile Navigation Menu */}
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
                
                <main>{children}</main>
                
                <footer className="text-white" style={{ backgroundColor: primaryColor }}>
                    <div className="container mx-auto px-4">
                        <div className="sm:py-12 py-6 grid grid-cols-1 lg:grid-cols-4 md:grid-cols-2 lg:gap-8 gap-6">
                            <div>
                                <Link href={userSlug ? route('booking.home', { userSlug: userSlug }) : '#'} className="inline-block md:mb-5 mb-4">
                                    <Image
                                        src={brandSettings?.footer_logo ? getImagePath(brandSettings.footer_logo) : getImagePath('packages/workdo/Bookings/src/assets/images/footer-logo.png')}
                                        alt={brandSettings.site_title || 'Service Bookings Addon'}
                                        className="h-10 rounded"
                                    />
                                </Link>
                                <p className="text-white mb-6">
                                    {brandSettings?.footer_description || 'Streamline your service booking process with our powerful, customizable booking addon solution. Perfect for businesses of all sizes.'}
                                </p>
                                <SocialLinks socialLinks={socialLinks} variant="light" style={{ color: primaryColor, backgroundColor: secondaryColor }} />
                            </div>
                            <div>
                                <h3 className="text-xl font-bold sm:mb-6 mb-4">{brandSettings?.footer_contact_title || 'Contact Information'}</h3>
                                <ul className="space-y-4">
                                    {brandSettings?.footer_address && (
                                        <li className="flex items-start">
                                            <MapPin className="w-4 h-4 mt-1 mr-3 text-white" />
                                            <span>{brandSettings.footer_address}</span>
                                        </li>
                                    )}
                                    {brandSettings?.footer_phone && (
                                        <li className="flex items-center">
                                            <Phone className="w-4 h-4 mr-3 text-white" />
                                            <a href={`tel:${brandSettings.footer_phone}`}>{brandSettings.footer_phone}</a>
                                        </li>
                                    )}
                                    {brandSettings?.footer_email && (
                                        <li className="flex items-center">
                                            <Mail className="w-4 h-4 mr-3 text-white" />
                                            <a href={`mailto:${brandSettings.footer_email}`}>{brandSettings.footer_email}</a>
                                        </li>
                                    )}
                                    {brandSettings?.footer_hours && (
                                        <li className="flex items-center">
                                            <svg className="w-4 h-4 mr-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" /></svg>
                                            <span>{brandSettings.footer_hours}</span>
                                        </li>
                                    )}
                                </ul>
                            </div>
                        </div>
                        <div className="md:py-6 py-4 border-t border-white text-center">
                            <p className="text-white">{brandSettings?.footer_copyright || '© 2025 Booking Addon. All rights reserved.'}</p>
                        </div>
                    </div>
                </footer>
                
                {/* Back to Top Button */}
                <Button
                    variant="custom"
                    onClick={scrollToTop}
                    className={`fixed bottom-8 right-8 rtl:left-8 rtl:right-auto text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg border-2 border-white transition-all duration-300 hover:bg-white z-50 ${
                        showBackToTop ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible translate-y-4'
                    }`}
                    style={{ backgroundColor: primaryColor, '--hover-color': primaryColor } as React.CSSProperties}
                    onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = 'white'; e.currentTarget.style.color = primaryColor; }}
                    onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = primaryColor; e.currentTarget.style.color = 'white'; }}
                >
                    <ChevronUp className="w-5 h-5" />
                </Button>
            </div>
        </>
    );
}