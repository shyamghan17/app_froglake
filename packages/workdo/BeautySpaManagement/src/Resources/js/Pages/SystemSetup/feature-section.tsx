import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Save } from "lucide-react";
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from './SystemSetupSidebar';

interface Feature {
    title: string;
    icon: string;
    description: string;
}

interface FeatureSectionFormData {
    why_choose_us_title: string;
    why_choose_us_description: string;
    features: Feature[];
}

export default function FeatureSection() {
    const { t } = useTranslation();
    const { beautysetups } = usePage<any>().props;

    const existingData = beautysetups?.find((setup: any) => setup.key === 'feature_section')?.value;
    const parsedData = existingData ? JSON.parse(existingData) : { features: [] };

    const processedFeatures = parsedData.features && parsedData.features.length > 0 
        ? parsedData.features
        : [{ title: '', icon: '', description: '' }];

    const { data, setData, post, processing, errors } = useForm<FeatureSectionFormData>({
        why_choose_us_title: parsedData.why_choose_us_title || '',
        why_choose_us_description: parsedData.why_choose_us_description || '',
        features: processedFeatures
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.feature-section.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Feature Section') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Feature Section')} />

            <div className="flex gap-6">
                <div className="w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="feature-section" />
                </div>

                <div className="flex-1">
                    <Card>
                        <CardContent className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">{t('Feature Section')}</h3>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="why_choose_us_title">{t('Why Choose Us Title')}</Label>
                                        <Input
                                            id="why_choose_us_title"
                                            value={data.why_choose_us_title}
                                            onChange={(e) => setData('why_choose_us_title', e.target.value)}
                                            placeholder={t('Enter why choose us title')}
                                        />
                                        <InputError message={errors.why_choose_us_title} />
                                    </div>
                                    <div>
                                        <Label htmlFor="why_choose_us_description">{t('Why Choose Us Description')}</Label>
                                        <Input
                                            id="why_choose_us_description"
                                            value={data.why_choose_us_description}
                                            onChange={(e) => setData('why_choose_us_description', e.target.value)}
                                            placeholder={t('Enter why choose us description')}
                                        />
                                        <InputError message={errors.why_choose_us_description} />
                                    </div>
                                </div>

                                <div>
                                    <Label className="text-lg font-medium mb-4 block">{t('Features Details')}</Label>
                                    <Repeater
                                    
                                        fields={[
                                            {
                                                name: 'title',
                                                label: t('Feature Title'),
                                                type: 'text',
                                                placeholder: t('Enter feature title'),
                                                
                                            },
                                            {
                                                name: 'icon',
                                                label: t('Feature Icon'),
                                                type: 'icon',
                                                placeholder: t('Select an icon'),
                                            },
                                            {
                                                name: 'description',
                                                label: t('Feature Description'),
                                                type: 'textarea',
                                                placeholder: t('Enter feature description'),
                                            }
                                        ]}
                                        value={data.features.map((feature, index) => ({
                                            id: `feature-${index}`,
                                            title: feature.title || '',
                                            icon: feature.icon || '',
                                            description: feature.description || ''
                                        }))}
                                        onChange={(items) => {
                                            const features = items.map(({ id, ...item }) => item);
                                            setData('features', features);
                                        }}
                                        addButtonText={t('Add Feature')}
                                        deleteTooltipText={t('Remove Feature')}
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