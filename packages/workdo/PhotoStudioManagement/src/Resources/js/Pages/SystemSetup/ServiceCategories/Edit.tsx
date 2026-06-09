import React from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { PhotoStudioServiceCategory } from './types';

interface EditServiceCategoryProps {
    serviceCategory: PhotoStudioServiceCategory;
    onClose: () => void;
}

interface EditServiceCategoryFormData {
    name: string;
    description: string;
    status: boolean;
}

export default function Edit({ serviceCategory, onClose }: EditServiceCategoryProps) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditServiceCategoryFormData>({
        name: serviceCategory.name,
        description: serviceCategory.description || '',
        status: Boolean(serviceCategory.status),
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('photo-studio-management.service-categories.update', serviceCategory.id), {
            data: {
                ...data,
                status: data.status,
            },
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-md">
            <DialogHeader>
                <DialogTitle>{t('Edit Service Category')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="name" required>{t('Name')}</Label>
                    <Input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder={t('Enter service category name')}
                        required
                    />
                    <InputError message={errors.name} />
                </div>

                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter description')}
                        rows={3}
                    />
                    <InputError message={errors.description} />
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

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onClose}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
