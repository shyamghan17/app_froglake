import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import { Save } from 'lucide-react';
import { IconPicker } from '@/components/ui/icon-picker';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import SystemSetupSidebar from './SystemSetupSidebar';

interface ContactSectionFormData {
    contact_page_title: string;
    location_title: string;
    contact_title: string;
    email_title: string;
    visit_address: string;
    call_details: string;
    support_email: string;
    location_icon: string;
    contact_icon: string;
    email_icon: string;
    google_map_iframe: string;
}

export default function ContactSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'contact_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<ContactSectionFormData>({
        contact_page_title: parsedData.contact_page_title || '',
        location_title:     parsedData.location_title || '',
        contact_title:      parsedData.contact_title || '',
        email_title:        parsedData.email_title || '',
        visit_address:      parsedData.visit_address || '',
        call_details:       parsedData.call_details || '',
        support_email:      parsedData.support_email || '',
        location_icon:      parsedData.location_icon || '',
        contact_icon:       parsedData.contact_icon || '',
        email_icon:         parsedData.email_icon || '',
        google_map_iframe:  parsedData.google_map_iframe || '',
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.contact-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Contact Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Contact Section')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="contact-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Contact Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div>
                                        <Label htmlFor="contact_page_title">{t('Contact Page Title')}</Label>
                                        <Input
                                            id="contact_page_title"
                                            value={data.contact_page_title}
                                            onChange={(e) => setData('contact_page_title', e.target.value)}
                                            placeholder={t('Enter contact page title')}
                                            required
                                        />
                                        <InputError message={errors.contact_page_title} />
                                    </div>

                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-4">
                                            <Label htmlFor="location_title">{t('Location Title')}</Label>
                                            <Input
                                                id="location_title"
                                                value={data.location_title}
                                                onChange={(e) => setData('location_title', e.target.value)}
                                                placeholder={t('Enter location title')}
                                                required
                                            />
                                            <InputError message={errors.location_title} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label htmlFor="contact_title">{t('Contact Title')}</Label>
                                            <Input
                                                id="contact_title"
                                                value={data.contact_title}
                                                onChange={(e) => setData('contact_title', e.target.value)}
                                                placeholder={t('Enter contact title')}
                                                required
                                            />
                                            <InputError message={errors.contact_title} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label htmlFor="email_title">{t('Email Title')}</Label>
                                            <Input
                                                id="email_title"
                                                value={data.email_title}
                                                onChange={(e) => setData('email_title', e.target.value)}
                                                placeholder={t('Enter email title')}
                                                required
                                            />
                                            <InputError message={errors.email_title} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-4">
                                            <Label htmlFor="visit_address">{t('Visit Address')}</Label>
                                            <Textarea
                                                id="visit_address"
                                                value={data.visit_address}
                                                onChange={(e) => setData('visit_address', e.target.value)}
                                                placeholder={t('Enter visit address')}
                                                rows={3}
                                                required
                                            />
                                            <InputError message={errors.visit_address} />
                                        </div>
                                        <div className="col-span-4">
                                            <PhoneInputComponent
                                                id="call_details"
                                                label={t('Call Details')}
                                                value={data.call_details}
                                                onChange={(value) => setData('call_details', value)}
                                                placeholder={t('Enter call details')}
                                                required
                                                error={errors.call_details}
                                            />
                                        </div>
                                        <div className="col-span-4">
                                            <Label htmlFor="support_email">{t('Support Email')}</Label>
                                            <Input
                                                id="support_email"
                                                type="email"
                                                value={data.support_email}
                                                onChange={(e) => setData('support_email', e.target.value)}
                                                placeholder={t('Enter support email')}
                                                required
                                            />
                                            <InputError message={errors.support_email} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-4">
                                            <Label required>{t('Choose Icon For Location Text')}</Label>
                                            <IconPicker
                                                value={data.location_icon}
                                                onChange={(value) => setData('location_icon', value)}
                                                placeholder={t('Select location icon')}
                                            />
                                            <InputError message={errors.location_icon} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label required>{t('Choose Icon For Contact Text')}</Label>
                                            <IconPicker
                                                value={data.contact_icon}
                                                onChange={(value) => setData('contact_icon', value)}
                                                placeholder={t('Select contact icon')}
                                            />
                                            <InputError message={errors.contact_icon} />
                                        </div>
                                        <div className="col-span-4">
                                            <Label required>{t('Choose Icon For Email Text')}</Label>
                                            <IconPicker
                                                value={data.email_icon}
                                                onChange={(value) => setData('email_icon', value)}
                                                placeholder={t('Select email icon')}
                                            />
                                            <InputError message={errors.email_icon} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label htmlFor="google_map_iframe">{t('Google Map Iframe')}</Label>
                                        <Textarea
                                            id="google_map_iframe"
                                            value={data.google_map_iframe}
                                            onChange={(e) => setData('google_map_iframe', e.target.value)}
                                            placeholder={t('Enter Google Map iframe code')}
                                            rows={4}
                                            required
                                        />
                                        <InputError message={errors.google_map_iframe} />
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
