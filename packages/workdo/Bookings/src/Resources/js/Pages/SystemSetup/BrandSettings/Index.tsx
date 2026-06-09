import { useState, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { toast } from 'sonner';
import MediaPicker from '@/components/MediaPicker';
import { getImagePath } from '@/utils/helpers';
import SystemSetupSidebar from '../SystemSetupSidebar';
import { Save } from 'lucide-react';
import { Repeater } from '@/components/ui/repeater';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import languagesData from '@/../lang/language.json';

const languages = languagesData;

const getCountryFlag = (countryCode: string): string => {
    const codePoints = countryCode
        .toUpperCase()
        .split('')
        .map(char => 127397 + char.charCodeAt(0));
    return String.fromCodePoint(...codePoints);
};

interface CustomPage {
    id: number;
    title: string;
    slug: string;
}

interface SettingsProps {
    settings: {
        header_logo: string;
        footer_logo: string;
        favicon: string;
        site_title: string;
        default_language: string;
        show_language_selector: boolean;
        footer_description: string;
        footer_address: string;
        footer_phone: string;
        footer_email: string;
        footer_hours: string;
        footer_copyright: string;
    };
    custom_pages: CustomPage[];
    auth: any;
}

export default function BrandSettings() {
    const { t } = useTranslation();
    const { settings, custom_pages, auth } = usePage<SettingsProps>().props;
    const [isLoading, setIsLoading] = useState(false);
    const canEdit = true;

    const [formSettings, setFormSettings] = useState({
        header_logo: settings?.header_logo || '',
        footer_logo: settings?.footer_logo || '',
        favicon: settings?.favicon || '',
        site_title: settings?.site_title || '',
        default_language: settings?.default_language || 'en',
        show_language_selector: settings?.show_language_selector !== false,
        footer_description: settings?.footer_description || '',
        footer_address: settings?.footer_address || '',
        footer_phone: settings?.footer_phone || '',
        footer_email: settings?.footer_email || '',
        footer_hours: settings?.footer_hours || '',
        footer_copyright: settings?.footer_copyright || '',
    });

    useEffect(() => {
        if (settings) {
            setFormSettings({
                header_logo: settings?.header_logo || '',
                footer_logo: settings?.footer_logo || '',
                favicon: settings?.favicon || '',
                site_title: settings?.site_title || '',
                default_language: settings?.default_language || 'en',
                show_language_selector: settings?.show_language_selector !== false,
                footer_description: settings?.footer_description || '',
                footer_address: settings?.footer_address || '',
                footer_phone: settings?.footer_phone || '',
                footer_email: settings?.footer_email || '',
                footer_hours: settings?.footer_hours || '',
                footer_copyright: settings?.footer_copyright || '',
            });
        }
    }, [settings]);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setFormSettings(prev => ({ ...prev, [name]: value }));
    };

    const handleMediaSelect = (name: string, url: string | string[]) => {
        const urlString = Array.isArray(url) ? url[0] || '' : url;
        const filename = urlString ? urlString.split('/').pop() || '' : '';
        setFormSettings(prev => ({ ...prev, [name]: filename }));
    };

    const saveBrandSettings = () => {
        setIsLoading(true);
        router.post(route('bookings.brand-settings.update'), formSettings, {
            preserveScroll: true,
            onSuccess: (page) => {
                setIsLoading(false);
                toast.success(t('Brand settings saved successfully'));
            },
            onError: (errors) => {
                setIsLoading(false);
                toast.error(t('Failed to save brand settings'));
            }
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                { label: t('Brand Settings') }
            ]}
            pageTitle={t('Brand Settings')}
        >
            <Head title={t('Brand Settings')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="brand-settings" />
                </div>

                <div className="flex-1 space-y-8">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div className="space-y-3">
                                        <Label>{t('Header Logo')}</Label>
                                        <div className="border rounded-md p-4 flex items-center justify-center bg-muted/30 h-32">
                                            {formSettings.header_logo ? (
                                                <img
                                                    src={getImagePath(formSettings.header_logo)}
                                                    alt={t('Header Logo')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            ) : (
                                                <img
                                                    src={getImagePath('packages/workdo/Bookings/src/assets/images/header-log.png')}
                                                    alt={t('Header Logo')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            )}
                                        </div>
                                        <MediaPicker
                                            value={formSettings.header_logo || 'header-log.png'}
                                            onChange={(url) => handleMediaSelect('header_logo', url)}
                                            placeholder={t('Select Header Logo')}
                                            showPreview={false}
                                        />
                                    </div>

                                    <div className="space-y-3">
                                        <Label>{t('Footer Logo')}</Label>
                                        <div className="border rounded-md p-4 flex items-center justify-center bg-gray-800 h-32">
                                            {formSettings.footer_logo ? (
                                                <img
                                                    src={getImagePath(formSettings.footer_logo)}
                                                    alt={t('Footer Logo')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            ) : (
                                                <img
                                                    src={getImagePath('packages/workdo/Bookings/src/assets/images/footer-logo.png')}
                                                    alt={t('Footer Logo')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            )}
                                        </div>
                                        <MediaPicker
                                            value={formSettings.footer_logo || 'footer-logo.png'}
                                            onChange={(url) => handleMediaSelect('footer_logo', url)}
                                            placeholder={t('Select Footer Logo')}
                                            showPreview={false}
                                        />
                                    </div>

                                    <div className="space-y-3">
                                        <Label>{t('Favicon')}</Label>
                                        <div className="border rounded-md p-4 flex items-center justify-center bg-muted/30 h-32">
                                            {formSettings.favicon ? (
                                                <img
                                                    src={getImagePath(formSettings.favicon)}
                                                    alt={t('Favicon')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            ) : (
                                                <img
                                                    src={getImagePath('packages/workdo/Bookings/src/assets/images/favicon.png')}
                                                    alt={t('Favicon')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            )}
                                        </div>
                                        <MediaPicker
                                            value={formSettings.favicon || 'favicon.png'}
                                            onChange={(url) => handleMediaSelect('favicon', url)}
                                            placeholder={t('Select Favicon')}
                                            showPreview={false}
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div className="space-y-3">
                                        <Label htmlFor="site_title">{t('Header Text')}</Label>
                                        <Input
                                            id="site_title"
                                            name="site_title"
                                            value={formSettings.site_title}
                                            onChange={handleInputChange}
                                            placeholder={t('Enter header text')}
                                        />
                                    </div>
                                    <div className="space-y-3">
                                        <Label htmlFor="footer_copyright">{t('Footer Text')}</Label>
                                        <Input
                                            id="footer_copyright"
                                            name="footer_copyright"
                                            value={formSettings.footer_copyright}
                                            onChange={handleInputChange}
                                            placeholder={t('Enter footer text')}
                                        />
                                    </div>
                                    <div className="space-y-3">
                                        <Label htmlFor="footer_email">{t('Email')}</Label>
                                        <Input
                                            id="footer_email"
                                            name="footer_email"
                                            type="email"
                                            value={formSettings.footer_email}
                                            onChange={handleInputChange}
                                            placeholder={t('Enter email address')}
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div className="space-y-3">
                                        <Label htmlFor="footer_phone">{t('Mobile No')}</Label>
                                        <Input
                                            id="footer_phone"
                                            name="footer_phone"
                                            value={formSettings.footer_phone}
                                            onChange={handleInputChange}
                                            placeholder={t('Enter mobile number')}
                                        />
                                    </div>
                                    <div className="space-y-3">
                                        <Label htmlFor="footer_address">{t('Address')}</Label>
                                        <Input
                                            id="footer_address"
                                            name="footer_address"
                                            value={formSettings.footer_address}
                                            onChange={handleInputChange}
                                            placeholder={t('Enter address')}
                                        />
                                    </div>
                                    <div className="space-y-3">
                                        <Label htmlFor="footer_description">{t('Description')}</Label>
                                        <Textarea
                                            id="footer_description"
                                            name="footer_description"
                                            value={formSettings.footer_description}
                                            onChange={handleInputChange}
                                            rows={1}
                                            placeholder={t('Enter description')}
                                        />
                                    </div>
                                </div>
                            </div>

                            {canEdit && (
                                <div className="flex justify-end pt-6 border-t mt-6">
                                    <Button onClick={saveBrandSettings} disabled={isLoading}>
                                        <Save className="h-4 w-4 mr-2" />
                                        {isLoading ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
