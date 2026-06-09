import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import SystemSetupSidebar from './SystemSetupSidebar';
import { getImagePath } from '@/utils/helpers';
import MediaPicker from '@/components/MediaPicker';
import { Save } from 'lucide-react';
import { IconPicker } from '@/components/ui/icon-picker';

interface BrandSettingsProps {
    settings: {
        logo?: string;
        footer_logo?: string;
        favicon?: string;
        site_title?: string;
        footer_text?: string;
        footer_description?: string;
        copy_link_card_title?: string;
        copy_link_card_description?: string;
        copy_link_button_text?: string;
        copy_link_button_icon?: string;
    };
}

export default function BrandSettings() {
    const { t } = useTranslation();
    const { settings } = usePage<BrandSettingsProps>().props;

    const defaultLogos = {
        logo: getImagePath('/packages/workdo/PhotoStudioManagement/src/Resources/assets/images/logo.png'),
        footer_logo: getImagePath('/packages/workdo/PhotoStudioManagement/src/Resources/assets/images/footer-logo.png'),
        favicon: getImagePath('/packages/workdo/PhotoStudioManagement/src/Resources/assets/images/favicon.png'),
    };

    const { data, setData, post, processing, errors } = useForm({
        logo: settings?.logo || '',
        footer_logo: settings?.footer_logo || '',
        favicon: settings?.favicon || '',
        site_title: settings?.site_title || '',
        footer_text: settings?.footer_text || '',
        footer_description: settings?.footer_description || '',
    });

    const { data: welcomeData, setData: setWelcomeData, post: postWelcome, processing: welcomeProcessing, errors: welcomeErrors } = useForm({
        copy_link_card_title:       settings?.copy_link_card_title || '',
        copy_link_card_description: settings?.copy_link_card_description || '',
        copy_link_button_text:      settings?.copy_link_button_text || '',
        copy_link_button_icon:      settings?.copy_link_button_icon || 'Copy',
    });

    useFlashMessages();

    const handleMediaSelect = (name: string, url: string | string[]) => {
        const urlString = Array.isArray(url) ? url[0] || '' : url;
        setData(name as keyof typeof data, urlString ? urlString.split('/').pop() || urlString : '');
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.brand-settings.store'));
    };

    const handleWelcomeSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        postWelcome(route('photo-studio-management.dashboard-welcome-card.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Brand Settings') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Brand Settings')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="brand-settings" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Brand Setting')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    {([
                                        { key: 'logo', label: t('Logo'), bg: 'bg-muted/30', default: defaultLogos.logo },
                                        { key: 'footer_logo', label: t('Footer Logo'), bg: 'bg-gray-800', default: defaultLogos.footer_logo },
                                        { key: 'favicon', label: t('Favicon'), bg: 'bg-muted/30', default: defaultLogos.favicon },
                                    ] as const).map(({ key, label, bg, default: def }) => (
                                        <div key={key} className="space-y-3">
                                            <Label>{label}</Label>
                                            <div className="flex flex-col gap-3">
                                                <div className={`border rounded-md p-4 flex items-center justify-center ${bg} h-32`}>
                                                    <img
                                                        src={data[key] ? getImagePath(data[key]) : def}
                                                        alt={label}
                                                        className="max-h-full max-w-full object-contain"
                                                    />
                                                </div>
                                                <MediaPicker
                                                    value={data[key]}
                                                    onChange={(url) => handleMediaSelect(key, url)}
                                                    placeholder={t(`Select ${label.toLowerCase()}...`)}
                                                    showPreview={false}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <Label htmlFor="site_title">{t('Site Title')}</Label>
                                            <Input id="site_title" value={data.site_title} onChange={(e) => setData('site_title', e.target.value)} placeholder={t('Enter Site Title')} required />
                                            {errors.site_title && <p className="text-red-500 text-sm mt-1">{errors.site_title}</p>}
                                        </div>
                                        <div>
                                            <Label htmlFor="footer_text">{t('Footer Text')}</Label>
                                            <Input id="footer_text" value={data.footer_text} onChange={(e) => setData('footer_text', e.target.value)} placeholder={t('Enter Footer Text')} required />
                                            {errors.footer_text && <p className="text-red-500 text-sm mt-1">{errors.footer_text}</p>}
                                        </div>
                                    </div>
                                    <div>
                                        <Label htmlFor="footer_description">{t('Footer Description')}</Label>
                                        <Textarea id="footer_description" value={data.footer_description} onChange={(e) => setData('footer_description', e.target.value)} placeholder={t('Enter Footer Description')} rows={3} required />
                                        {errors.footer_description && <p className="text-red-500 text-sm mt-1">{errors.footer_description}</p>}
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                    {/* Dashboard Welcome Card Settings */}
                    <Card className="shadow-sm mt-6">
                        <CardContent className="p-6">
                            <form onSubmit={handleWelcomeSubmit} className="space-y-6">
                                <div className="flex justify-between items-center">
                                    <div>
                                        <h3 className="text-lg font-medium">{t('Dashboard Welcome Card Settings')}</h3>
                                        <p className="text-sm text-muted-foreground">{t('Configure the title and description for the dashboard welcome card')}</p>
                                    </div>
                                    <Button type="submit" disabled={welcomeProcessing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {welcomeProcessing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="grid grid-cols-1 gap-4">
                                    <div>
                                        <Label htmlFor="copy_link_card_title">{t('Card Title')}</Label>
                                        <Input
                                            id="copy_link_card_title"
                                            value={welcomeData.copy_link_card_title}
                                            onChange={(e) => setWelcomeData('copy_link_card_title', e.target.value)}
                                            placeholder={t('Enter welcome card title')}
                                            required
                                        />
                                        {welcomeErrors.copy_link_card_title && <p className="text-red-500 text-sm mt-1">{welcomeErrors.copy_link_card_title}</p>}
                                    </div>
                                    <div>
                                        <Label htmlFor="copy_link_card_description">{t('Card Description')}</Label>
                                        <Textarea
                                            id="copy_link_card_description"
                                            value={welcomeData.copy_link_card_description}
                                            onChange={(e) => setWelcomeData('copy_link_card_description', e.target.value)}
                                            placeholder={t('Enter welcome card description')}
                                            rows={3}
                                            required
                                        />
                                        {welcomeErrors.copy_link_card_description && <p className="text-red-500 text-sm mt-1">{welcomeErrors.copy_link_card_description}</p>}
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="copy_link_button_text">{t('Button Text')}</Label>
                                            <Input
                                                id="copy_link_button_text"
                                                value={welcomeData.copy_link_button_text}
                                                onChange={(e) => setWelcomeData('copy_link_button_text', e.target.value)}
                                                placeholder={t('Enter button text')}
                                                required
                                            />
                                            {welcomeErrors.copy_link_button_text && <p className="text-red-500 text-sm mt-1">{welcomeErrors.copy_link_button_text}</p>}
                                        </div>
                                        <div>
                                            <Label htmlFor="copy_link_button_icon">{t('Button Icon')}</Label>
                                            <IconPicker
                                                value={welcomeData.copy_link_button_icon}
                                                onChange={(value) => setWelcomeData('copy_link_button_icon', value)}
                                            />
                                            {welcomeErrors.copy_link_button_icon && <p className="text-red-500 text-sm mt-1">{welcomeErrors.copy_link_button_icon}</p>}
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
