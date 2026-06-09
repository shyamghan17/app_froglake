import React from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import MediaPicker from '@/components/MediaPicker';
import { Repeater } from '@/components/ui/repeater';
import { EditCameraKitProps, CameraKitFormData } from './types';

export default function Edit({ cameraKit, onClose, equipmentTags, equipmentTypes }: EditCameraKitProps) {
    const { t } = useTranslation();
    const { auth } = usePage().props as any;

    const { data, setData, put, processing, errors } = useForm<CameraKitFormData>({
        name: cameraKit.name,
        image: cameraKit.image || '',
        description: cameraKit.description || '',
        tags: Array.isArray(cameraKit.tags) ? cameraKit.tags.map(String) : [],
        specifications: Array.isArray(cameraKit.specifications) ? cameraKit.specifications : [],
        equipment_type_id: cameraKit.equipment_type_id?.toString() || '',
        status: cameraKit.status || 'available',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('photo-studio-management.camera-kits.update', cameraKit.id), {
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-5xl max-h-[95vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Edit Camera Kit')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="name" required>{t('Name')}</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder={t('Enter camera kit name')}
                            required
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div>
                        <Label htmlFor="equipment_type_id" required>{t('Equipment Type')}</Label>
                        <Select value={data.equipment_type_id} onValueChange={(value) => setData('equipment_type_id', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Equipment Type')} />
                            </SelectTrigger>
                            <SelectContent>
                                {equipmentTypes.map((type) => (
                                    <SelectItem key={type.id} value={type.id.toString()}>
                                        {type.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.equipment_type_id} />
                        {equipmentTypes.length === 0 && auth.user?.permissions?.includes('create-photo-studio-equipment-type') && (
                            <p className="text-xs text-gray-500 mt-1">
                                {t('No equipment types found.')} <button type="button" onClick={() => router.get(route('photo-studio-management.equipment-types.index'))} className="text-blue-600 hover:underline">{t('Create equipment type')}</button>
                            </p>
                        )}
                    </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Tags')}</Label>
                        <MultiSelectEnhanced
                            options={equipmentTags.map((tag) => ({
                                value: tag.id.toString(),
                                label: tag.name,
                            }))}
                            value={data.tags}
                            onValueChange={(value) => setData('tags', value)}
                            placeholder={t('Select Equipment Tags...')}
                            searchable
                            className="z-[60]"
                        />
                        <InputError message={errors.tags} />
                        {equipmentTags.length === 0 && auth.user?.permissions?.includes('create-photo-studio-equipment-tag') && (
                            <p className="text-xs text-gray-500 mt-1">
                                {t('No equipment tags found.')} <button type="button" onClick={() => router.get(route('photo-studio-management.equipment-tags.index'))} className="text-blue-600 hover:underline">{t('Create equipment tag')}</button>
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="status" required>{t('Status')}</Label>
                        <Select value={data.status} onValueChange={(value) => setData('status', value as 'available' | 'unavailable')}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Status')} />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="available">{t('Available')}</SelectItem>
                                <SelectItem value="unavailable">{t('Unavailable')}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError message={errors.status} />
                    </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="description" required>{t('Description')}</Label>
                        <Textarea
                            id="description"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            placeholder={t('Enter description')}
                            rows={5}
                        />
                        <InputError message={errors.description} />
                    </div>
                    <div>
                        <MediaPicker
                            label={t('Image')}
                            value={data.image}
                            onChange={(value) => setData('image', Array.isArray(value) ? value[0] || '' : value)}
                            placeholder={t('Select Image...')}
                            showPreview={true}
                            multiple={false}
                        />
                        <InputError message={errors.image} />
                    </div>
                </div>
                <div>
                    <Label required>{t('Specifications')}</Label>
                    <Repeater
                        fields={[
                            { name: 'field_name', label: t('Field Name'), type: 'text', placeholder: t('Enter field name'), required: true, layout: { colSpan: 1 } },
                            { name: 'description', label: t('Description'), type: 'text', placeholder: t('Enter description'), required: true, layout: { colSpan: 1 } },
                        ]}
                        layout={{ type: 'grid', columns: 2 }}
                        value={data.specifications.map((spec, index) => ({
                            id: `spec-${index}`,
                            field_name: spec.field_name || '',
                            description: spec.description || '',
                        }))}
                        onChange={(items) => setData('specifications', items.map(({ id, ...item }) => item as any))}
                        addButtonText={t('Add Specification')}
                        deleteTooltipText={t('Remove')}
                        minItems={1}
                        showDefault={true}
                        errors={errors}
                    />
                    <InputError message={errors.specifications} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
