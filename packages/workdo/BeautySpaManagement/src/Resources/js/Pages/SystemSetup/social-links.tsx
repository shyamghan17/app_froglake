import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Save } from "lucide-react";
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface SocialLink {
    url: string;
    icon: string;
}

interface SocialLinksFormData {
    social_links: SocialLink[];
}

export default function SocialLinks() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'social_links')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {
        social_links: []
    };

    const processedSocialLinks = parsedData.social_links && parsedData.social_links.length > 0 
        ? parsedData.social_links 
        : [{ url: '', icon: '' }];

    const { data, setData, post, processing, errors } = useForm<SocialLinksFormData>({
        social_links: processedSocialLinks
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.social-links.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Social Links') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Social Links')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="social-links" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <Label className="text-lg font-medium mb-4 block">{t('Social Links')}</Label>
                                    <Repeater
                                        fields={[
                                            {
                                                name: 'url',
                                                label: t('Social Link'),
                                                type: 'text',
                                                placeholder: 'https://example.com/yourpage',
                                            },
                                            {
                                                name: 'icon',
                                                label: t('Social Icon'),
                                                type: 'icon',
                                                placeholder: t('Select an icon'),
                                            }
                                        ]}
                                        value={data.social_links.map((link, index) => ({
                                            id: `link-${index}`,
                                            url: link.url || '',
                                            icon: link.icon || ''
                                        }))}
                                        onChange={(items) => {
                                            const links = items.map(({ id, ...item }) => item);
                                            setData('social_links', links);
                                        }}
                                        addButtonText={t('Add Social Link')}
                                        deleteTooltipText={t('Remove Social Link')}
                                        minItems={1}
                                        errors={errors as any}
                                    />
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