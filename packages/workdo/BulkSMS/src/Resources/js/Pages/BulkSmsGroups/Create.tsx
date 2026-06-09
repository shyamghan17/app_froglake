import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import { CreateBulkSmsGroupProps, CreateBulkSmsGroupFormData } from './types';
import { usePage } from '@inertiajs/react';

export default function Create({ onSuccess }: CreateBulkSmsGroupProps) {
    const { bulksmscontacts } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateBulkSmsGroupFormData>({
        name: '',
        contacts: [] as string[],
    });

    const contactOptions = bulksmscontacts?.map((contact: any) => ({
        value: contact.id.toString(),
        label: contact.name
    })) || [];

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bulk-s-m-s.bulk-sms-groups.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Group')}</DialogTitle>
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
                    <Label>{t('Contacts')}</Label>
                    <MultiSelectEnhanced
                        options={contactOptions}
                        value={data.contacts}
                        onValueChange={(value) => setData('contacts', value)}
                        placeholder={t('Select Contacts...')}
                        searchable={true}
                    />
                    <InputError message={errors.contacts} />
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