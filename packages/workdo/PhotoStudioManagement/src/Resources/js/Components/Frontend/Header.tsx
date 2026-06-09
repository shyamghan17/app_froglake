import React, { useState, useEffect, useRef } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';
import SocialLinks from '@/components/SocialLinks';
import { X, Menu, ChevronDown, Check } from 'lucide-react';
import languagesData from '@/../lang/language.json';

interface HeaderProps {
    userSlug?: string;
}

const Header = ({ userSlug = '' }: HeaderProps) => {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [languageDropdownOpen, setLanguageDropdownOpen] = useState(false);
    const [mobileLanguageDropdownOpen, setMobileLanguageDropdownOpen] = useState(false);
    const [selectedLanguage, setSelectedLanguage] = useState('en');
    const { photoStudioSettings, auth } = usePage<{ photoStudioSettings?: any; auth?: any }>().props;
    const { t, i18n } = useTranslation();
    const dropdownRef = useRef<HTMLDivElement>(null);
    const mobileDropdownRef = useRef<HTMLDivElement>(null);

    const languages = languagesData.filter(lang => lang.enabled !== false);

    useEffect(() => {
        const savedLang = sessionStorage.getItem('selectedLanguage');
        const companyLang = (auth as any)?.lang || 'en';
        const finalLang = savedLang || companyLang;
        setSelectedLanguage(finalLang);
        i18n.changeLanguage(finalLang);
        document.documentElement.dir = 'ltr';
        document.documentElement.lang = finalLang;
    }, [(auth as any)?.lang]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setLanguageDropdownOpen(false);
            }
            if (mobileDropdownRef.current && !mobileDropdownRef.current.contains(event.target as Node)) {
                setMobileLanguageDropdownOpen(false);
            }
        };
        if (languageDropdownOpen || mobileLanguageDropdownOpen) document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [languageDropdownOpen, mobileLanguageDropdownOpen]);

    const handleLanguageChange = (languageCode: string) => {
        setSelectedLanguage(languageCode);
        setLanguageDropdownOpen(false);
        setMobileLanguageDropdownOpen(false);
        i18n.changeLanguage(languageCode);
        sessionStorage.setItem('selectedLanguage', languageCode);
        document.documentElement.dir = 'ltr';
        document.documentElement.lang = languageCode;
    };

    const currentLang = languages.find(lang => lang.code === selectedLanguage) || languages[0];
    const brandSettings = photoStudioSettings?.brand_settings || {};
    const footerSection = photoStudioSettings?.footer_section || {};

    const logoUrl = brandSettings.logo
        ? getImagePath(brandSettings.logo)
        : getImagePath('packages/workdo/PhotoStudioManagement/src/Resources/assets/images/logo.png');

    return (
        <header className="sticky top-0 z-50">
            {/* Top Bar */}
            <div className="hidden lg:block bg-[#674B2F] py-3">
                <div className="md:container w-full mx-auto px-4">
                    <div className="flex justify-between items-center">
                        <div className="flex items-center gap-4 text-sm text-white">
                            {footerSection.phone_no && (
                                <a href={`tel:${footerSection.phone_no}`} className="flex items-center gap-2 font-medium hover:text-gray-200 transition duration-300">
                                    <SocialLinks icon={footerSection.phone_icon || 'Phone'} className="w-4 h-4" />
                                    {footerSection.phone_no}
                                </a>
                            )}
                            {footerSection.email && (
                                <a href={`mailto:${footerSection.email}`} className="flex items-center gap-2 font-medium hover:text-gray-200 transition duration-300">
                                    <SocialLinks icon={footerSection.email_icon || 'Mail'} className="w-4 h-4" />
                                    {footerSection.email}
                                </a>
                            )}
                        </div>
                        {footerSection.social_links && footerSection.social_links.length > 0 && (
                            <div className="flex items-center gap-4 text-sm text-white">
                                <span className="font-medium">{t('Visit Us:')}</span>
                                <SocialLinks
                                    socialLinks={footerSection.social_links.map((link: any) => ({
                                        platform: link.platform || 'social',
                                        icon: link.social_icon || link.icon || 'Globe',
                                        url: link.social_link || link.url || '#',
                                        enabled: link.enabled !== false,
                                    }))}
                                    variant="light"
                                    size="sm"
                                    className="text-white hover:text-gray-200 transition duration-300"
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Main Navigation */}
            <nav className="bg-white shadow-md relative z-50">
                <div className="md:container w-full mx-auto px-4">
                    <div className="flex justify-between items-center py-4">
                        {/* Desktop Menu */}
                        <div className="hidden lg:flex flex-1 items-center gap-6">
                            <Link href={route('photo-studio-management.frontend.services', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('Services')}
                            </Link>
                            <Link href={route('photo-studio-management.frontend.portfolio', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('Portfolio')}
                            </Link>
                            <Link href={route('photo-studio-management.frontend.camera-kit', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('Camera Kit')}
                            </Link>
                            <Link href={route('photo-studio-management.frontend.media-awards', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('Awards')}
                            </Link>
                            <Link href={route('photo-studio-management.frontend.faq', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('FAQ')}
                            </Link>
                            <Link href={route('photo-studio-management.frontend.contact', { userSlug })} className="hover:text-primary transition duration-300 font-medium">
                                {t('Contact')}
                            </Link>
                        </div>

                        {/* Logo */}
                        <div className="lg:max-w-[150px] max-w-[120px] w-full">
                            <h1>
                                <Link href={route('photo-studio-management.frontend.index', { userSlug })}>
                                    <img src={logoUrl} alt={t('photo studio logo')} loading="lazy" />
                                </Link>
                            </h1>
                        </div>

                        {/* Language Dropdown + Book Appointment */}
                        <div className="flex-1 flex justify-end items-center gap-3">
                             <div className="hidden lg:block">
                                <Link href={route('photo-studio-management.frontend.appointment', { userSlug })} className="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">
                                    {t('Book appointment')}
                                </Link>
                            </div>
                            <div ref={dropdownRef} className="relative inline-block text-left hidden lg:block">
                                <button
                                    onClick={() => setLanguageDropdownOpen(!languageDropdownOpen)}
                                    className="inline-flex items-center justify-center border border-[#674B2F] bg-[#674B2F] text-white shadow-sm px-4 py-2 font-medium hover:bg-transparent hover:text-[#674B2F] transition-colors"
                                >
                                    <span className="w-[65px] text-left">{currentLang?.name}</span>
                                    <ChevronDown className="w-4 h-4 ml-2" />
                                </button>
                                {languageDropdownOpen && (
                                    <div className="absolute end-0 z-10 mt-2 w-48 origin-top-right bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                        <div className="py-1">
                                            {languages.map((lang) => (
                                                <button
                                                    key={lang.code}
                                                    onClick={() => handleLanguageChange(lang.code)}
                                                    className={`flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-[#674B2F] hover:text-white w-full text-left ${selectedLanguage === lang.code ? 'bg-gray-50' : ''}`}
                                                >
                                                    <span>{lang.name}</span>
                                                    {selectedLanguage === lang.code && <Check className="w-4 h-4 text-[#674B2F]" />}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                           
                            <button
                                onClick={() => {
                                    console.log('Mobile menu button clicked, current state:', mobileMenuOpen);
                                    setMobileMenuOpen(!mobileMenuOpen);
                                }}
                                className="lg:hidden hover:text-primary transition-all duration-300 z-50 relative p-2 text-gray-800"
                                aria-label="Toggle mobile menu"
                            >
                                {mobileMenuOpen ? <X className="w-5 h-5 text-gray-800" /> : <Menu className="w-5 h-5 text-gray-800" />}
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Mobile Menu */}
            <div className={`fixed inset-0 z-[9999] transition-all duration-300 ease-in-out ${
                mobileMenuOpen ? 'visible opacity-100' : 'invisible opacity-0'
            }`}>
                {/* Backdrop */}
                <div 
                    className="absolute inset-0 bg-black bg-opacity-50"
                    onClick={() => setMobileMenuOpen(false)}
                ></div>
                
                {/* Menu Panel */}
                <div className={`absolute top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transition-transform duration-300 ease-in-out ${
                    mobileMenuOpen ? 'translate-x-0' : 'translate-x-full'
                }`}>
                    <div className="p-4 h-full overflow-y-auto">
                        <div className="flex justify-between items-center mb-4 pb-4 border-b">
                            <div className="logo-col max-w-[100px] w-full">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug })} onClick={() => setMobileMenuOpen(false)}>
                                    <img src={logoUrl} alt={t('logo')} loading="lazy" />
                                </Link>
                            </div>
                            <button
                                onClick={() => setMobileMenuOpen(false)}
                                className="bg-gray-100 hover:bg-gray-200 text-gray-800 hover:text-primary transition-all duration-300 p-2 rounded border border-gray-300"
                                aria-label="Close mobile menu"
                            >
                                <X className="w-5 h-5 text-gray-800" />
                            </button>
                        </div>
                        <nav className="space-y-4">
                            <Link href={route('photo-studio-management.frontend.services', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('Services')}</Link>
                            <Link href={route('photo-studio-management.frontend.portfolio', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('Portfolio')}</Link>
                            <Link href={route('photo-studio-management.frontend.camera-kit', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('Camera Kit')}</Link>
                            <Link href={route('photo-studio-management.frontend.media-awards', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('Awards')}</Link>
                            <Link href={route('photo-studio-management.frontend.faq', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('FAQ')}</Link>
                            <Link href={route('photo-studio-management.frontend.contact', { userSlug })} className="block text-base text-gray-800 hover:text-primary transition-all duration-300 py-2 font-medium" onClick={() => setMobileMenuOpen(false)}>{t('Contact')}</Link>
                        </nav>

                        {/* Mobile Language Selector */}
                        <div ref={mobileDropdownRef} className="mt-6 pt-6 border-t border-gray-200">
                            <h4 className="text-sm font-semibold text-gray-600 mb-3">{t('Language')}</h4>
                            <div className="relative">
                                <button
                                    onClick={() => setMobileLanguageDropdownOpen(!mobileLanguageDropdownOpen)}
                                    className="inline-flex items-center justify-between w-full rounded-md border border-[#674B2F] bg-[#674B2F] text-white px-4 py-2 font-medium hover:bg-transparent hover:text-[#674B2F] transition-colors"
                                >
                                    <span>{currentLang?.name}</span>
                                    <ChevronDown className="w-4 h-4 ml-2" />
                                </button>
                                {mobileLanguageDropdownOpen && (
                                    <div className="absolute left-0 right-0 z-10 mt-1 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                        <div className="py-1 max-h-48 overflow-y-auto">
                                            {languages.map((lang) => (
                                                <button
                                                    key={lang.code}
                                                    onClick={() => { handleLanguageChange(lang.code); setMobileMenuOpen(false); }}
                                                    className={`flex items-center justify-between px-4 py-2 text-sm w-full text-left font-medium hover:bg-[#674B2F] hover:text-white ${
                                                        selectedLanguage === lang.code ? 'bg-gray-50 text-gray-900' : 'text-gray-700'
                                                    }`}
                                                >
                                                    <span>{lang.name}</span>
                                                    {selectedLanguage === lang.code && <Check className="w-4 h-4 text-[#674B2F]" />}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                        {(footerSection.phone_no || footerSection.email) && (
                            <div className="mt-6 pt-6 border-t border-gray-200">
                                <h4 className="text-sm font-semibold text-gray-600 mb-4 font-medium">{t('CONTACT INFO')}</h4>
                                <div className="space-y-3">
                                    {footerSection.phone_no && (
                                        <a href={`tel:${footerSection.phone_no}`} className="flex font-medium items-center text-gray-600 hover:text-primary duration-300">
                                            <SocialLinks icon={footerSection.phone_icon || 'Phone'} className="font-medium w-4 h-4 me-3 text-primary" />
                                            {footerSection.phone_no}
                                        </a>
                                    )}
                                    {footerSection.email && (
                                        <a href={`mailto:${footerSection.email}`} className="flex items-center font-medium text-gray-600 hover:text-primary duration-300">
                                            <SocialLinks icon={footerSection.email_icon || 'Mail'} className="font-medium w-4 h-4 me-3 text-primary" />
                                            {footerSection.email}
                                        </a>
                                    )}
                                </div>
                            </div>
                        )}

                        {footerSection.social_links && footerSection.social_links.length > 0 && (
                            <div className="mt-6 flex gap-4">
                                <SocialLinks
                                    socialLinks={footerSection.social_links.map((link: any) => ({
                                        platform: link.platform || 'social',
                                        icon: link.social_icon || link.icon || 'Globe',
                                        url: link.social_link || link.url || '#',
                                        enabled: link.enabled !== false,
                                    }))}
                                    variant="dark"
                                    size="sm"
                                    className="text-base hover:text-primary transition-all duration-300"
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Header;
