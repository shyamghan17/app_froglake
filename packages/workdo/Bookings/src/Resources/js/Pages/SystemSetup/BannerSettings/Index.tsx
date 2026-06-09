import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import MediaPicker from '@/components/MediaPicker';
import SystemSetupSidebar from "../SystemSetupSidebar";
import { getImagePath } from '@/utils/helpers';
import { Save } from 'lucide-react';

interface BannerSettings {
    title: string;
    description: string;
    banner_image: string;
}

interface BannerSettingsProps {
    settings: BannerSettings;
}

export default function Index() {
    const { t } = useTranslation();
    const { settings } = usePage<BannerSettingsProps>().props;
    const [isSubmitting, setIsSubmitting] = useState(false);

    const { data, setData, post, errors, processing } = useForm({
        title: settings.title || '',
        description: settings.description || '',
        banner_image: settings.banner_image || ''
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        post(route('bookings.banner-settings.update'), {
            onFinish: () => setIsSubmitting(false)
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                {label: t('Banner Settings')}
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Banner Settings')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="banner-settings" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Banner Settings')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your banner section settings')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="title">{t('Title')}</Label>
                                        <Input
                                            id="title"
                                            type="text"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder={t('Enter Title')}
                                            required
                                        />
                                        <InputError message={errors.title} />
                                    </div>

                                    <div>
                                        <Label htmlFor="description">{t('Description')}</Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder={t('Enter Description')}
                                            rows={3}
                                            required
                                        />
                                        <InputError message={errors.description} />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <MediaPicker
                                            label={t('Banner Image')}
                                            value={data.banner_image}
                                            onChange={(value) => setData('banner_image', Array.isArray(value) ? value[0] || '' : value)}
                                            placeholder={t('Select Banner Image...')}
                                            showPreview={false}
                                            multiple={false}
                                            accept="image/*"
                                        />
                                        <InputError message={errors.banner_image} />
                                    </div>
                                    <div>
                                        <div className="flex items-center">
                                            {data.banner_image ? (
                                                <img
                                                    src={getImagePath(data.banner_image)}
                                                    alt={t('Banner Image')}
                                                    className="max-w-full max-h-32 object-contain rounded"
                                                />
                                            ) : (
                                                <div className="w-full h-32 bg-gray-100 rounded flex items-center justify-center text-gray-500">
                                                    {t('No image selected')}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                <div className="flex justify-end">
                                    <Button type="submit" disabled={processing || isSubmitting}>
                                        <Save className="h-4 w-4 mr-2" />
                                        {processing || isSubmitting ? t('Saving...') : t('Save Changes')}
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
