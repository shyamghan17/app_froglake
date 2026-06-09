import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import SystemSetupSidebar from "../SystemSetupSidebar";
import { Save } from 'lucide-react';

interface ContactSettings {
    title: string;
    description: string;
    google_map_iframe: string;
}

interface ContactSettingsProps {
    settings: ContactSettings;
}

export default function Index({ settings }: ContactSettingsProps) {
    const { t } = useTranslation();
    const [isSubmitting, setIsSubmitting] = useState(false);

    const { data, setData, post, errors, processing } = useForm({
        title: settings.title || '',
        description: settings.description || '',
        google_map_iframe: settings.google_map_iframe || ''
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        post(route('bookings.contact-settings.update'), {
            onFinish: () => setIsSubmitting(false)
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                {label: t('Contact Setting 1111')}
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Contact Setting')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="contact-settings" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Contact Setting')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your contact page settings')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <Label htmlFor="title" required>{t('Title')}</Label>
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
                                    <Label htmlFor="description" required>{t('Description')}</Label>
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

                                <div>
                                    <Label htmlFor="google_map_iframe">{t('Google Map Iframe')}</Label>
                                    <Textarea
                                        id="google_map_iframe"
                                        value={data.google_map_iframe}
                                        onChange={(e) => setData('google_map_iframe', e.target.value)}
                                        placeholder={t('Paste Google Maps embed code here')}
                                        rows={4}
                                    />
                                    <p className="text-xs text-muted-foreground mt-1">
                                        {t('Note: You can get iframe from Google Maps → Share → Embed a map')}
                                    </p>
                                    <InputError message={errors.google_map_iframe} />
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
