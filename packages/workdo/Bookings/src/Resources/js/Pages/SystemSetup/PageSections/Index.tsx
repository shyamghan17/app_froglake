import { useState, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { toast } from 'sonner';
import SystemSetupSidebar from '../SystemSetupSidebar';
import { Save } from 'lucide-react';


interface SettingsProps {
    settings: any;
    config_data: any;
    auth: any;
}

export default function PageSections() {
    const { t } = useTranslation();
    const { settings, config_data, auth } = usePage<SettingsProps>().props;
    const [isLoading, setIsLoading] = useState(false);
    const [activeSection, setActiveSection] = useState('home');
    const [formData, setFormData] = useState({
        config_data: config_data
    });

    const setData = (key: string, value: any) => {
        setFormData(prev => ({ ...prev, [key]: value }));
    };

    const saveSettings = () => {
        setIsLoading(true);
        router.post(route('bookings.page-sections.update'), formData, {
            preserveScroll: true,
            onSuccess: (page) => {
                setIsLoading(false);
                toast.success(t('Page sections saved successfully'));
            },
            onError: (errors) => {
                setIsLoading(false);
                toast.error(t('Failed to save page sections'));
            }
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                { label: t('Page Sections') }
            ]}
            pageTitle={t('Page Sections')}
        >
            <Head title={t('Page Sections')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="page-sections" />
                </div>

                <div className="flex-1 space-y-8">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Page Sections')}</h3>
                            </div>

                            <div className="flex flex-wrap gap-2 mb-6">
                                {[
                                    { key: 'home', label: t('Home') },
                                    { key: 'about', label: t('About') },
                                    { key: 'services', label: t('Services') },
                                    { key: 'contact', label: t('Contact') },
                                    { key: 'service_detail', label: t('Service Detail') },
                                    { key: 'notfound', label: t('Not Found') }
                                ].map(section => (
                                    <Button
                                        key={section.key}
                                        variant={activeSection === section.key ? "default" : "outline"}
                                        size="sm"
                                        onClick={() => setActiveSection(section.key)}
                                    >
                                        {section.label}
                                    </Button>
                                ))}
                            </div>

                            <div className="space-y-6">
                                <p className="text-gray-500">{t('Page section settings for')} {activeSection}</p>
                            </div>

                            <div className="flex justify-end pt-6 border-t mt-6">
                                <Button onClick={saveSettings} disabled={isLoading}>
                                    <Save className="h-4 w-4 mr-2" />
                                    {isLoading ? t('Saving...') : t('Save Changes')}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
