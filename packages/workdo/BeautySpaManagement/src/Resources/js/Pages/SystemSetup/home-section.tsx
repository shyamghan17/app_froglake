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
import SystemSetupSidebar from './SystemSetupSidebar';

interface HomeSectionFormData {
    services_title: string;
    services_description: string;
    offers_title: string;
    offers_description: string;
}

export default function HomeSection() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'home_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {
        why_choose_us_title: '',
        why_choose_us_description: ''
    };

    const { data, setData, post, processing, errors } = useForm<HomeSectionFormData>({
        services_title: parsedData.services_title || '',
        services_description: parsedData.services_description || '',
        offers_title: parsedData.offers_title || '',
        offers_description: parsedData.offers_description || ''
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.home-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Home Section') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Home Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="home-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Home Section')}</h3>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <Card className="p-6">
                                    <h3 className="text-lg font-medium mb-4">{t('Services Section')}</h3>
                                    <div className="space-y-4">
                                        <div>
                                            <Label htmlFor="services_title">{t('Services Title')}</Label>
                                            <Input
                                                id="services_title"
                                                value={data.services_title}
                                                onChange={(e) => setData('services_title', e.target.value)}
                                                placeholder={t('Enter services title')}
                                            />
                                            <InputError message={errors.services_title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="services_description">{t('Services Description')}</Label>
                                            <Textarea
                                                id="services_description"
                                                value={data.services_description}
                                                onChange={(e) => setData('services_description', e.target.value)}
                                                placeholder={t('Enter services description')}
                                                rows={3}
                                            />
                                            <InputError message={errors.services_description} />
                                        </div>
                                    </div>
                                </Card>

                                <Card className="p-6">
                                    <h3 className="text-lg font-medium mb-4">{t('Service Offers Section')}</h3>
                                    <div className="space-y-4">
                                        <div>
                                            <Label htmlFor="offers_title">{t('Offers Title')}</Label>
                                            <Input
                                                id="offers_title"
                                                value={data.offers_title}
                                                onChange={(e) => setData('offers_title', e.target.value)}
                                                placeholder={t('Enter offers title')}
                                            />
                                            <InputError message={errors.offers_title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="offers_description">{t('Offers Description')}</Label>
                                            <Textarea
                                                id="offers_description"
                                                value={data.offers_description}
                                                onChange={(e) => setData('offers_description', e.target.value)}
                                                placeholder={t('Enter offers description')}
                                                rows={3}
                                            />
                                            <InputError message={errors.offers_description} />
                                        </div>
                                    </div>
                                </Card>

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