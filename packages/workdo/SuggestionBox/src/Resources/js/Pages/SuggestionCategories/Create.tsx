import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { CreateSuggestionCategoryProps, CreateSuggestionCategoryFormData } from './types';

export default function Create({ onSuccess }: CreateSuggestionCategoryProps) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateSuggestionCategoryFormData>({
        name: '',
        color: '#FF6B6B',
        description: '',
        is_active: false,
        display_order: '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('suggestion-categories.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Category')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="name">{t('Name')}</Label>
                    <Input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder={t('Enter Name')}
                        required
                    />
                    <InputError message={errors.name} />
                </div>
                
                <div>
                    <Label htmlFor="color">{t('Color')}</Label>
                    <Input
                        id="color"
                        type="color"
                        value={data.color}
                        onChange={(e) => setData('color', e.target.value)}
                    />
                    <InputError message={errors.color} />
                </div>
                
                <div>
                    <Label htmlFor="display_order">{t('Display Order')}</Label>
                    <Input
                        id="display_order"
                        type="number"
                        step="1"
                        min="0"
                        value={data.display_order}
                        onChange={(e) => setData('display_order', e.target.value)}
                        placeholder="0"
                        required
                    />
                    <InputError message={errors.display_order} />
                </div>
                
                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter Description')}
                        rows={3}
                    />
                    <InputError message={errors.description} />
                </div>
                
                <div className="flex items-center space-x-2">
                    <Switch
                        id="is_active"
                        checked={data.is_active || false}
                        onCheckedChange={(checked) => setData('is_active', !!checked)}
                    />
                    <Label htmlFor="is_active" className="cursor-pointer">{t('Is Active')}</Label>
                    <InputError message={errors.is_active} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}