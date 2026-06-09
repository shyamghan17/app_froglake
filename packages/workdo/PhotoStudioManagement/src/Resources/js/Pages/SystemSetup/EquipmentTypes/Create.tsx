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

interface CreateEquipmentTypeProps {
    onClose: () => void;
}

interface CreateEquipmentTypeFormData {
    name: string;
    description: string;
    status: boolean;
}

export default function Create({ onClose }: CreateEquipmentTypeProps) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateEquipmentTypeFormData>({
        name: '',
        description: '',
        status: true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.equipment-types.store'), {
            data: { ...data, status: data.status },
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-md">
            <DialogHeader>
                <DialogTitle>{t('Create Equipment Type')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="name" required>{t('Name')}</Label>
                    <Input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder={t('Enter equipment type name')}
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
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
