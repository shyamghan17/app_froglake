import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import { Save } from "lucide-react";
import SystemSetupSidebar from '../SystemSetupSidebar';

interface AdditionalSettingsData {
    stats: {
        title: string;
        description: string;
    };
    services: {
        title: string;
        description: string;
    };
    service_detail: {
        title: string;
        description: string;
    };
    related_services: {
        title: string;
        description: string;
    };
}

interface AdditionalSettingsProps {
    settings: AdditionalSettingsData;
}

export default function Index({ settings }: AdditionalSettingsProps) {
    const { t } = useTranslation();
    const { auth } = usePage<any>().props;
    const canEdit = auth?.user?.permissions?.includes('edit-booking-additional-settings');

    const { data, setData, post, processing, errors } = useForm<AdditionalSettingsData>(settings);

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.additional-settings.update'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Bookings'), url: route('bookings.dashboard') },
                { label: t('System Setup'), url: route('bookings.brand-settings.index') },
                { label: t('Additional Setting') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Additional Setting')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="additional-settings" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="flex justify-between items-center">
                                    <h3 className="text-lg font-medium">{t('Additional Setting')}</h3>
                                    <Button type="submit" disabled={processing || !canEdit} className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <Card>
                                        <CardContent className="p-6 space-y-4">
                                            <h4 className="text-lg font-medium">{t('Section 1 : Counter Section')}</h4>
                                            <div>
                                                <Label htmlFor="stats-title">{t('Title')}</Label>
                                                <Input
                                                    id="stats-title"
                                                    value={data.stats.title}
                                                    onChange={(e) => setData('stats', { ...data.stats, title: e.target.value })}
                                                    placeholder={t('Enter title')}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['stats.title']} />
                                            </div>
                                            <div>
                                                <Label htmlFor="stats-description">{t('Description')}</Label>
                                                <Textarea
                                                    id="stats-description"
                                                    value={data.stats.description}
                                                    onChange={(e) => setData('stats', { ...data.stats, description: e.target.value })}
                                                    placeholder={t('Enter description')}
                                                    rows={4}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['stats.description']} />
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardContent className="p-6 space-y-4">
                                            <h4 className="text-lg font-medium">{t('Section 2 : Home Page Service Section')}</h4>
                                            <div>
                                                <Label htmlFor="services-title">{t('Title')}</Label>
                                                <Input
                                                    id="services-title"
                                                    value={data.services.title}
                                                    onChange={(e) => setData('services', { ...data.services, title: e.target.value })}
                                                    placeholder={t('Enter title')}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['services.title']} />
                                            </div>
                                            <div>
                                                <Label htmlFor="services-description">{t('Description')}</Label>
                                                <Textarea
                                                    id="services-description"
                                                    value={data.services.description}
                                                    onChange={(e) => setData('services', { ...data.services, description: e.target.value })}
                                                    placeholder={t('Enter description')}
                                                    rows={4}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['services.description']} />
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardContent className="p-6 space-y-4">
                                            <h4 className="text-lg font-medium">{t('Section 3 : Service Section')}</h4>
                                            <div>
                                                <Label htmlFor="service_detail-title">{t('Title')}</Label>
                                                <Input
                                                    id="service_detail-title"
                                                    value={data.service_detail.title}
                                                    onChange={(e) => setData('service_detail', { ...data.service_detail, title: e.target.value })}
                                                    placeholder={t('Enter title')}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['service_detail.title']} />
                                            </div>
                                            <div>
                                                <Label htmlFor="service_detail-description">{t('Description')}</Label>
                                                <Textarea
                                                    id="service_detail-description"
                                                    value={data.service_detail.description}
                                                    onChange={(e) => setData('service_detail', { ...data.service_detail, description: e.target.value })}
                                                    placeholder={t('Enter description')}
                                                    rows={4}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['service_detail.description']} />
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardContent className="p-6 space-y-4">
                                            <h4 className="text-lg font-medium">{t('Section 4 : Service Detail Section')}</h4>
                                            <div>
                                                <Label htmlFor="related_services-title">{t('Title')}</Label>
                                                <Input
                                                    id="related_services-title"
                                                    value={data.related_services.title}
                                                    onChange={(e) => setData('related_services', { ...data.related_services, title: e.target.value })}
                                                    placeholder={t('Enter title')}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['related_services.title']} />
                                            </div>
                                            <div>
                                                <Label htmlFor="related_services-description">{t('Description')}</Label>
                                                <Textarea
                                                    id="related_services-description"
                                                    value={data.related_services.description}
                                                    onChange={(e) => setData('related_services', { ...data.related_services, description: e.target.value })}
                                                    placeholder={t('Enter description')}
                                                    rows={4}
                                                    required
                                                    disabled={!canEdit}
                                                />
                                                <InputError message={errors['related_services.description']} />
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
