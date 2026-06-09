import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import MediaPicker from '@/components/MediaPicker';
import { Repeater } from '@/components/ui/repeater';
import SystemSetupSidebar from "../SystemSetupSidebar";
import { getImagePath } from '@/utils/helpers';
import { Save } from 'lucide-react';

interface AboutUsSettings {
    header_title: string;
    header_description: string;
    banner_image: string;
    title: string;
    description: string;
    mission_title: string;
    mission_subtitle: string;
    mission_content_title: string;
    mission_content_description: string;
    mission_features: { icon: string; title: string; description: string; }[];
    team_title: string;
    team_subtitle: string;
    team_members: { image: string; name: string; position: string; description: string; }[];
}

interface AboutUsSettingsProps {
    settings: AboutUsSettings;
}

export default function Index({ settings }: AboutUsSettingsProps) {
    const { t } = useTranslation();
    const [isSubmitting, setIsSubmitting] = useState(false);

    const { data, setData, post, errors, processing } = useForm({
        header_title: settings.header_title || '',
        header_description: settings.header_description || '',
        banner_image: settings.banner_image || '',
        title: settings.title || '',
        description: settings.description || '',
        mission_title: settings.mission_title || '',
        mission_subtitle: settings.mission_subtitle || '',
        mission_content_title: settings.mission_content_title || '',
        mission_content_description: settings.mission_content_description || '',
        mission_features: settings.mission_features && settings.mission_features.length > 0
            ? settings.mission_features
            : [{ icon: '', title: '', description: '' }],
        team_title: settings.team_title || '',
        team_subtitle: settings.team_subtitle || '',
        team_members: settings.team_members && settings.team_members.length > 0
            ? settings.team_members
            : [{ image: '', name: '', position: '', description: '' }]
    });

    useFlashMessages();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        post(route('bookings.about-us-settings.update'), {
            onFinish: () => setIsSubmitting(false)
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bookings'), url: route('bookings.dashboard')},
                {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                {label: t('About Us Setting')}
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('About Us Setting')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="about-us-settings" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('About Us Setting')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your about us page settings')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="header_title" required>{t('Header Title')}</Label>
                                        <Input
                                            id="header_title"
                                            type="text"
                                            value={data.header_title}
                                            onChange={(e) => setData('header_title', e.target.value)}
                                            placeholder={t('Enter Header Title')}
                                            required
                                        />
                                        <InputError message={errors.header_title} />
                                    </div>

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
                                </div>

                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="header_description" required>{t('Header Description')}</Label>
                                        <Textarea
                                            id="header_description"
                                            value={data.header_description}
                                            onChange={(e) => setData('header_description', e.target.value)}
                                            placeholder={t('Enter Header Description')}
                                            rows={3}
                                            required
                                        />
                                        <InputError message={errors.header_description} />
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

                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <MediaPicker
                                            label={t('Banner Background Image')}
                                            value={data.banner_image}
                                            onChange={(value) => setData('banner_image', Array.isArray(value) ? value[0] || '' : value)}
                                            placeholder={t('Choose File')}
                                            showPreview={false}
                                            multiple={false}
                                            accept="image/*"
                                        />
                                        <InputError message={errors.banner_image} />
                                    </div>
                                    <div>
                                        {data.banner_image && (
                                            <img
                                                src={getImagePath(data.banner_image)}
                                                alt={t('Banner Image')}
                                                className="max-w-full max-h-32 object-contain rounded"
                                            />
                                        )}
                                    </div>
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

                    <Card className="shadow-sm mt-6">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Mission Section')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your mission section content')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="mission_title" required>{t('Title')}</Label>
                                        <Input
                                            id="mission_title"
                                            type="text"
                                            value={data.mission_title}
                                            onChange={(e) => setData('mission_title', e.target.value)}
                                            placeholder={t('Enter Title')}
                                            required
                                        />
                                        <InputError message={errors.mission_title} />
                                    </div>

                                    <div>
                                        <Label htmlFor="mission_subtitle" required>{t('Subtitle')}</Label>
                                        <Input
                                            id="mission_subtitle"
                                            type="text"
                                            value={data.mission_subtitle}
                                            onChange={(e) => setData('mission_subtitle', e.target.value)}
                                            placeholder={t('Enter Subtitle')}
                                            required
                                        />
                                        <InputError message={errors.mission_subtitle} />
                                    </div>
                                </div>

                                <div>
                                    <Label htmlFor="mission_content_title" required>{t('Content Title')}</Label>
                                    <Input
                                        id="mission_content_title"
                                        type="text"
                                        value={data.mission_content_title}
                                        onChange={(e) => setData('mission_content_title', e.target.value)}
                                        placeholder={t('Enter Content Title')}
                                        required
                                    />
                                    <InputError message={errors.mission_content_title} />
                                </div>

                                <div>
                                    <Label htmlFor="mission_content_description" required>{t('Content Description')}</Label>
                                    <Textarea
                                        id="mission_content_description"
                                        value={data.mission_content_description}
                                        onChange={(e) => setData('mission_content_description', e.target.value)}
                                        placeholder={t('Enter Content Description')}
                                        rows={4}
                                        required
                                    />
                                    <InputError message={errors.mission_content_description} />
                                </div>

                                <div>
                                    <Label className="mb-3" required>{t('Features')}</Label>
                                    <div className="border rounded-lg p-4 bg-gray-50/50">
                                        <Repeater
                                            fields={[
                                                { name: 'icon', label: t('Icon'), type: 'icon', placeholder: t('Select icon'), required: true },
                                                { name: 'title', label: t('Title'), type: 'text', placeholder: t('Enter title'), required: true },
                                                { name: 'description', label: t('Description'), type: 'textarea', placeholder: t('Enter description'), required: true }
                                            ]}
                                            value={(data.mission_features || []).map((feature, index) => ({
                                                id: `feature-${index}`,
                                                icon: feature.icon || '',
                                                title: feature.title || '',
                                                description: feature.description || ''
                                            }))}
                                            onChange={(items) => {
                                                const features = items.map(({ id, ...item }) => ({
                                                    icon: item.icon || '',
                                                    title: item.title || '',
                                                    description: item.description || ''
                                                }));
                                                setData('mission_features', features);
                                            }}
                                            addButtonText={t('Add Feature')}
                                            deleteTooltipText={t('Delete')}
                                            showDefault={true}
                                            minItems={1}
                                            containerClassName="space-y-3"
                                            itemClassName="border border-gray-200 rounded-lg p-4 bg-white"
                                        />
                                    </div>
                                    <InputError message={errors.mission_features} />
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

                    <Card className="shadow-sm mt-6">
                        <CardContent className="p-6">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium">{t('Team Section')}</h3>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your team section content')}
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="team_title" required>{t('Title')}</Label>
                                        <Input
                                            id="team_title"
                                            type="text"
                                            value={data.team_title}
                                            onChange={(e) => setData('team_title', e.target.value)}
                                            placeholder={t('Enter title')}
                                            required
                                        />
                                        <InputError message={errors.team_title} />
                                    </div>

                                    <div>
                                        <Label htmlFor="team_subtitle" required>{t('Subtitle')}</Label>
                                        <Input
                                            id="team_subtitle"
                                            type="text"
                                            value={data.team_subtitle}
                                            onChange={(e) => setData('team_subtitle', e.target.value)}
                                            placeholder={t('Enter subtitle')}
                                            required
                                        />
                                        <InputError message={errors.team_subtitle} />
                                    </div>
                                </div>

                                <div>
                                    <Label className="mb-3" required>{t('Team Members')}</Label>
                                    <div className="border rounded-lg p-4 bg-gray-50/50">
                                        <Repeater
                                            fields={[
                                                { name: 'image', label: t('Image'), type: 'image', placeholder: t('Select image'), required: true },
                                                { name: 'name', label: t('Name'), type: 'text', placeholder: t('Enter name'), required: true },
                                                { name: 'position', label: t('Position'), type: 'text', placeholder: t('Enter position'), required: true },
                                                { name: 'description', label: t('Description'), type: 'textarea', placeholder: t('Enter description'), required: true }
                                            ]}
                                            value={(data.team_members || []).map((member, index) => ({
                                                id: `member-${index}`,
                                                image: member.image || '',
                                                name: member.name || '',
                                                position: member.position || '',
                                                description: member.description || ''
                                            }))}
                                            onChange={(items) => {
                                                const members = items.map(({ id, ...item }) => ({
                                                    image: item.image || '',
                                                    name: item.name || '',
                                                    position: item.position || '',
                                                    description: item.description || ''
                                                }));
                                                setData('team_members', members);
                                            }}
                                            addButtonText={t('Add Team Member')}
                                            deleteTooltipText={t('Delete')}
                                            showDefault={true}
                                            minItems={1}
                                            containerClassName="space-y-3"
                                            itemClassName="border border-gray-200 rounded-lg p-4 bg-white"
                                        />
                                    </div>
                                    <InputError message={errors.team_members} />
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
