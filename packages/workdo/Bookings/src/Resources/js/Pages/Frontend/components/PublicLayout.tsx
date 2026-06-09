import { ReactNode, useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import Header from './Header';
import Footer from './Footer';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import { Button } from './Button';
import { ChevronUp } from 'lucide-react';

interface PublicLayoutProps {
    children: ReactNode;
    title: string;
    userSlug?: string;
    settings?: any; // For backward compatibility
    brandSettings?: {
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
    footerServices?: Array<{
        id: number;
        name: string;
    }>;
}

export default function PublicLayout({ 
    children, 
    title, 
    userSlug, 
    settings,
    brandSettings: propBrandSettings, 
    colorSettings: propColorSettings, 
    socialLinks: propSocialLinks, 
    customPages: propCustomPages,
    footerServices: propFooterServices
}: PublicLayoutProps) {
    // Support both old and new prop formats
    const brandSettings = propBrandSettings || settings?.brand_settings || settings?.brandSettings || {};
    const colorSettings = propColorSettings || settings?.color_settings || settings?.colorSettings || {};
    const socialLinks = propSocialLinks || settings?.social_links || settings?.socialLinks || [];
    const customPages = propCustomPages || settings?.custom_pages || settings?.customPages || [];
    const footerServices = propFooterServices || settings?.footer_services || settings?.footerServices || [];
    const primaryColor = colorSettings.primary_color || '#52816D';
    const secondaryColor = colorSettings.secondary_color || '#ffffff';
    const [showBackToTop, setShowBackToTop] = useState(false);
    const [toast, setToast] = useState<{ message: string, type: 'success' | 'error' } | null>(null);
    const { flash } = usePage().props as any;
    const { i18n } = useTranslation();
    const [currentLanguage, setCurrentLanguage] = useState(i18n.language || brandSettings.default_language || 'en');
    
    const changeLanguage = (languageCode: string) => {
        setCurrentLanguage(languageCode);
        i18n.changeLanguage(languageCode);
        
        const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        const isRTL = rtlLanguages.includes(languageCode);
        document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
        document.documentElement.lang = languageCode;
    };
    
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

        if (flash?.success) {
            setToast({ message: flash.success, type: 'success' });
            setTimeout(() => setToast(null), 3000);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: 'error' });
            setTimeout(() => setToast(null), 3000);
        }
    }, [brandSettings.default_language, i18n, flash]);

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
        <div className="min-h-screen bg-white flex flex-col">
            <Head title={brandSettings?.site_title || title}>
                {brandSettings.favicon && (
                    <link rel="icon" type="image/x-icon" href={getImagePath(brandSettings.favicon)} />
                )}
            </Head>
            
            <Header 
                brandSettings={brandSettings}
                primaryColor={primaryColor}
                userSlug={userSlug}
                customPages={customPages}
                currentLanguage={currentLanguage}
                onLanguageChange={changeLanguage}
            />
            
            {/* Toast Notification */}
            {toast && (
                <div className={`fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
                    toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'
                }`}>
                    {toast.message}
                </div>
            )}
            
            <main className="flex-1">{children}</main>
            
            <Footer 
                brandSettings={brandSettings}
                primaryColor={primaryColor}
                secondaryColor={secondaryColor}
                userSlug={userSlug}
                socialLinks={socialLinks}
                footerServices={footerServices}
            />
            
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
    );
}
