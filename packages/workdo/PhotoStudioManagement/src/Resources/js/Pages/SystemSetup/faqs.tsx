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

interface FaqSectionFormData {
    faq_page_title: string;
    faq_label: string;
    faq_title: string;
    faqs: any[];
}

export default function Faqs() {
    const { t } = useTranslation();
    const { photostudiosetups } = usePage<any>().props;

    const existingData = photostudiosetups?.find((s: any) => s.key === 'faq_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : { faq_page_title: '', faq_label: '', faq_title: '', faqs: [] };

    const { data, setData, post, processing, errors } = useForm<FaqSectionFormData>({
        faq_page_title: parsedData.faq_page_title || '',
        faq_label:      parsedData.faq_label || '',
        faq_title:      parsedData.faq_title || '',
        faqs: parsedData.faqs?.length > 0
            ? parsedData.faqs.map((faq: any, index: number) => ({
                id: `faq-${Date.now()}-${index}`,
                ...faq,
            }))
            : [{ id: `faq-${Date.now()}-0`, question: '', answer: '' }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.faqs.store'), {
            data: {
                faq_page_title: data.faq_page_title,
                faq_label:      data.faq_label,
                faq_title:      data.faq_title,
                faqs:           data.faqs.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('FAQ') },
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('FAQ')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="faqs" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('FAQ')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="space-y-6">
                                    <div className="grid grid-cols-3 gap-4">
                                        <div>
                                            <Label htmlFor="faq_page_title">{t('FAQ Page Title')}</Label>
                                            <Input
                                                id="faq_page_title"
                                                value={data.faq_page_title}
                                                onChange={(e) => setData('faq_page_title', e.target.value)}
                                                placeholder={t('Enter FAQ page title')}
                                                required
                                            />
                                            <InputError message={errors.faq_page_title} />
                                        </div>
                                        <div>
                                            <Label htmlFor="faq_label">{t('FAQ Label')}</Label>
                                            <Input
                                                id="faq_label"
                                                value={data.faq_label}
                                                onChange={(e) => setData('faq_label', e.target.value)}
                                                placeholder={t('Enter FAQ label')}
                                                required
                                            />
                                            <InputError message={errors.faq_label} />
                                        </div>
                                        <div>
                                            <Label htmlFor="faq_title">{t('FAQ Title')}</Label>
                                            <Input
                                                id="faq_title"
                                                value={data.faq_title}
                                                onChange={(e) => setData('faq_title', e.target.value)}
                                                placeholder={t('Enter FAQ title')}
                                                required
                                            />
                                            <InputError message={errors.faq_title} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('FAQs')}</Label>
                                        <Repeater
                                            fields={[
                                                { name: 'question', label: t('Question'), type: 'text', placeholder: t('Enter question'), required: true, layout: { colSpan: 12 } },
                                                { name: 'answer', label: t('Answer'), type: 'textarea', placeholder: t('Enter answer'), required: true, layout: { colSpan: 12 } },
                                            ]}
                                            layout={{ type: 'grid', columns: 12 }}
                                            value={data.faqs}
                                            onChange={(items) => setData('faqs', items)}
                                            addButtonText={t('Add FAQ')}
                                            deleteTooltipText={t('Remove FAQ')}
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
