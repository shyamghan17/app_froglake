import React from 'react';
import PublicLayout from './components/PublicLayout';
import { Button } from './components';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Home, ArrowLeft } from 'lucide-react';

interface NotFoundProps {
    title?: string;
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
    notFoundSettings?: any;
}

export default function NotFound({ title = 'Page Not Found', userSlug, brandSettings, colorSettings, socialLinks, customPages, footerServices, pageSettings, notFoundSettings }: NotFoundProps) {
    const { t } = useTranslation();
    
    const pageSettingsData = notFoundSettings || pageSettings?.notfound?.notfound || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';

    return (
        <PublicLayout title={pageSettingsData.title || title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            <section className="min-h-screen flex items-center justify-center" style={{ background: `linear-gradient(to bottom right, ${primaryColor}, #10b981)` }}>
                <div className="max-w-4xl mx-auto px-4 text-center text-white">
                    <div className="mb-8">
                        <div className="text-9xl font-bold mb-4 opacity-80">{pageSettingsData.error_code || '404'}</div>
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">{pageSettingsData.heading || pageSettingsData.title || t('Oops! Page Not Found')}</h1>
                        <p className="text-lg md:text-xl mb-8 max-w-2xl mx-auto opacity-90">
                            {pageSettingsData.description || t('The page you\'re looking for seems to have wandered off. Let\'s get you back on track!')}
                        </p>
                    </div>
                    
                    <div className="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                        <Link href={userSlug ? route('booking.home', { userSlug }) : '#'}>
                            <Button 
                                variant="secondary" 
                                size="lg" 
                                className="bg-white hover:bg-gray-100 shadow-lg transform hover:scale-105 flex items-center gap-2"
                                style={{ color: primaryColor }}
                            >
                                <Home className="w-5 h-5" />
                                {pageSettingsData.home_button_text || t('Back to Home')}
                            </Button>
                        </Link>
                        
                        <Button 
                            variant="outline" 
                            size="lg" 
                            onClick={() => window.history.back()}
                            className="bg-transparent border-2 border-white text-white hover:bg-white transform hover:scale-105 flex items-center gap-2"
                            style={{ '--hover-color': primaryColor } as React.CSSProperties}
                            onMouseEnter={(e) => e.currentTarget.style.color = primaryColor}
                            onMouseLeave={(e) => e.currentTarget.style.color = 'white'}
                        >
                            <ArrowLeft className="w-5 h-5" />
                            {pageSettingsData.back_button_text || 'Go Back'}
                        </Button>
                    </div>
                    
                    <div className="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20">
                        <h2 className="text-2xl font-bold mb-6">{pageSettingsData.navigation_title || t('Quick Navigation')}</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {(pageSettingsData.navigation_items || [
                                { icon: 'fas fa-concierge-bell', title: 'Our Services', description: 'Explore our premium services', url: 'booking.services' },
                                { icon: 'fas fa-users', title: 'About Us', description: 'Learn more about our story', url: 'booking.about' },
                                { icon: 'fas fa-envelope', title: 'Contact Us', description: 'Get in touch with our team', url: 'booking.contact' }
                            ]).map((item, i) => (
                                <Link key={i} href={userSlug ? route(item.url, { userSlug }) : '#'} className="group">
                                    <div className="bg-white/10 p-6 rounded-lg hover:bg-white/20 transition-all transform hover:scale-105">
                                        <i className={`${item.icon} text-3xl mb-3 group-hover:scale-110 transition-transform`}></i>
                                        <h3 className="font-semibold mb-2">{item.title}</h3>
                                        <p className="text-sm opacity-80">{item.description}</p>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}