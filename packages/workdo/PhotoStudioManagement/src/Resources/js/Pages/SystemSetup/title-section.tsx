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
import SystemSetupSidebar from './SystemSetupSidebar';

interface TitleSectionFormData {
    service_page_title: string;
    service_label: string;
    service_title: string;
    camera_kit_page_title: string;
    camera_kit_label: string;
    camera_kit_title: string;
    camera_kit_details_label: string;
    camera_kit_details_title: string;
    equipment_label: string;
    equipment_title: string;
    booking_page_title: string;
}

export default function TitleSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'title_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<TitleSectionFormData>({
        service_page_title:       parsedData.service_page_title || '',
        service_label:            parsedData.service_label || '',
        service_title:            parsedData.service_title || '',
        camera_kit_page_title:    parsedData.camera_kit_page_title || '',
        camera_kit_label:         parsedData.camera_kit_label || '',
        camera_kit_title:         parsedData.camera_kit_title || '',
        camera_kit_details_label: parsedData.camera_kit_details_label || '',
        camera_kit_details_title: parsedData.camera_kit_details_title || '',
        equipment_label:          parsedData.equipment_label || '',
        equipment_title:          parsedData.equipment_title || '',
        booking_page_title:       parsedData.booking_page_title || '',
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.title-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Title Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Title Section')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="title-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Title Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    {/* Service */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-base font-medium mb-4 block">{t('Service')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-4">
                                                <Label htmlFor="service_page_title">{t('Service Page Title')}</Label>
                                                <Input
                                                    id="service_page_title"
                                                    value={data.service_page_title}
                                                    onChange={(e) => setData('service_page_title', e.target.value)}
                                                    placeholder={t('Enter service page title')}
                                                    required
                                                />
                                                <InputError message={errors.service_page_title} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label htmlFor="service_label">{t('Service Label')}</Label>
                                                <Input
                                                    id="service_label"
                                                    value={data.service_label}
                                                    onChange={(e) => setData('service_label', e.target.value)}
                                                    placeholder={t('Enter service label')}
                                                    required
                                                />
                                                <InputError message={errors.service_label} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label htmlFor="service_title">{t('Service Title')}</Label>
                                                <Input
                                                    id="service_title"
                                                    value={data.service_title}
                                                    onChange={(e) => setData('service_title', e.target.value)}
                                                    placeholder={t('Enter service title')}
                                                    required
                                                />
                                                <InputError message={errors.service_title} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Camera Kit */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-base font-medium mb-4 block">{t('Camera Kit')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-4">
                                                <Label htmlFor="camera_kit_page_title">{t('Camera Kit Page Title')}</Label>
                                                <Input
                                                    id="camera_kit_page_title"
                                                    value={data.camera_kit_page_title}
                                                    onChange={(e) => setData('camera_kit_page_title', e.target.value)}
                                                    placeholder={t('Enter camera kit page title')}
                                                    required
                                                />
                                                <InputError message={errors.camera_kit_page_title} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label htmlFor="camera_kit_label">{t('Camera Kit Label')}</Label>
                                                <Input
                                                    id="camera_kit_label"
                                                    value={data.camera_kit_label}
                                                    onChange={(e) => setData('camera_kit_label', e.target.value)}
                                                    placeholder={t('Enter camera kit label')}
                                                    required
                                                />
                                                <InputError message={errors.camera_kit_label} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label htmlFor="camera_kit_title">{t('Camera Kit Title')}</Label>
                                                <Input
                                                    id="camera_kit_title"
                                                    value={data.camera_kit_title}
                                                    onChange={(e) => setData('camera_kit_title', e.target.value)}
                                                    placeholder={t('Enter camera kit title')}
                                                    required
                                                />
                                                <InputError message={errors.camera_kit_title} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Camera Kit Details */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-base font-medium mb-4 block">{t('Camera Kit Details')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-6">
                                                <Label htmlFor="camera_kit_details_label">{t('Camera Kit Details Label')}</Label>
                                                <Input
                                                    id="camera_kit_details_label"
                                                    value={data.camera_kit_details_label}
                                                    onChange={(e) => setData('camera_kit_details_label', e.target.value)}
                                                    placeholder={t('Enter camera kit details label')}
                                                    required
                                                />
                                                <InputError message={errors.camera_kit_details_label} />
                                            </div>
                                            <div className="col-span-6">
                                                <Label htmlFor="camera_kit_details_title">{t('Camera Kit Details Title')}</Label>
                                                <Input
                                                    id="camera_kit_details_title"
                                                    value={data.camera_kit_details_title}
                                                    onChange={(e) => setData('camera_kit_details_title', e.target.value)}
                                                    placeholder={t('Enter camera kit details title')}
                                                    required
                                                />
                                                <InputError message={errors.camera_kit_details_title} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Equipment */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-base font-medium mb-4 block">{t('Equipment')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-6">
                                                <Label htmlFor="equipment_label">{t('Equipment Label')}</Label>
                                                <Input
                                                    id="equipment_label"
                                                    value={data.equipment_label}
                                                    onChange={(e) => setData('equipment_label', e.target.value)}
                                                    placeholder={t('Enter equipment label')}
                                                    required
                                                />
                                                <InputError message={errors.equipment_label} />
                                            </div>
                                            <div className="col-span-6">
                                                <Label htmlFor="equipment_title">{t('Equipment Title')}</Label>
                                                <Input
                                                    id="equipment_title"
                                                    value={data.equipment_title}
                                                    onChange={(e) => setData('equipment_title', e.target.value)}
                                                    placeholder={t('Enter equipment title')}
                                                    required
                                                />
                                                <InputError message={errors.equipment_title} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Booking */}
                                    <div>
                                        <Label className="text-base font-medium mb-4 block">{t('Booking')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-12">
                                                <Label htmlFor="booking_page_title">{t('Booking Page Title')}</Label>
                                                <Input
                                                    id="booking_page_title"
                                                    value={data.booking_page_title}
                                                    onChange={(e) => setData('booking_page_title', e.target.value)}
                                                    placeholder={t('Enter booking page title')}
                                                    required
                                                />
                                                <InputError message={errors.booking_page_title} />
                                            </div>
                                        </div>
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
