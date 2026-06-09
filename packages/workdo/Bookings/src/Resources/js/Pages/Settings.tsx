import React, { useState, useEffect } from 'react';
import { Head, useForm, usePage, router } from '@inertiajs/react';
import { Settings, Palette, Layout as LayoutIcon, Save, Eye } from 'lucide-react';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { useTranslation } from 'react-i18next';

interface SettingsProps {
    settings: any;
    config_data: {
        general: {
            primary_color: string;
            secondary_color: string;
            font_family: string;
            header_logo: string;
            footer_logo: string;
            header_menu: string[];
            footer_text: string;
        };
        page_sections: {
            [key: string]: {
                [key: string]: {
                    enabled: boolean;
                    title: string;
                    [key: string]: any;
                };
            };
        };
    };
    custom_pages: Array<{
        id: number;
        title: string;
        slug: string;
    }>;
}

export default function SettingsPage({ settings, config_data, custom_pages }: SettingsProps) {
    const { t } = useTranslation();
    const { auth } = usePage<{ auth: { user: { permissions: string[] } } }>().props;
    const [activeTab, setActiveTab] = useState<'general' | 'pages' | 'custom-pages' | 'extra-services' | 'contacts' | 'reviews' | 'business-hours' | 'social-links'>('general');
    const [activeSection, setActiveSection] = useState<'colors' | 'branding' | 'home' | 'about' | 'services' | 'contact' | 'service_detail'>('colors');
    const [isLoading, setIsLoading] = useState(false);

    const handleTabChange = (tabKey: string) => {
        setActiveTab(tabKey as any);
        router.visit(window.location.pathname, { replace: true, preserveState: true });
    };

    const { data, setData, post, processing } = useForm({
        company_name: settings.company_name || '',
        contact_email: settings.contact_email || '',
        contact_phone: settings.contact_phone || '',
        contact_address: settings.contact_address || '',
        config_data: config_data
    });

    const saveSettings = () => {
        setIsLoading(true);
        post(route('bookings.settings.store'), {
            preserveScroll: true,
            onSuccess: (page) => {
                setIsLoading(false);
                if (page.props.flash?.success) {
                    toast.success(page.props.flash.success);
                }
            },
            onError: (errors) => {
                setIsLoading(false);
                toast.error(t('Failed to save settings'));
            }
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Bookings'), url: route('bookings.dashboard')},
                { label: 'Settings' }
            ]}
            pageTitle="Booking Settings"
            pageActions={
                <div className="flex gap-2">
                    <Button 
                        onClick={saveSettings} 
                        disabled={isLoading} 
                        className="text-white"
                        style={{ backgroundColor: 'hsl(var(--primary))' }}
                    >
                        <Save className="h-4 w-4 mr-2" />
                        {isLoading ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            }
        >
            <Head title="Booking Settings" />
            
            <div className="space-y-6">
                <div className="space-y-6">
                        {/* Tab Navigation */}
                        <div className="flex border-b border-gray-200 mb-8">
                            {[
                                { key: 'general', label: t('General'), sections: ['colors', 'branding'] },
                                { key: 'pages', label: t('Pages'), sections: ['home', 'about', 'services', 'contact'] },
                                ...(auth.user?.permissions?.includes('manage-booking-custom-pages') ? [{ key: 'custom-pages', label: t('Custom Pages'), sections: [] }] : []),
                                ...(auth.user?.permissions?.includes('manage-booking-extra-services') ? [{ key: 'extra-services', label: t('Extra Services'), sections: [] }] : []),
                                ...(auth.user?.permissions?.includes('manage-booking-contacts') ? [{ key: 'contacts', label: t('Contacts'), sections: [] }] : []),
                                ...(auth.user?.permissions?.includes('manage-booking-reviews') ? [{ key: 'reviews', label: t('Reviews'), sections: [] }] : []),
                                ...(auth.user?.permissions?.includes('manage-booking-business-hours') ? [{ key: 'business-hours', label: t('Business Hours'), sections: [] }] : []),
                                ...(auth.user?.permissions?.includes('manage-booking-social-links') ? [{ key: 'social-links', label: t('Social Links'), sections: [] }] : [])
                            ].map(tab => (
                                <button
                                    key={tab.key}
                                    onClick={() => {
                                        handleTabChange(tab.key);
                                        if (tab.sections.length > 0) {
                                            setActiveSection(tab.sections[0] as any);
                                        }
                                    }}
                                    className={`px-6 py-3 font-medium text-sm border-b-2 transition-colors ${
                                        activeTab === tab.key
                                            ? 'text-white rounded-t-lg'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    }`}
                                    style={activeTab === tab.key ? {
                                        backgroundColor: 'hsl(var(--primary))',
                                        borderColor: 'hsl(var(--primary))'
                                    } : {}}
                                >
                                    {tab.label}
                                </button>
                            ))}
                        </div>

                        {/* Section Navigation within Tab */}
                        {!['custom-pages', 'extra-services', 'contacts', 'reviews', 'business-hours', 'social-links'].includes(activeTab) && (
                            <div className="flex flex-wrap gap-2 mb-8">
                                {(() => {
                                    const tabSections = {
                                        general: [{ key: 'colors', label: t('Colors') }, { key: 'branding', label: t('Branding') }],
                                        pages: [{ key: 'home', label: t('Home') }, { key: 'about', label: t('About') }, { key: 'services', label: t('Services') }, { key: 'contact', label: t('Contact') }, { key: 'service_detail', label: t('Service Detail') }, { key: 'notfound', label: t('Not Found') }]
                                    };
                                    return tabSections[activeTab]?.map(section => (
                                        <Button
                                            key={section.key}
                                            variant={activeSection === section.key ? "default" : "outline"}
                                            size="sm"
                                            onClick={() => setActiveSection(section.key as any)}
                                            style={activeSection === section.key ? {
                                                backgroundColor: 'hsl(var(--primary))',
                                                borderColor: 'hsl(var(--primary))',
                                                color: 'white'
                                            } : {}}
                                        >
                                            {section.label}
                                        </Button>
                                    )) || [];
                                })()}
                            </div>
                        )}

                        {/* Section Components */}
                        {activeTab === 'custom-pages' ? (
                            <CustomPages key="custom-pages" />
                        ) : activeTab === 'extra-services' ? (
                            <ExtraServices key="extra-services" />
                        ) : activeTab === 'contacts' ? (
                            <Contacts key="contacts" />
                        ) : activeTab === 'reviews' ? (
                            <Reviews key="reviews" />
                        ) : activeTab === 'business-hours' ? (
                            <BusinessHours key="business-hours" />
                        ) : activeTab === 'social-links' ? (
                            <SocialLinks key="social-links" />
                        ) : (
                            <Card>
                                <CardContent className="space-y-6">
                                    {activeSection === 'colors' && <Colors data={data} setData={setData} />}
                                    {activeSection === 'branding' && <Branding data={data} setData={setData} customPages={custom_pages} />}
                                    {['home', 'about', 'services', 'contact', 'service_detail', 'notfound'].includes(activeSection) && (
                                        <PageSections data={data} setData={setData} activeSection={activeSection} />
                                    )}
                                </CardContent>
                            </Card>
                        )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}