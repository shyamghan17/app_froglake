import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Save } from "lucide-react";
import MediaPicker from '@/components/MediaPicker';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';
import { getImagePath } from '@/utils/helpers';

interface AboutUsStats {
    title: string;
    description: string;
    icon: string;
}

interface AboutSectionFormData {
    about_image: string;
    main_title: string;
    content: string;
    sub_text: string;
    purpose_title: string;
    purpose_description: string;
    about_stats: AboutUsStats[];
}

export default function AboutSection() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const defaultAboutImage = getImagePath('/packages/workdo/BeautySpaManagement/src/Resources/assets/images/about-img.jpg');

    const existingData = beautysetups?.find((setup: any) => setup.key === 'about_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {
        about_image: '',
        main_title: '',
        content: '',
        sub_text: '',
        about_stats: []
    };

    const { data, setData, post, processing, errors } = useForm<AboutSectionFormData>({
        about_image: parsedData.about_image || '',
        main_title: parsedData.main_title || '',
        content: parsedData.content || '',
        sub_text: parsedData.sub_text || '',
        purpose_title: parsedData.purpose_title || '',
        purpose_description: parsedData.purpose_description || '',
        about_stats: parsedData.about_stats && parsedData.about_stats.length > 0
            ? parsedData.about_stats
            : [{ title: '', description: '', icon: '' }]
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.about-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('About Section') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('About Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="about-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('About Section')}</h3>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-8">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="main_title">{t('Main Title')}</Label>
                                        <Input
                                            id="main_title"
                                            value={data.main_title}
                                            onChange={(e) => setData('main_title', e.target.value)}
                                            placeholder={t('Enter main title')}
                                            required
                                        />
                                        <InputError message={errors.main_title} />
                                    </div>
                                    <div>
                                        <Label htmlFor="sub_text">{t('Sub Text')}</Label>
                                        <Input
                                            id="sub_text"
                                            value={data.sub_text}
                                            onChange={(e) => setData('sub_text', e.target.value)}
                                            placeholder={t('Enter sub text')}
                                            required
                                        />
                                        <InputError message={errors.sub_text} />
                                    </div>
                                </div>
                                <div>
                                    <Label htmlFor="content">{t('Content')}</Label>
                                    <RichTextEditor
                                        className="[&_.ProseMirror]:min-h-[200px]"
                                        content={data.content}
                                        onChange={(value) => setData('content', value)}
                                        placeholder={t('Enter about content')}
                                    />
                                    <InputError message={errors.content} />
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-12 gap-4">
                                        <div className="col-span-10">
                                            <Label>{t('About Image')}</Label>
                                            <MediaPicker
                                                value={data.about_image || 'about-img.jpg'}
                                                onChange={(value) => setData('about_image', value)}
                                                placeholder={t('Select about image')}
                                                showPreview={false}
                                            />
                                            <InputError message={errors.about_image} />
                                        </div>
                                        <div className="col-span-2">
                                            <div className="flex items-center justify-center h-24 w-full">
                                                <img
                                                    src={data.about_image && !data.about_image.startsWith('about-img') ? getImagePath(data.about_image) : defaultAboutImage}
                                                    alt={t('About Image')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="purpose_title">{t('Purpose Title')}</Label>
                                        <Input
                                            id="purpose_title"
                                            value={data.purpose_title}
                                            onChange={(e) => setData('purpose_title', e.target.value)}
                                            placeholder={t('Enter purpose title')} required
                                        />
                                        <InputError message={errors.purpose_title} />
                                    </div>
                                    <div>
                                        <Label htmlFor="purpose_description">{t('Purpose Description')}</Label>
                                        <Input
                                            id="purpose_description"
                                            value={data.purpose_description}
                                            onChange={(e) => setData('purpose_description', e.target.value)}
                                            placeholder={t('Enter purpose description')} required
                                        />
                                        <InputError message={errors.purpose_description} />
                                    </div>
                                </div>
                                <div>
                                    <Label className="text-lg font-medium mb-4 block">{t('Purpose Details')}</Label>
                                    <Repeater
                                        fields={[
                                            {
                                                name: 'title',
                                                label: t('Purpose Title'),
                                                type: 'text',
                                                placeholder: t('Enter Purpose Title'),
                                            },
                                            {
                                                name: 'icon',
                                                label: t('Purpose Icon'),
                                                type: 'icon',
                                                placeholder: t('Select an icon'),
                                            },
                                            {
                                                name: 'description',
                                                label: t('Purpose Description'),
                                                type: 'textarea',
                                                placeholder: t('Enter Purpose Description'),
                                            }
                                        ]}
                                        value={data.about_stats.map((stat, index) => ({
                                            id: `stat-${index}`,
                                            title: stat.title || '',
                                            icon: stat.icon || '',
                                            description: stat.description || ''
                                        }))}
                                        onChange={(items) => {
                                            const stats = items.map(({ id, ...item }) => item);
                                            setData('about_stats', stats);
                                        }}
                                        addButtonText={t('Add Purpose')}
                                        deleteTooltipText={t('Remove Purpose')}
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