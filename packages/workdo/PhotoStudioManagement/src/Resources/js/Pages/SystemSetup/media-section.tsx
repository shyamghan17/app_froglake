import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Save } from 'lucide-react';
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface MediaSectionFormData {
    label: string;
    title: string;
    media_items: any[];
}

export default function MediaSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'media_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : { label: '', title: '', media_items: [] };

    const { data, setData, post, processing, errors } = useForm<MediaSectionFormData>({
        label: parsedData.label || '',
        title: parsedData.title || '',
        media_items: parsedData.media_items?.length > 0
            ? parsedData.media_items.map((item: any, index: number) => ({
                id: `media-${Date.now()}-${index}`,
                ...item,
            }))
            : [{ id: `media-${Date.now()}-0`, media_heading: '', media_image: '', date: '', content_type: '', content: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.media-section.store'), {
            data: {
                label: data.label,
                title: data.title,
                media_items: data.media_items.map(({ id, ...item }) => ({
                    ...item,
                    media_image: item.media_image && typeof item.media_image === 'string' && item.media_image.includes('/')
                        ? item.media_image.split('/').pop() || item.media_image
                        : item.media_image,
                })),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Media Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Media Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="media-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Media Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="label">{t('Label')}</Label>
                                            <Input
                                                id="label"
                                                value={data.label}
                                                onChange={(e) => setData('label', e.target.value)}
                                                placeholder={t('Enter label')}
                                                required
                                            />
                                            <InputError message={errors.label} />
                                        </div>
                                        <div>
                                            <Label htmlFor="title">{t('Title')}</Label>
                                            <Input
                                                id="title"
                                                value={data.title}
                                                onChange={(e) => setData('title', e.target.value)}
                                                placeholder={t('Enter title')}
                                                required
                                            />
                                            <InputError message={errors.title} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Media Items')}</Label>
                                        <Repeater
                                            fields={[
                                                { name: 'media_heading', label: t('Media Heading'), type: 'text', placeholder: t('Enter media heading'), required: true, layout: { colSpan: 6 } },
                                                { name: 'media_image', label: t('Media Image'), type: 'media', placeholder: t('Select media image'), required: true, layout: { colSpan: 6 } },
                                                { name: 'date', label: t('Date'), type: 'date', placeholder: t('Select date'), required: true, layout: { colSpan: 6 } },
                                                { name: 'content_type', label: t('Content Type'), type: 'text', placeholder: t('Enter content type'), required: true, layout: { colSpan: 6 } },
                                                { name: 'content', label: t('Content'), type: 'textarea', placeholder: t('Enter content'), required: true, layout: { colSpan: 12 } },
                                            ]}
                                            layout={{ type: 'grid', columns: 12 }}
                                            value={data.media_items}
                                            onChange={(items) => setData('media_items', items.map(item => ({
                                                ...item,
                                                media_image: item.media_image && typeof item.media_image === 'string' && item.media_image.includes('/')
                                                    ? item.media_image.split('/').pop() || item.media_image
                                                    : item.media_image,
                                            })))}
                                            addButtonText={t('Add Media')}
                                            deleteTooltipText={t('Remove Media')}
                                            minItems={1}
                                            errors={errors as any}
                                        />
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
