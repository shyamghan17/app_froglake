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
import MediaPicker from '@/components/MediaPicker';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import { Textarea } from '@/components/ui/textarea';
import { Repeater } from '@/components/ui/repeater';
import { getImagePath } from '@/utils/helpers';
import SystemSetupSidebar from './SystemSetupSidebar';

interface AboutSectionFormData {
    title: string;
    sub_title: string;
    content: string;
    description: string;
    about_us_image: string;
    tips: any[];
}

export default function AboutSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'about_section')?.value;
    const parsedData = existingData
        ? JSON.parse(existingData)
        : { title: '', sub_title: '', content: '', description: '', about_us_image: '', tips: [] };

    const { data, setData, post, processing, errors } = useForm<AboutSectionFormData>({
        title: parsedData.title || '',
        sub_title: parsedData.sub_title || '',
        content: parsedData.content || '',
        description: parsedData.description || '',
        about_us_image: parsedData.about_us_image || '',
        tips: parsedData.tips?.length > 0
            ? parsedData.tips.map((tip: any, i: number) => ({ id: `tip-${Date.now()}-${i}`, ...tip }))
            : [{ id: `tip-${Date.now()}-0`, description: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.about-section.store'), {
            data: {
                title: data.title,
                sub_title: data.sub_title,
                content: data.content,
                description: data.description,
                about_us_image: data.about_us_image,
                tips: data.tips.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('About Section') },
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
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('About Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="title">{t('Title')}</Label>
                                            <Input
                                                id="title"
                                                value={data.title}
                                                onChange={(e) => setData('title', e.target.value)}
                                                placeholder={t('Enter title')}
                                                required
                                            />
                                            <InputError message={errors.title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="sub_title">{t('Sub Title')}</Label>
                                            <Input
                                                id="sub_title"
                                                value={data.sub_title}
                                                onChange={(e) => setData('sub_title', e.target.value)}
                                                placeholder={t('Enter sub title')}
                                                required
                                            />
                                            <InputError message={errors.sub_title} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label htmlFor="content" required>{t('Content')}</Label>
                                        <RichTextEditor
                                            content={data.content}
                                            onChange={(val) => setData('content', val)}
                                            placeholder={t('Enter content')}
                                            className="[&_.ProseMirror]:min-h-[150px]"
                                            required
                                        />
                                        <InputError message={errors.content} />
                                    </div>

                                    <div className="grid grid-cols-2 gap-6">
                                        <div>
                                            <Label htmlFor="description">{t('Description')}</Label>
                                            <Textarea
                                                id="description"
                                                value={data.description}
                                                onChange={(e) => setData('description', e.target.value)}
                                                placeholder={t('Enter description')}
                                                rows={4}
                                                required
                                            />
                                            <InputError message={errors.description} />
                                        </div>
                                        <div>
                                            <Label htmlFor="about_us_image" required>{t('Image')}</Label>
                                            <MediaPicker
                                                value={data.about_us_image}
                                                onChange={(url) => {
                                                    const urlString = Array.isArray(url) ? url[0] || '' : url;
                                                    const baseName = urlString ? urlString.split('/').pop() || urlString : '';
                                                    setData('about_us_image', baseName);
                                                }}
                                                placeholder={t('Select image')}
                                                showPreview={false}
                                            />
                                            <InputError message={errors.about_us_image} />
                                            <div className="flex items-center justify-center h-24 w-full mt-2">
                                                <img
                                                    src={data.about_us_image
                                                        ? getImagePath(data.about_us_image)
                                                        : getImagePath('packages/workdo/PhotoStudioManagement/src/Resources/assets/images/about-image.png')}
                                                    alt={t('About Image')}
                                                    className="max-h-full max-w-full object-contain"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Tips')}</Label>
                                        <Repeater
                                            fields={[
                                                {
                                                    name: 'description',
                                                    label: t('Description'),
                                                    type: 'text',
                                                    placeholder: t('Enter tip description'),
                                                    required: true,
                                                },
                                            ]}
                                            value={data.tips}
                                            onChange={(items) => setData('tips', items)}
                                            addButtonText={t('Add Tip')}
                                            deleteTooltipText={t('Remove Tip')}
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
