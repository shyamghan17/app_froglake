import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Save } from 'lucide-react';
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface BannerSectionFormData {
    banners: any[];
}

export default function BannerSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'banner_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : { banners: [] };

    const { data, setData, post, processing, errors } = useForm<BannerSectionFormData>({
        banners: parsedData.banners?.length > 0
            ? parsedData.banners.map((b: any, i: number) => ({ id: `banner-${Date.now()}-${i}`, ...b }))
            : [{ id: `banner-${Date.now()}-0`, title: '', sub_title: '', image: '', description: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.banner-section.store'), {
            data: { banners: data.banners.map(({ id, ...item }) => item) },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Banner Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Banner Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="banner-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Banner Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <Repeater
                                    fields={[
                                        { name: 'title', label: t('Banner Title'), type: 'text', placeholder: t('Enter banner title'), required: true, layout: { colSpan: 6 } },
                                        { name: 'sub_title', label: t('Banner Sub Title'), type: 'text', placeholder: t('Enter banner sub title'), required:true, layout: { colSpan: 6 } },
                                        { name: 'image', label: t('Banner Image'), type: 'media', placeholder: t('Select banner image'),required:true, layout: { colSpan: 12 } },
                                        { name: 'description', label: t('Description'), type: 'textarea', placeholder: t('Enter banner description'), required: true, layout: { colSpan: 12 } },
                                    ]}
                                    layout={{ type: 'grid', columns: 12 }}
                                    value={data.banners}
                                    onChange={(items) => setData('banners', items.map(item => ({
                                        ...item,
                                        image: item.image && typeof item.image === 'string' && item.image.includes('/')
                                            ? item.image.split('/').pop() || item.image
                                            : item.image,
                                    })))}
                                    addButtonText={t('Add Banner')}
                                    deleteTooltipText={t('Remove Banner')}
                                    minItems={1}
                                    errors={errors as any}
                                />
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
