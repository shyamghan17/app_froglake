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
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface AwardSectionFormData {
    award_page_title: string;
    label: string;
    title: string;
    awards: any[];
}

export default function AwardSection() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'award_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const { data, setData, post, processing, errors } = useForm<AwardSectionFormData>({
        award_page_title: parsedData.award_page_title || '',
        label: parsedData.label || '',
        title: parsedData.title || '',
        awards: parsedData.awards?.length > 0
            ? parsedData.awards.map((award: any, index: number) => ({
                id: `award-${Date.now()}-${index}`,
                ...award,
            }))
            : [{ id: `award-${Date.now()}-0`, award_title: '', award_name: '', award_icon: '', description: '', achievement_name: '', achievement_icon: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.award-section.store'), {
            data: {
                award_page_title: data.award_page_title,
                label: data.label,
                title: data.title,
                awards: data.awards.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Award Section') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Award Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="award-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Award Section')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-3 gap-4">
                                        <div>
                                            <Label htmlFor="award_page_title">{t('Award Page Title')}</Label>
                                            <Input
                                                id="award_page_title"
                                                value={data.award_page_title}
                                                onChange={(e) => setData('award_page_title', e.target.value)}
                                                placeholder={t('Enter award page title')}
                                                required
                                            />
                                            <InputError message={errors.award_page_title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="label">{t('Label')}</Label>
                                            <Input
                                                id="label"
                                                value={data.label}
                                                onChange={(e) => setData('label', e.target.value)}
                                                placeholder={t('Enter label')}
                                                required
                                            />
                                            <InputError message={errors.label} />
                                        </div>
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
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Awards')}</Label>
                                        <Repeater
                                            fields={[
                                                { name: 'award_title', label: t('Award Title'), type: 'text', placeholder: t('Enter award title'), required: true, layout: { colSpan: 6 } },
                                                { name: 'award_name', label: t('Award Name'), type: 'text', placeholder: t('Enter award name'), required: true, layout: { colSpan: 6 } },
                                                { name: 'award_icon', label: t('Award Icon'), type: 'icon', placeholder: t('Select award icon'), required: true, layout: { colSpan: 6 } },
                                                { name: 'description', label: t('Description'), type: 'textarea', placeholder: t('Enter description'), required: true, layout: { colSpan: 6 } },
                                                { name: 'achievement_name', label: t('Achievement Name'), type: 'text', placeholder: t('Enter achievement name'), required: true, layout: { colSpan: 6 } },
                                                { name: 'achievement_icon', label: t('Achievement Icon'), type: 'icon', placeholder: t('Select achievement icon'), required: true, layout: { colSpan: 6 } },
                                            ]}
                                            layout={{ type: 'grid', columns: 12 }}
                                            value={data.awards}
                                            onChange={(items) => setData('awards', items)}
                                            addButtonText={t('Add Award')}
                                            deleteTooltipText={t('Remove Award')}
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
