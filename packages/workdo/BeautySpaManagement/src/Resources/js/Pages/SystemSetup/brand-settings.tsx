import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import SystemSetupSidebar from "./SystemSetupSidebar";
import { getImagePath } from '@/utils/helpers';
import MediaPicker from '@/components/MediaPicker';
import { Save } from 'lucide-react';

interface BrandSettingsProps {
    settings: {
        logo?: string;
        favicon?: string;
        footer_text?: string;
        footer_description?: string;
        beauty_spa_store_name?: string;
    };
}

export default function BrandSettings() {
    const { t } = useTranslation();
    const { settings } = usePage<BrandSettingsProps>().props;

    // Default logo paths
    const defaultLogos = {
        logo: getImagePath('/packages/workdo/BeautySpaManagement/src/Resources/assets/images/logo.png'),
        favicon: getImagePath('/packages/workdo/BeautySpaManagement/src/Resources/assets/images/favicon.png')
    };

    const { data, setData, post, processing, errors } = useForm({
        logo: settings?.logo || '',
        favicon: settings?.favicon || '',
        footer_text: settings?.footer_text || '',
        footer_description: settings?.footer_description || '',
        beauty_spa_store_name: settings?.beauty_spa_store_name || '',
    });

    useFlashMessages();

    const handleMediaSelect = (name: string, url: string | string[]) => {
        const urlString = Array.isArray(url) ? url[0] || '' : url;
        const baseName = urlString ? urlString.split('/').pop() || urlString : '';
        setData(name as keyof typeof data, baseName);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.brand-settings.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Brand Settings') }
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
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Brand Setting')}</h3>
                            </div>
                            <form onSubmit={handleSubmit}>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div className="space-y-3">
                                        <Label>{t('Logo')}</Label>
                                        <div className="flex flex-col gap-3">
                                            <div className="border rounded-md p-4 flex items-center justify-center bg-muted/30 h-32">
                                                <img
                                                    src={data.logo ? getImagePath(data.logo) : defaultLogos.logo}
                                                    alt={t('Logo')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            </div>
                                            <MediaPicker
                                                value={data.logo || 'logo.png'}
                                                onChange={(url) => handleMediaSelect('logo', url)}
                                                placeholder={t('Select logo...')}
                                                showPreview={false}
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-3">
                                        <Label>{t('Favicon')}</Label>
                                        <div className="flex flex-col gap-3">
                                            <div className="border rounded-md p-4 flex items-center justify-center bg-muted/30 h-32">
                                                <img
                                                    src={data.favicon ? getImagePath(data.favicon) : defaultLogos.favicon}
                                                    alt={t('Favicon')}
                                                    className="h-16 w-16 object-contain"
                                                />
                                            </div>
                                            <MediaPicker
                                                value={data.favicon || 'favicon.png'}
                                                onChange={(url) => handleMediaSelect('favicon', url)}
                                                placeholder={t('Select favicon...')}
                                                showPreview={false}
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-6 mb-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <Label htmlFor="beauty_spa_store_name">{t('Store Name')}</Label>
                                            <Input
                                                id="beauty_spa_store_name"
                                                value={data.beauty_spa_store_name}
                                                onChange={(e) => setData('beauty_spa_store_name', e.target.value)}
                                                placeholder={t('Enter Store Name')}
                                                required
                                            />
                                            {errors.beauty_spa_store_name && (
                                                <p className="text-red-500 text-sm mt-1">{errors.beauty_spa_store_name}</p>
                                            )}
                                        </div>

                                        <div>
                                            <Label htmlFor="footer_text">{t('Footer Text')}</Label>
                                            <Input
                                                id="footer_text"
                                                value={data.footer_text}
                                                onChange={(e) => setData('footer_text', e.target.value)}
                                                placeholder={t('Enter Footer Text')}
                                                required
                                            />
                                            {errors.footer_text && (
                                                <p className="text-red-500 text-sm mt-1">{errors.footer_text}</p>
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <Label htmlFor="footer_description">{t('Footer Description')}</Label>
                                        <Textarea
                                            id="footer_description"
                                            value={data.footer_description}
                                            onChange={(e) => setData('footer_description', e.target.value)}
                                            placeholder={t('Enter Footer Description')}
                                            rows={3}
                                            required
                                        />
                                        {errors.footer_description && (
                                            <p className="text-red-500 text-sm mt-1">{errors.footer_description}</p>
                                        )}
                                    </div>
                                </div>

                                <div className="flex justify-end">
                                    <Button type="submit" disabled={processing}>
                                        <Save className="h-4 w-4 mr-2" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}