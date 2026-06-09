import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { getImagePath } from '@/utils/helpers';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/ui/input-error';
import { Save } from "lucide-react";
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface TestimonialItem {
    customer_name: string;
    rating: number;
    comment: string;
}

interface TestimonialFormData {
    title: string;
    description: string;
    testimonials: TestimonialItem[];
}

interface TestimonialIndexProps {
    testimonials: TestimonialItem[];
}

export default function Testimonials() {
    const { t } = useTranslation();
    const pageData = usePage<TestimonialIndexProps>();
    const { beautysetups } = pageData.props as any;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'testimonials')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : {};

    const processedTestimonials = parsedData.testimonials || [{ customer_name: '', rating: 5, comment: '' }];

    const { data, setData, post, processing, errors } = useForm<TestimonialFormData>({
        title: parsedData.title || '',
        description: parsedData.description || '',
        testimonials: processedTestimonials
    });

    useFlashMessages();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.testimonials.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Testimonials') }
            ]}
            pageTitle={t('System Setup')}
        >
                <Head title={t('Testimonials')} />

                <div className="flex flex-col md:flex-row gap-8">
                    <div className="md:w-64 flex-shrink-0">
                        <SystemSetupSidebar activeItem="testimonials" />
                    </div>

                    <div className="flex-1">
                        <Card>
                            <CardContent className="p-6">
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Testimonials Section')}</h3>
                                </div>
                                <form onSubmit={submit} className="space-y-8">
                                    <div className="space-y-6">
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label htmlFor="title">{t('Title')}</Label>
                                                <Input
                                                    id="title"
                                                    value={data.title}
                                                    onChange={(e) => setData('title', e.target.value)}
                                                    placeholder={t('Enter testimonial title')}
                                                    required
                                                />
                                                <InputError message={errors.title} />
                                            </div>
                                            <div>
                                                <Label htmlFor="description">{t('Description')}</Label>
                                                <Input
                                                    id="description"
                                                    value={data.description}
                                                    onChange={(e) => setData('description', e.target.value)}
                                                    placeholder={t('Enter testimonial description')}
                                                    required
                                                />
                                                <InputError message={errors.description} />
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <Label className="text-lg font-medium mb-4 block">{t('Testimonials Details')}</Label>
                                        <Repeater
                                            fields={[
                                                {
                                                    name: 'customer_name',
                                                    label: t('Customer Name'),
                                                    type: 'text',
                                                    placeholder: t('Enter Customer Name'),
                                                },
                                                {
                                                    name: 'rating',
                                                    label: t('Rating'),
                                                    type: 'select',
                                                    placeholder: t('Select Rating'),
                                                    options: [
                                                        { value: '1', label: '1 Star' },
                                                        { value: '2', label: '2 Stars' },
                                                        { value: '3', label: '3 Stars' },
                                                        { value: '4', label: '4 Stars' },
                                                        { value: '5', label: '5 Stars' }
                                                    ],
                                                },
                                                {
                                                    name: 'comment',
                                                    label: t('Comment'),
                                                    type: 'textarea',
                                                    placeholder: t('Enter testimonial comment'),
                                                }
                                            ]}
                                            value={data.testimonials.map((testimonial, index) => ({
                                                id: `testimonial-${index}`,
                                                customer_name: testimonial.customer_name || '',
                                                rating: testimonial.rating?.toString() || '5',
                                                comment: testimonial.comment || ''
                                            }))}
                                            onChange={(items) => {
                                                const testimonials = items.map(({ id, ...item }) => ({
                                                    ...item,
                                                    rating: parseInt(item.rating) || 5
                                                }));
                                                setData('testimonials', testimonials);
                                            }}
                                            addButtonText={t('Add Testimonial')}
                                            deleteTooltipText={t('Remove Testimonial')}
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