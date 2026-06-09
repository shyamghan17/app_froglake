import { useState } from 'react';
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
import { IconPicker } from '@/components/ui/icon-picker';
import SystemSetupSidebar from './SystemSetupSidebar';

interface ContactInfoFormData {
    header_title: string;
    header_description: string;
    location: string;
    phone_number: string;
    email_address: string;
    location_icon: string;
    phone_icon: string;
    email_icon: string;
    map_title: string;
    map_subtext: string;
    map_iframe: string;
    follow_us_description: string;
    cta_title: string;
    cta_description: string;
}

export default function ContactInfo() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'contact_info')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {
        header_title: '',
        header_description: '',
        location: '',
        phone_number: '',
        email_address: '',
        location_icon: '',
        phone_icon: '',
        email_icon: '',
        map_title: '',
        map_subtext: '',
        map_iframe: '',
        follow_us_description: ''
    };

    const { data, setData, post, processing, errors } = useForm<ContactInfoFormData>({
        header_title: parsedData.header_title || '',
        header_description: parsedData.header_description || '',
        location: parsedData.location || '',
        phone_number: parsedData.phone_number || '',
        email_address: parsedData.email_address || '',
        location_icon: parsedData.location_icon || '',
        phone_icon: parsedData.phone_icon || '',
        email_icon: parsedData.email_icon || '',
        map_title: parsedData.map_title || '',
        map_subtext: parsedData.map_subtext || '',
        map_iframe: parsedData.map_iframe || '',
        follow_us_description: parsedData.follow_us_description || '',
        cta_title: parsedData.cta_title || '',
        cta_description: parsedData.cta_description || ''
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.contact-info.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Contact Info') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Contact Info')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="contact-info" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Contact Info')}</h3>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-8">
                                <Card className="p-6">
                                    <div className="space-y-4">
                                        <h3 className="text-lg font-medium">{t('Header Title & Description')}</h3>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="header_title">{t('Heading Title')}</Label>
                                                <Input
                                                    id="header_title"
                                                    value={data.header_title}
                                                    onChange={(e) => setData('header_title', e.target.value)}
                                                    placeholder={t('Enter heading title')}
                                                    required
                                                />
                                                <InputError message={errors.header_title} />
                                            </div>
                                            <div>
                                                <Label htmlFor="header_description">{t('Sub Text')}</Label>
                                                <Input
                                                    id="header_description"
                                                    value={data.header_description}
                                                    onChange={(e) => setData('header_description', e.target.value)}
                                                    placeholder={t('Enter sub text')}
                                                    required
                                                />
                                                <InputError message={errors.header_description} />
                                            </div>
                                        </div>
                                    </div>
                                </Card>

                                <Card className="p-6">
                                    <div className="space-y-4">
                                        <h3 className="text-lg font-medium">{t('Contact Information')}</h3>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="location">{t('Location')}</Label>
                                                <Input
                                                    id="location"
                                                    value={data.location}
                                                    onChange={(e) => setData('location', e.target.value)}
                                                    placeholder={t('Enter location')}
                                                    required
                                                />
                                                <InputError message={errors.location} />
                                            </div>
                                            <div>
                                                <Label>{t('Location Icon')}</Label>
                                                <IconPicker
                                                    value={data.location_icon}
                                                    onChange={(value) => setData('location_icon', value)}
                                                    placeholder={t('Select location icon')}
                                                />
                                                <InputError message={errors.location_icon} />
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="phone_number">{t('Phone Number')}</Label>
                                                <Input
                                                    id="phone_number"
                                                    value={data.phone_number}
                                                    onChange={(e) => setData('phone_number', e.target.value)}
                                                    placeholder={t('Enter phone number')}
                                                    required
                                                />
                                                <InputError message={errors.phone_number} />
                                            </div>
                                            <div>
                                                <Label>{t('Phone Icon')}</Label>
                                                <IconPicker
                                                    value={data.phone_icon}
                                                    onChange={(value) => setData('phone_icon', value)}
                                                    placeholder={t('Select phone icon')}
                                                />
                                                <InputError message={errors.phone_icon} />
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="email_address">{t('Email Address')}</Label>
                                                <Input
                                                    id="email_address"
                                                    type="email"
                                                    value={data.email_address}
                                                    onChange={(e) => setData('email_address', e.target.value)}
                                                    placeholder={t('Enter email address')}
                                                    required
                                                />
                                                <InputError message={errors.email_address} />
                                            </div>
                                            <div>
                                                <Label>{t('Email Icon')}</Label>
                                                <IconPicker
                                                    value={data.email_icon}
                                                    onChange={(value) => setData('email_icon', value)}
                                                    placeholder={t('Select email icon')}
                                                />
                                                <InputError message={errors.email_icon} />
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="map_title">{t('Google Map Title')}</Label>
                                                <Input
                                                    id="map_title"
                                                    value={data.map_title}
                                                    onChange={(e) => setData('map_title', e.target.value)}
                                                    placeholder={t('Enter map title')}
                                                    required
                                                />
                                                <InputError message={errors.map_title} />
                                            </div>
                                            <div>
                                                <Label htmlFor="map_subtext">{t('Google Map Subtext')}</Label>
                                                <Input
                                                    id="map_subtext"
                                                    value={data.map_subtext}
                                                    onChange={(e) => setData('map_subtext', e.target.value)}
                                                    placeholder={t('Enter map subtext')}
                                                    required
                                                />
                                                <InputError message={errors.map_subtext} />
                                            </div>
                                        </div>

                                        <div>
                                            <Label htmlFor="map_iframe">{t('Google Map Iframe')}</Label>
                                            <Textarea
                                                id="map_iframe"
                                                value={data.map_iframe}
                                                onChange={(e) => setData('map_iframe', e.target.value)}
                                                placeholder={t('Enter Google Map iframe code')}
                                                rows={4}
                                                required
                                            />
                                            <p className="text-xs text-gray-500 mt-1">{t('You can get iframe from Google Maps → Share → Embed a map')}</p>
                                            <InputError message={errors.map_iframe} />
                                        </div>
                                    </div>
                                </Card>

                                <Card className="p-6">
                                    <div className="space-y-4">
                                        <h3 className="text-lg font-medium">{t('Call to Action')}</h3>

                                        <div>
                                            <Label htmlFor="cta_title">{t('CTA Title')}</Label>
                                            <Input
                                                id="cta_title"
                                                value={data.cta_title}
                                                onChange={(e) => setData('cta_title', e.target.value)}
                                                placeholder={t('Enter CTA title')} required
                                            />
                                            <InputError message={errors.cta_title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="cta_description">{t('CTA Description')}</Label>
                                            <Textarea
                                                id="cta_description"
                                                value={data.cta_description}
                                                onChange={(e) => setData('cta_description', e.target.value)}
                                                placeholder={t('Enter CTA description')}
                                                rows={3} required
                                            />
                                            <InputError message={errors.cta_description} />
                                        </div>
                                    </div>
                                </Card>
                                <Card className="p-6">
                                    <div className="space-y-4">
                                        <h3 className="text-lg font-medium">{t('Follow Us')}</h3>

                                        <div>
                                            <Label htmlFor="follow_us_description">{t('Description')}</Label>
                                            <Textarea
                                                id="follow_us_description"
                                                value={data.follow_us_description}
                                                onChange={(e) => setData('follow_us_description', e.target.value)}
                                                placeholder={t('Enter follow us description')}
                                                rows={3}
                                                required
                                            />
                                            <InputError message={errors.follow_us_description} />
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