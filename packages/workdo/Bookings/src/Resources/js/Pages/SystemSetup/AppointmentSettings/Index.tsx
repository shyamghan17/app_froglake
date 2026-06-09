import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from "../SystemSetupSidebar";
import { Save } from 'lucide-react';

interface AppointmentSettings {
    title: string;
    description: string;
    why_book_with_us: string[];
}

interface AppointmentSettingsProps {
    settings: AppointmentSettings;
}

export default function Index({ settings }: AppointmentSettingsProps) {
    const { t } = useTranslation();
    const [isSubmitting, setIsSubmitting] = useState(false);

    const { data, setData, post, errors, processing } = useForm({
        title: settings.title || '',
        description: settings.description || '',
        why_book_with_us: settings.why_book_with_us && settings.why_book_with_us.length > 0
            ? settings.why_book_with_us
            : ['']
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        post(route('bookings.appointment-settings.update'), {
            onFinish: () => setIsSubmitting(false)
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                {label: t('Appointment Setting')}
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Appointment Setting')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="appointment-settings" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Appointment Setting')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your appointment section settings')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="title" required>{t('Title')}</Label>
                                        <Input
                                            id="title"
                                            type="text"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder={t('Enter Title')}
                                            required
                                        />
                                        <InputError message={errors.title} />
                                    </div>

                                    <div>
                                        <Label htmlFor="description" required>{t('Description')}</Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder={t('Enter Description')}
                                            rows={3}
                                            required
                                        />
                                        <InputError message={errors.description} />
                                    </div>
                                </div>

                                <div>
                                    <Label className="mb-3" required>{t('Why Book With Us?')}</Label>
                                    <div className="border rounded-lg p-4 bg-gray-50/50">
                                        <Repeater
                                            fields={[
                                                { 
                                                    name: 'text', 
                                                    label: t('Feature'), 
                                                    type: 'text', 
                                                    placeholder: t('Enter feature'), 
                                                    required: true,
                                                    className: 'col-span-full'
                                                }
                                            ]}
                                            value={data.why_book_with_us.map((text, index) => ({
                                                id: `feature-${index}`,
                                                text: text || ''
                                            }))}
                                            onChange={(items) => {
                                                const features = items.map(({ text }) => text || '');
                                                setData('why_book_with_us', features);
                                            }}
                                            addButtonText={t('Add Feature')}
                                            deleteTooltipText={t('Delete')}
                                            showDefault={true}
                                            minItems={1}
                                            containerClassName="space-y-3"
                                            itemClassName="border border-gray-200 rounded-lg p-3 bg-white"
                                        />
                                    </div>
                                    <InputError message={errors.why_book_with_us} />
                                </div>

                                <div className="flex justify-end">
                                    <Button type="submit" disabled={processing || isSubmitting}>
                                        <Save className="h-4 w-4 mr-2" />
                                        {processing || isSubmitting ? t('Saving...') : t('Save Changes')}
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
