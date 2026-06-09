import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, usePage, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import SocialLinks from '@/components/SocialLinks';
import * as LucideIcons from 'lucide-react';
import { Globe, MapPin, Phone, Mail, Clock } from 'lucide-react';
import { useFormFields } from '@/hooks/useFormFields';
import languagesData from '@/../lang/language.json';

interface Props {
    title?: string;
    children: React.ReactNode;
}

export default function Layout({ title = 'Serenity Spa | Beauty Spa Management', children }: Props) {
    const { t } = useTranslation();
    const { beautySpaSettings, customPages, workingHours, auth } = usePage().props as any;
    const { url } = usePage();

    const pathParts = url.split('/');
    const beautyIndex = pathParts.findIndex(part => part === 'beauty-spa');
    const userSlug = beautyIndex !== -1 && pathParts[beautyIndex - 1] ? pathParts[beautyIndex - 1] : null;

    // Construct title with slug and store name
    const storeName = beautySpaSettings?.brand_settings?.beauty_spa_store_name || 'Beauty Spa Management';
    const pageTitle = userSlug ? `${title.split('|')[0].trim()} | ${storeName}` : `${title.split('|')[0].trim()} | ${storeName}`;


    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [languageDropdownOpen, setLanguageDropdownOpen] = useState(false);
    const [selectedLanguage, setSelectedLanguage] = useState('en');
    const [toast, setToast] = useState<{ message: string, type: 'success' | 'error' } | null>(null);
    const { data, setData, post, processing, reset } = useForm({ email: '' });
    const { flash } = usePage().props as any;
    const { i18n } = useTranslation();
    const dropdownRef = useRef<HTMLDivElement>(null);

    const languages = languagesData.filter(lang => lang.enabled !== false);

    useEffect(() => {
        // Check if user has previously selected a language
        const savedLang = sessionStorage.getItem('selectedLanguage');
        const companyLang = auth?.lang || 'en';
        const finalLang = savedLang || companyLang;
        
        setSelectedLanguage(finalLang);
        i18n.changeLanguage(finalLang);

        document.documentElement.dir = 'ltr';
        document.documentElement.lang = finalLang;

        if (flash?.success) {
            setToast({ message: flash.success, type: 'success' });
            setTimeout(() => setToast(null), 3000);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: 'error' });
            setTimeout(() => setToast(null), 3000);
        }
    }, [flash, auth?.lang]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setLanguageDropdownOpen(false);
            }
        };

        if (languageDropdownOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [languageDropdownOpen]);

    const handleLanguageChange = (languageCode: string) => {
        setSelectedLanguage(languageCode);
        setLanguageDropdownOpen(false);
        i18n.changeLanguage(languageCode);
        
        // Save selected language to sessionStorage
        sessionStorage.setItem('selectedLanguage', languageCode);

        document.documentElement.dir = 'ltr';
        document.documentElement.lang = languageCode;
    };

    const currentLang = languages.find(lang => lang.code === selectedLanguage) || languages[0];

    const renderIcon = (iconName: string, size: number = 16) => {
        const IconComponent = LucideIcons[iconName as keyof typeof LucideIcons] as React.ComponentType<{ size?: number }>;
        if (IconComponent) {
            return <IconComponent size={size} />;
        }
        return <MapPin size={size} />;
    };

    const integrationFields = useFormFields('getIntegrationFields', {}, () => { }, {}, 'create', t, 'BeautySpaManagement');


    const handleSubscribe = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa.subscribe', { userSlug }), {
            onSuccess: () => reset()
        });
    };

    return (
        <>
            <Head title={pageTitle}>
                <link
                    rel="icon"
                    type="image/x-icon"
                    href={beautySpaSettings?.brand_settings?.favicon ? getImagePath(beautySpaSettings.brand_settings.favicon) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/favicon.png')}
                />
            </Head>

            {/* Header */}
            <header className="bg-white shadow-sm fixed w-full top-0 z-50">
                <div className="container mx-auto px-4 py-3 flex justify-between items-center">
                    <h1 className="header-logo sm:max-w-[120px] max-w-[110px] w-full">
                        <Link href={route('beauty-spa.home', { userSlug })} className="logo">
                            <img
                                src={beautySpaSettings?.brand_settings?.logo ? getImagePath(beautySpaSettings.brand_settings.logo) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/logo.png')}
                                alt="Serenity Spa Logo"
                                className="h-full"
                            />
                        </Link>
                    </h1>

                    {/* Desktop Navigation */}
                    <nav className="hidden md:flex gap-8 items-center">
                        <Link href={route('beauty-spa.services', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Services')}</Link>
                        <Link href={route('beauty-spa.contact', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Contact Us')}</Link>
                        <Link href={route('beauty-spa.booking', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Book Now')}</Link>
                        <Link href={route('beauty-spa.about', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{('About Us')}</Link>
                    </nav>

                    {/* Language Dropdown */}
                    <div ref={dropdownRef} className="relative inline-block text-left">
                        <button
                            onClick={() => setLanguageDropdownOpen(!languageDropdownOpen)}
                            className="inline-flex items-center justify-center rounded-md border border-[#df9896] bg-[#df9896] text-white shadow-sm md:px-4 px-3 py-2 font-medium hover:bg-transparent hover:text-[#df9896] transition-colors"
                        >
                            <span className="sm:w-[65px] w-[60px] text-left">{currentLang.name}</span>
                            <LucideIcons.ChevronDown className="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" />
                        </button>
                        {languageDropdownOpen && (
                            <div className="absolute end-0 rtl:end-auto rtl:start-0 z-10 mt-2 w-48 origin-top-right rtl:origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                <div className="py-1">
                                    {languages.map((lang) => (
                                        <button
                                            key={lang.code}
                                            onClick={() => handleLanguageChange(lang.code)}
                                            className={`block px-4 py-2 text-sm text-gray-700 hover:bg-[#df9896] hover:text-white w-full text-left rtl:text-right flex items-center justify-between ${selectedLanguage === lang.code ? 'bg-gray-50' : ''
                                                }`}
                                        >
                                            <span>{lang.name}</span>
                                            {selectedLanguage === lang.code && (
                                                <LucideIcons.Check className="w-4 h-4 text-[#df9896]" />
                                            )}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                        className="md:hidden text-[#df9896]"
                    >
                        <LucideIcons.Menu className="w-6 h-6" />
                    </button>
                </div>

                {/* Mobile Navigation Menu */}
                {mobileMenuOpen && (
                    <div className="md:hidden bg-white shadow-md">
                        <div className="container mx-auto px-4 pb-3 pt-1 flex flex-col space-y-3">
                            <Link href={route('beauty-spa.services', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Services')}</Link>
                            <Link href={route('beauty-spa.contact', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Contact Us')} </Link>
                            <Link href={route('beauty-spa.booking', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('Book Now')} </Link>
                            <Link href={route('beauty-spa.about', { userSlug })} className="text-gray-900 hover:text-[#df9896] transition-colors font-medium">{t('About Us')} </Link>
                        </div>
                    </div>
                )}
            </header>

            {/* Toast Notification */}
            {toast && (
                <div className={`fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${toast.type === 'success' ? 'bg-[#df9896]' : 'bg-red-500'
                    }`}>
                    {toast.message}
                </div>
            )}

            {/* Main Content */}
            {children}

            {/* Footer */}
            <footer className="relative overflow-hidden">
                <div className="absolute w-full overflow-hidden top-0 start-0 h-16 -mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" className="h-full w-full" preserveAspectRatio="none">
                        <path fill="#fff" fillOpacity="1" d="M0,256L48,229.3C96,203,192,149,288,144C384,139,480,181,576,208C672,235,768,245,864,224C960,203,1056,149,1152,138.7C1248,128,1344,160,1392,176L1440,192L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
                    </svg>
                </div>

                <div className="bg-[#df98962b] pt-20 pb-10">
                    <div className="container mx-auto px-4">
                        <div className="flex flex-col md:flex-row justify-between md:items-center lg:mb-12 md:mb-10 mb-6 relative z-10">
                            <div className="mb-4 md:mb-0">
                                <Link href={route('beauty-spa.home', { userSlug })} className="logo">
                                    <img
                                        src={beautySpaSettings?.brand_settings?.logo ? getImagePath(beautySpaSettings.brand_settings.logo) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/logo.png')}
                                        alt="Serenity Spa Logo"
                                        className="h-full"
                                    />
                                </Link>
                                <p className="text-gray-800 md:max-w-xs">{beautySpaSettings?.brand_settings?.footer_description}</p>
                            </div>

                            <div className="w-full md:w-auto">
                                <h3 className="text-xl font-semibold mb-4 text-gray-800">{t('Join Our Newsletter')}</h3>
                                <form onSubmit={handleSubscribe} className="flex">
                                    <input
                                        type="email"
                                        placeholder="Enter your email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="px-4 md:py-3 py-2 border border-gray-300 rounded-s-lg focus:outline-none focus:ring-1 focus:ring-[#df9896] focus:border-[#df9896] w-full md:w-64"
                                        required
                                    />
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="bg-[#df9896] border border-[#df9896] hover:bg-white hover:text-[#df9896] hover:border-gray-300 text-white px-6 md:py-3 py-2 rounded-e-lg transition-colors font-medium disabled:opacity-50"
                                    >
                                        {processing ? 'Subscribing...' : 'Subscribe'}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 md:gap-y-12 gap-y-5">
                            <div>
                                <h3 className="text-lg font-semibold md:mb-4 mb-2 flex items-center text-gray-800">
                                    <span className="w-6 h-1 bg-[#df9896] rounded-full me-2"></span>
                                    {t('Quick Links')}
                                </h3>
                                <ul className="space-y-3">
                                    <li>
                                        <Link href={route('beauty-spa.services', { userSlug })} className="text-gray-800 hover:text-[#df9896] transition-colors flex items-center" >
                                            <LucideIcons.ChevronRight size={20} className="me-2 text-[#df9896]" />{t('Services')}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href={route('beauty-spa.contact', { userSlug })} className="text-gray-800 hover:text-[#df9896] transition-colors flex items-center">
                                            <LucideIcons.ChevronRight size={20} className="me-2 text-[#df9896]" />
                                            {t('Contact Us')}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href={route('beauty-spa.booking', { userSlug })} className="text-gray-800 hover:text-[#df9896] transition-colors flex items-center">
                                            <LucideIcons.ChevronRight size={20} className="me-2 text-[#df9896]" />
                                            {t('Book Now')}
                                        </Link>
                                    </li>
                                </ul>
                            </div>

                            {customPages && customPages.length > 0 && (
                                <div>
                                    <h3 className="text-lg font-semibold md:mb-4 mb-2 flex items-center text-gray-800">
                                        <span className="w-6 h-1 bg-[#df9896] rounded-full me-2"></span>
                                        {t('Our Treatments')}
                                    </h3>
                                    <ul className="space-y-3">
                                        {customPages.map((page) => (
                                            <li key={page.id}>
                                                <Link href={route('beauty-spa.custom-page', { userSlug: userSlug, slug: page.slug })} className="text-gray-800 hover:text-[#df9896] transition-colors flex items-center">
                                                    <LucideIcons.ChevronRight size={20} className="me-2 text-[#df9896]" />
                                                    {page.title}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            {(beautySpaSettings?.contact_info?.location || beautySpaSettings?.contact_info?.phone_number || beautySpaSettings?.contact_info?.email_address || workingHours) && (
                                <div>
                                    <h3 className="text-lg font-semibold md:mb-4 mb-2 flex items-center text-gray-800">
                                        <span className="w-6 h-1 bg-[#df9896] rounded-full me-2"></span>
                                        {t('Contact Us')}
                                    </h3>
                                    <ul className="space-y-3">
                                        {beautySpaSettings?.contact_info?.location && (
                                            <li className="text-gray-800 flex items-start gap-3">
                                                <div className="flex items-center justify-center flex-shrink-0 mt-1 text-[#df9896]">
                                                    {beautySpaSettings.contact_info.location_icon ? (
                                                        <SocialLinks
                                                            icon={beautySpaSettings.contact_info.location_icon}
                                                            className="w-4 h-4"
                                                        />
                                                    ) : (
                                                        <MapPin size={16} />
                                                    )}
                                                </div>
                                                <div>
                                                    <span>{beautySpaSettings.contact_info.location}</span>
                                                </div>
                                            </li>
                                        )}
                                        {beautySpaSettings?.contact_info?.phone_number && (
                                            <li className="text-gray-800 flex items-center gap-3">
                                                <div className="flex items-center justify-center text-[#df9896]">
                                                    {beautySpaSettings.contact_info.phone_icon ? (
                                                        <SocialLinks
                                                            icon={beautySpaSettings.contact_info.phone_icon}
                                                            className="w-4 h-4"
                                                        />
                                                    ) : (
                                                        <Phone size={16} />
                                                    )}
                                                </div>
                                                <a href={`tel:${beautySpaSettings.contact_info.phone_number}`}>{beautySpaSettings.contact_info.phone_number}</a>
                                            </li>
                                        )}
                                        {beautySpaSettings?.contact_info?.email_address && (
                                            <li className="text-gray-800 flex items-center gap-3">
                                                <div className="flex items-center justify-center text-[#df9896]">
                                                    {beautySpaSettings.contact_info.email_icon ? (
                                                        <SocialLinks
                                                            icon={beautySpaSettings.contact_info.email_icon}
                                                            className="w-4 h-4"
                                                        />
                                                    ) : (
                                                        <Mail size={16} />
                                                    )}
                                                </div>
                                                <a href={`mailto:${beautySpaSettings.contact_info.email_address}`}>{beautySpaSettings.contact_info.email_address}</a>
                                            </li>
                                        )}
                                        {workingHours && (
                                            <li className="text-gray-800 flex items-center gap-3">
                                                <div className="flex items-center justify-center">
                                                    <Clock className="w-4 h-4 text-[#df9896]" />
                                                </div>
                                                <span>{workingHours ? `${workingHours.day_range}: ${workingHours.opening_time} - ${workingHours.closing_time}` : ''}</span>
                                            </li>
                                        )}
                                    </ul>
                                </div>
                            )}
                            {(beautySpaSettings?.brand_settings?.footer_text || (beautySpaSettings?.social_links?.social_links && beautySpaSettings.social_links.social_links.length > 0)) && (
                                <div>
                                    <h3 className="text-lg font-semibold md:mb-4 mb-2 flex items-center text-gray-800">
                                        <span className="w-6 h-1 bg-[#df9896] rounded-full me-2"></span>
                                        {t('Follow Us')}
                                    </h3>
                                    {beautySpaSettings?.contact_info?.follow_us_description && (
                                        <p className="text-gray-800 mb-4">{beautySpaSettings.contact_info.follow_us_description}</p>
                                    )}
                                    <SocialLinks socialLinks={beautySpaSettings?.social_links?.social_links || []}
                                        variant="light"
                                        size="sm"
                                        style={{ backgroundColor: '#df9896' }} />
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                {/* Integration Widgets (Tawk.to, etc.) */}
                {integrationFields.map((field) => (
                    <div key={field.id}>
                        {field.component}
                    </div>
                ))}
                {(beautySpaSettings?.brand_settings?.footer_text) && (
                    <div className="bg-[#df9896] text-white py-6 relative">
                        <div className="absolute top-0 start-0 w-full h-full opacity-10 pointer-events-none">
                            <div className="absolute top-1/2 start-1/4 w-2 h-2 rounded-full bg-white"></div>
                            <div className="absolute top-1/4 start-1/3 w-1 h-1 rounded-full bg-white"></div>
                            <div className="absolute top-3/4 start-1/2 w-2 h-2 rounded-full bg-white"></div>
                            <div className="absolute top-1/4 start-2/3 w-1 h-1 rounded-full bg-white"></div>
                            <div className="absolute top-1/2 start-3/4 w-2 h-2 rounded-full bg-white"></div>
                        </div>
                        <div className="container mx-auto px-4">
                            <p className="text-center">&copy; {new Date().getFullYear()} {beautySpaSettings?.brand_settings?.footer_text}</p>
                        </div>
                    </div>
                )}
            </footer>
        </>
    );
}
