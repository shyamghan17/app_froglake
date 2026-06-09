import React from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import MediaPicker from '@/components/MediaPicker';
import { CreateServiceProps, ServiceFormData } from './types';

export default function Create({ onClose, serviceCategories, cameraKits }: CreateServiceProps) {
    const { t } = useTranslation();
    const { auth } = usePage().props as any;

    const { data, setData, post, processing, errors } = useForm<ServiceFormData>({
        name: '',
        service_category_ids: [],
        description: '',
        image: '',
        price: '',
        status: true,
        camera_kit_ids: [],
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.services.store'), {
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-3xl max-h-[95vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Create Service')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="name" >{t('Service Name')}</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder={t('Enter service name')}
                            required
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div>
                        <Label htmlFor="price" >{t('Price')}</Label>
                        <Input
                            id="price"
                            type="number"
                            min="0"
                            step="0.01"
                            value={data.price}
                            onChange={(e) => setData('price', e.target.value)}
                            placeholder={t('Enter price')}
                            required
                        />
                        <InputError message={errors.price} />
                    </div>
                </div>

                <div>
                    <Label required>{t('Service Category')}</Label>
                    <MultiSelectEnhanced
                        options={serviceCategories.map((c) => ({ value: c.id.toString(), label: c.name }))}
                        value={data.service_category_ids}
                        onValueChange={(value) => setData('service_category_ids', value)}
                        placeholder={t('Select Service Categories...')}
                        searchable
                        className="z-[60]"
                    />
                    {serviceCategories.length === 0 && auth.user?.permissions?.includes('create-photo-studio-service-category') && (
                        <p className="text-xs text-gray-500 mt-1">
                            {t('No service categories found.')} <button type="button" onClick={() => router.get(route('photo-studio-management.service-categories.index'))} className="text-blue-600 hover:underline">{t('Create service category')}</button>
                        </p>
                    )}
                    <InputError message={errors.service_category_ids} />
                </div>

                <div>
                    <Label required>{t('Camera Kits')}</Label>
                    <MultiSelectEnhanced
                        options={cameraKits.map((k) => ({ value: k.id.toString(), label: k.name }))}
                        value={data.camera_kit_ids}
                        onValueChange={(value) => setData('camera_kit_ids', value)}
                        placeholder={t('Select Camera Kits...')}
                        searchable
                        className="z-[60]"
                    />
                    <InputError message={errors.camera_kit_ids} />
                </div>

                <div className="flex items-center space-x-2">
                        <Label htmlFor="status">{t('Status')}</Label>
                        <Switch
                            id="status"
                            checked={data.status}
                            onCheckedChange={(checked) => setData('status', checked)}
                        />
                        <InputError message={errors.status} />
                    </div>

                <div>
                    <Label required>{t('Image')}</Label>
                        <MediaPicker
                            value={data.image}
                            onChange={(value) => setData('image', Array.isArray(value) ? value[0] || '' : value)}
                            placeholder={t('Select Image...')}
                            showPreview={true}
                            multiple={false} 
                        />
                        <InputError message={errors.image} />
                    </div>

                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter description')} required
                        rows={4}
                    />
                    <InputError message={errors.description} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
