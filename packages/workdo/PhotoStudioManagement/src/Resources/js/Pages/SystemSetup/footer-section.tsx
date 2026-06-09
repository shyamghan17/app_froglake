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
import { IconPicker } from '@/components/ui/icon-picker';
import { Repeater } from '@/components/ui/repeater';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import SystemSetupSidebar from './SystemSetupSidebar';

interface FooterSectionFormData {
    location: string;
    phone_no: string;
    email: string;
    location_icon: string;
    phone_icon: string;
    email_icon: string;
    newsletter_label: string;
    newsletter_title: string;
    social_links: any[];
}

export default function FooterSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'footer_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<FooterSectionFormData>({
        location:         parsedData.location || '',
        phone_no:         parsedData.phone_no || '',
        email:            parsedData.email || '',
        location_icon:    parsedData.location_icon || '',
        phone_icon:       parsedData.phone_icon || '',
        email_icon:       parsedData.email_icon || '',
        newsletter_label: parsedData.newsletter_label || '',
        newsletter_title: parsedData.newsletter_title || '',
        social_links: parsedData.social_links?.length > 0
            ? parsedData.social_links.map((link: any, index: number) => ({
                id: `social-${Date.now()}-${index}`,
                ...link,
            }))
            : [{ id: `social-${Date.now()}-0`, social_link: '', social_icon: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.footer-section.store'), {
            data: {
                ...data,
                social_links: data.social_links.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Footer Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Footer Section')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="footer-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Footer Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    {/* Information Section */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-lg font-medium mb-4 block">{t('Information Section')}</Label>

                                        <div className="grid grid-cols-12 gap-4 mb-4">
                                            <div className="col-span-4">
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
                                            <div className="col-span-4">
                                                <PhoneInputComponent
                                                    id="phone_no"
                                                    label={t('Phone No.')}
                                                    value={data.phone_no}
                                                    onChange={(value) => setData('phone_no', value)}
                                                    placeholder={t('Enter phone number')}
                                                    required
                                                    error={errors.phone_no}
                                                />
                                            </div>
                                            <div className="col-span-4">
                                                <Label htmlFor="email">{t('Email')}</Label>
                                                <Input
                                                    id="email"
                                                    type="email"
                                                    value={data.email}
                                                    onChange={(e) => setData('email', e.target.value)}
                                                    placeholder={t('Enter email')}
                                                    required
                                                />
                                                <InputError message={errors.email} />
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-4">
                                                <Label required>{t('Choose Icon For Location')}</Label>
                                                <IconPicker
                                                    value={data.location_icon}
                                                    onChange={(value) => setData('location_icon', value)}
                                                    placeholder={t('Select location icon')} 
                                                />
                                                <InputError message={errors.location_icon} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label required>{t('Choose Icon For Phone No.')}</Label>
                                                <IconPicker
                                                    value={data.phone_icon}
                                                    onChange={(value) => setData('phone_icon', value)}
                                                    placeholder={t('Select phone icon')}
                                                />
                                                <InputError message={errors.phone_icon} />
                                            </div>
                                            <div className="col-span-4">
                                                <Label required>{t('Choose Icon For Email')}</Label>
                                                <IconPicker
                                                    value={data.email_icon}
                                                    onChange={(value) => setData('email_icon', value)}
                                                    placeholder={t('Select email icon')}
                                                />
                                                <InputError message={errors.email_icon} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Newsletter Section */}
                                    <div className="pb-6 border-b border-gray-200">
                                        <Label className="text-lg font-medium mb-4 block">{t('Newsletter Section')}</Label>
                                        <div className="grid grid-cols-12 gap-4">
                                            <div className="col-span-6">
                                                <Label htmlFor="newsletter_label">{t('Newsletter Label')}</Label>
                                                <Input
                                                    id="newsletter_label"
                                                    value={data.newsletter_label}
                                                    onChange={(e) => setData('newsletter_label', e.target.value)}
                                                    placeholder={t('Enter newsletter label')}
                                                    required
                                                />
                                                <InputError message={errors.newsletter_label} />
                                            </div>
                                            <div className="col-span-6">
                                                <Label htmlFor="newsletter_title">{t('Newsletter Title')}</Label>
                                                <Input
                                                    id="newsletter_title"
                                                    value={data.newsletter_title}
                                                    onChange={(e) => setData('newsletter_title', e.target.value)}
                                                    placeholder={t('Enter newsletter title')}
                                                    required
                                                />
                                                <InputError message={errors.newsletter_title} />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Social Link Section */}
                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Social Link Section')}</Label>
                                        <Repeater
                                            fields={[
                                                {
                                                    name: 'social_link',
                                                    label: t('Social Link'),
                                                    type: 'url',
                                                    placeholder: 'https://example.com/yourpage',
                                                    required: true,
                                                },
                                                {
                                                    name: 'social_icon',
                                                    label: t('Social Icon'),
                                                    type: 'icon',
                                                    placeholder: t('Select an icon'),
                                                    required: true,
                                                },
                                            ]}
                                            value={data.social_links}
                                            onChange={(items) => setData('social_links', items)}
                                            addButtonText={t('Add Social Link')}
                                            deleteTooltipText={t('Remove Social Link')}
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
