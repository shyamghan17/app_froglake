import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Save } from 'lucide-react';
import { Repeater } from '@/components/ui/repeater';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import MediaPicker from '@/components/MediaPicker';
import { getImagePath } from '@/utils/helpers';
import SystemSetupSidebar from './SystemSetupSidebar';

interface TestimonialFormData {
    client_feedback_label: string;
    client_feedback_title: string;
    testimonial_title: string;
    testimonial_image: string;
    testimonials: any[];
}

export default function Testimonials() {
    const { t } = useTranslation();
    const { testimonials: initialTestimonials, testimonial_title: initialTitle, testimonial_image: initialImage, client_feedback_label: initialLabel, client_feedback_title: initialFeedbackTitle } = usePage<any>().props;

    const { data, setData, post, processing, errors } = useForm<TestimonialFormData>({
        client_feedback_label: initialLabel || '',
        client_feedback_title: initialFeedbackTitle || '',
        testimonial_title: initialTitle || '',
        testimonial_image: initialImage || '',
        testimonials: (initialTestimonials || []).length > 0
            ? initialTestimonials.map((testimonial: any, index: number) => ({
                id: `testimonial-${Date.now()}-${index}`,
                ...testimonial,
            }))
            : [{ id: `testimonial-${Date.now()}-0`, customer_name: '', designation: '', rating: 5, comment: '', profile_image: null }],
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.testimonials.store'), {
            data: {
                client_feedback_label: data.client_feedback_label,
                client_feedback_title: data.client_feedback_title,
                testimonial_title: data.testimonial_title,
                testimonial_image: data.testimonial_image,
                testimonials: data.testimonials.map(({ id, ...item }) => item),
            },
        } as any);
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('System Setup'), url: route('photo-studio-management.brand-settings.index') },
                { label: t('Testimonials') },
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
                            <form onSubmit={handleSubmit}>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Testimonials')}</h3>
                                    <Button type="submit" disabled={processing} size="sm" className="flex items-center gap-2">
                                        <Save className="h-4 w-4" />
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>

                                <div className="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <Label htmlFor="client_feedback_label">{t('Client Feedback Label')}</Label>
                                        <Input
                                            id="client_feedback_label"
                                            value={data.client_feedback_label}
                                            onChange={(e) => setData('client_feedback_label', e.target.value)}
                                            placeholder={t('Enter client feedback label')}
                                            required
                                        />
                                        <InputError message={errors.client_feedback_label} />
                                    </div>
                                    <div>
                                        <Label htmlFor="client_feedback_title">{t('Client Feedback Title')}</Label>
                                        <Input
                                            id="client_feedback_title"
                                            value={data.client_feedback_title}
                                            onChange={(e) => setData('client_feedback_title', e.target.value)}
                                            placeholder={t('Enter client feedback title')}
                                            required
                                        />
                                        <InputError message={errors.client_feedback_title} />
                                    </div>
                                </div>

                                                <div className="grid grid-cols-12 gap-4 mb-6">
                                    <div className="col-span-6">
                                        <Label htmlFor="testimonial_title">{t('Testimonial Title')}</Label>
                                        <Input
                                            id="testimonial_title"
                                            value={data.testimonial_title}
                                            onChange={(e) => setData('testimonial_title', e.target.value)}
                                            placeholder={t('Enter testimonial title')}
                                            required
                                        />
                                        <InputError message={errors.testimonial_title} />
                                    </div>
                                    <div className="col-span-6">
                                        <Label htmlFor="testimonial_image">{t('Testimonial Image')} <span className="text-red-500">*</span></Label>
                                        <MediaPicker
                                            value={data.testimonial_image}
                                            onChange={(url) => {
                                                const urlString = Array.isArray(url) ? url[0] || '' : url;
                                                setData('testimonial_image', urlString ? urlString.split('/').pop() || urlString : '');
                                            }}
                                            placeholder={t('Select testimonial image')}
                                            showPreview={false}
                                            multiple={false}
                                        />
                                        <InputError message={errors.testimonial_image} />
                                        <div className="flex items-center justify-center h-24 w-full mt-2">
                                            <img
                                                src={data.testimonial_image
                                                    ? getImagePath(data.testimonial_image)
                                                    : getImagePath('packages/workdo/PhotoStudioManagement/src/Resources/assets/images/testimonial-bg.png')}
                                                alt={t('Testimonial Image')}
                                                className="max-h-full max-w-full object-contain"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <Repeater
                                    fields={[
                                        { name: 'customer_name', label: t('Customer Name'), type: 'text', placeholder: t('Enter Customer Name'), required: true },
                                        { name: 'designation', label: t('Designation'), type: 'text', placeholder: t('Enter Designation'), required: true },
                                        { name: 'profile_image', label: t('Profile Image'), type: 'media' },
                                        { name: 'rating', label: t('Rating'), type: 'rating', max: 5, required: true },
                                        { name: 'comment', label: t('Comment'), type: 'textarea', placeholder: t('Enter Comment'), required: true },
                                    ]}
                                    value={data.testimonials}
                                    onChange={(items) => setData('testimonials', items.map(item => ({
                                        ...item,
                                        profile_image: item.profile_image && typeof item.profile_image === 'string' && item.profile_image.includes('/')
                                            ? item.profile_image.split('/').pop() || item.profile_image
                                            : item.profile_image,
                                    })))}
                                    addButtonText={t('Add Testimonial')}
                                    deleteTooltipText={t('Remove Testimonial')}
                                    minItems={1}
                                    errors={errors as any}
                                />
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
