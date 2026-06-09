import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Plus, Trash2, Save } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { IconPicker } from '@/components/ui/icon-picker';
import SystemSetupSidebar from '../SystemSetupSidebar';

interface SocialLink {
    url: string;
    icon: string;
    name: string;
}

interface SocialLinksFormData {
    social_links: SocialLink[];
}

export default function SocialLinks() {
    const { t } = useTranslation();
    const { socialLinks: existingLinks } = usePage<any>().props;

    const processedSocialLinks = existingLinks && existingLinks.length > 0 
        ? existingLinks 
        : [{ url: '', icon: '', name: '' }];

    const [socialLinks, setSocialLinks] = useState<SocialLink[]>(processedSocialLinks);

    const { data, setData, post, processing, errors } = useForm<SocialLinksFormData>({
        social_links: socialLinks
    });

    useFlashMessages();

    const addSocialLink = () => {
        const newLinks = [...socialLinks, { url: '', icon: '', name: '' }];
        setSocialLinks(newLinks);
        setData('social_links', newLinks);
    };

    const removeSocialLink = (index: number) => {
        const newLinks = socialLinks.filter((_, i) => i !== index);
        setSocialLinks(newLinks);
        setData('social_links', newLinks);
    };

    const updateSocialLink = (index: number, field: keyof SocialLink, value: string) => {
        const newLinks = [...socialLinks];
        newLinks[index][field] = value;
        setSocialLinks(newLinks);
        setData('social_links', newLinks);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.social-links.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Bookings'), url: route('bookings.dashboard') },
                { label: t('System Setup'), url: route('bookings.brand-settings.index') },
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
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Social Links')}</h3>
                                <TooltipProvider>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button type="button" size="sm" onClick={addSocialLink}>
                                                <Plus className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Add Social Link')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-4">
                                    {socialLinks.map((link, index) => (
                                        <Card key={index} className="p-4 border-2 border-dashed">
                                            <div className="flex justify-between items-start mb-4">
                                                <h4 className="text-lg font-medium">{t('Social Link')} {index + 1}</h4>
                                                {socialLinks.length > 1 && (
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    type="button"
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    onClick={() => removeSocialLink(index)}
                                                                    className="text-red-600 hover:text-red-700"
                                                                >
                                                                    <Trash2 className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>{t('Remove')}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                )}
                                            </div>
                                            <div className="grid grid-cols-12 gap-4">
                                                <div className="col-span-4">
                                                    <Label required>{t('Name')}</Label>
                                                    <Input
                                                        type="text"
                                                        value={link.name}
                                                        onChange={(e) => updateSocialLink(index, 'name', e.target.value)}
                                                        placeholder={t('Enter name')}
                                                        required
                                                    />
                                                    <InputError message={errors[`social_links.${index}.name` as keyof typeof errors]} />
                                                </div>
                                                <div className="col-span-4">
                                                    <Label required>{t('Social Link')}</Label>
                                                    <Input
                                                        type="url"
                                                        value={link.url}
                                                        onChange={(e) => updateSocialLink(index, 'url', e.target.value)}
                                                        placeholder="https://example.com"
                                                        required
                                                    />
                                                    <InputError message={errors[`social_links.${index}.url` as keyof typeof errors]} />
                                                </div>
                                                <div className="col-span-4">
                                                    <Label required>{t('Social Icon')}</Label>
                                                    <IconPicker
                                                        value={link.icon}
                                                        onChange={(value) => updateSocialLink(index, 'icon', value)}
                                                        placeholder={t('Select an icon')}
                                                    />
                                                    <InputError message={errors[`social_links.${index}.icon` as keyof typeof errors]} />
                                                </div>
                                            </div>
                                        </Card>
                                    ))}
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
