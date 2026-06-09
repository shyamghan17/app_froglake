import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import { Save } from "lucide-react";
import SystemSetupSidebar from './SystemSetupSidebar';
import MediaPicker from '@/components/MediaPicker';
import { getImagePath } from '@/utils/helpers';

interface BannerSectionFormData {
    heading: string;
    title: string;
    image: string;
    description: string;
}

export default function BannerSection() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'banner_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<BannerSectionFormData>({
        heading: parsedData.heading || '',
        title: parsedData.title || '',
        image: parsedData.image || '',
        description: parsedData.description || ''
    });

    useFlashMessages();

    const handleMediaSelect = (url: string | string[]) => {
        const urlString = Array.isArray(url) ? url[0] || '' : url;
        const baseName = urlString ? urlString.split('/').pop() || urlString : '';
        setData('image', baseName);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.banner-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Banner Section') }
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
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Banner Section')}</h3>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="heading">{t('Heading')}</Label>
                                        <Input
                                            id="heading"
                                            value={data.heading}
                                            onChange={(e) => setData('heading', e.target.value)}
                                            placeholder={t('Enter banner heading')}
                                            required
                                        />
                                        <InputError message={errors.heading} />
                                    </div>

                                    <div>
                                        <Label htmlFor="title">{t('Title')}</Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder={t('Enter banner title')}
                                            required
                                        />
                                        <InputError message={errors.title} />
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-10">
                                            <Label>{t('Banner Image')}</Label>
                                            <MediaPicker
                                                value={data.image || ''}
                                                onChange={handleMediaSelect}
                                                placeholder={t('Select banner image...')}
                                                showPreview={false}
                                            />
                                            <InputError message={errors.image} />
                                        </div>
                                        <div className="col-span-2">
                                            <div className="flex items-center justify-center h-24 w-full">
                                                {data.image && (
                                                    <img
                                                        src={getImagePath(data.image)}
                                                        alt={t('Banner Image')}
                                                        className="max-h-full max-w-full object-contain"
                                                    />
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <Label htmlFor="description">{t('Description')}</Label>
                                    <Textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        placeholder={t('Enter banner description')}
                                        rows={4}
                                        required
                                    />
                                    <InputError message={errors.description} />
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