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

interface GallerySectionFormData {
    gallery_page_title: string;
    gallery_label: string;
    gallery_title: string;
    gallery_category_label: string;
    gallery_category_title: string;
    images: any[];
}

export default function GallerySection() {
    const { t } = useTranslation();
    const { photostudiosetups, galleryTypes } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'gallery_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<GallerySectionFormData>({
        gallery_page_title:     parsedData.gallery_page_title || '',
        gallery_label:          parsedData.gallery_label || '',
        gallery_title:          parsedData.gallery_title || '',
        gallery_category_label: parsedData.gallery_category_label || '',
        gallery_category_title: parsedData.gallery_category_title || '',
        images: parsedData.images?.length > 0
            ? parsedData.images.map((image: any, index: number) => ({
                id: `image-${Date.now()}-${index}`,
                ...image,
            }))
            : [{ id: `image-${Date.now()}-0`, image: '', gallery_type_id: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.gallery-section.store'), {
            data: {
                gallery_page_title:     data.gallery_page_title,
                gallery_label:          data.gallery_label,
                gallery_title:          data.gallery_title,
                gallery_category_label: data.gallery_category_label,
                gallery_category_title: data.gallery_category_title,
                images: data.images.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Gallery Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Gallery Section')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="gallery-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Gallery Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-4">
                                            <Label htmlFor="gallery_page_title">{t('Gallery Page Title')}</Label>
                                            <Input
                                                id="gallery_page_title"
                                                value={data.gallery_page_title}
                                                onChange={(e) => setData('gallery_page_title', e.target.value)}
                                                placeholder={t('Enter gallery page title')}
                                                required
                                            />
                                            <InputError message={errors.gallery_page_title} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label htmlFor="gallery_label">{t('Gallery Label')}</Label>
                                            <Input
                                                id="gallery_label"
                                                value={data.gallery_label}
                                                onChange={(e) => setData('gallery_label', e.target.value)}
                                                placeholder={t('Enter gallery label')}
                                                required
                                            />
                                            <InputError message={errors.gallery_label} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label htmlFor="gallery_title">{t('Gallery Title')}</Label>
                                            <Input
                                                id="gallery_title"
                                                value={data.gallery_title}
                                                onChange={(e) => setData('gallery_title', e.target.value)}
                                                placeholder={t('Enter gallery title')}
                                                required
                                            />
                                            <InputError message={errors.gallery_title} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-6">
                                            <Label htmlFor="gallery_category_label">{t('Gallery Category Label')}</Label>
                                            <Input
                                                id="gallery_category_label"
                                                value={data.gallery_category_label}
                                                onChange={(e) => setData('gallery_category_label', e.target.value)}
                                                placeholder={t('Enter gallery category label')}
                                                required
                                            />
                                            <InputError message={errors.gallery_category_label} />
                                        </div>
                                        <div className="col-span-6">
                                            <Label htmlFor="gallery_category_title">{t('Gallery Category Title')}</Label>
                                            <Input
                                                id="gallery_category_title"
                                                value={data.gallery_category_title}
                                                onChange={(e) => setData('gallery_category_title', e.target.value)}
                                                placeholder={t('Enter gallery category title')}
                                                required
                                            />
                                            <InputError message={errors.gallery_category_title} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Gallery Images')}</Label>
                                        <Repeater
                                            fields={[
                                                {
                                                    name: 'gallery_type_id',
                                                    label: t('Gallery Type'),
                                                    type: 'select',
                                                    placeholder: t('Select gallery type'),
                                                    options: galleryTypes || [],
                                                    required: true,
                                                },
                                                {
                                                    name: 'image',
                                                    label: t('Gallery Image'),
                                                    type: 'media',
                                                    placeholder: t('Select gallery image'),
                                                    required: true,
                                                },
                                            ]}
                                            value={data.images}
                                            onChange={(items) => setData('images', items.map(item => ({
                                                ...item,
                                                image: item.image && typeof item.image === 'string' && item.image.includes('/')
                                                    ? item.image.split('/').pop() || item.image
                                                    : item.image,
                                            })))}
                                            addButtonText={t('Add Image')}
                                            deleteTooltipText={t('Remove Image')}
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
