import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { EditSuggestionProps, CreateSuggestionFormData } from './types';
import { usePage } from '@inertiajs/react';

export default function Edit({ onSuccess, suggestion }: EditSuggestionProps) {
    const { categories, suggestioncategories } = usePage<any>().props;
    
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<CreateSuggestionFormData>({
        title: suggestion?.title || '',
        category_id: suggestion?.category?.id ? String(suggestion.category.id) : '',
        description: suggestion?.description || '',
        is_anonymous: suggestion?.is_anonymous || false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('suggestions.update', suggestion.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };
    
    const handleCategoryValue = (value: string) => {
        setData('category_id', value);
    };

    const availableCategories = categories || suggestioncategories || [];
    
    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Suggestion')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="title">{t('Title')}</Label>
                    <Input
                        id="title"
                        type="text"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        placeholder={t('Enter title')}
                        required
                    />
                    <InputError message={errors.title} />
                </div>
                
                <div>
                    <Label required htmlFor="category_id">{t('Category')}</Label>
                    <Select value={data.category_id || ''} onValueChange={handleCategoryValue} required>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Category')} />
                        </SelectTrigger>
                        <SelectContent>
                            {availableCategories.length > 0 ? (
                                availableCategories.filter((item: any) => item.id && String(item.id).trim()).map((item: any) => (
                                    <SelectItem key={item.id} value={String(item.id)}>
                                        {item.name}
                                    </SelectItem>
                                ))
                            ) : (
                                <SelectItem value="no-categories" disabled>
                                    {t('No categories available')}
                                </SelectItem>
                            )}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.category_id} />
                </div>
                
                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter description')}
                        rows={3}
                        required
                    />
                    <InputError message={errors.description} />
                </div>
                
                <div>
                    <div className="flex items-center space-x-2 mt-2">
                        <Checkbox
                            id="is_anonymous"
                            checked={data.is_anonymous || false}
                            onCheckedChange={(checked) => setData('is_anonymous', !!checked)}
                        />
                        <Label htmlFor="is_anonymous" className="cursor-pointer text-sm">
                            {t('Submit anonymously')}
                        </Label>
                    </div>
                    <InputError message={errors.is_anonymous} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
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
