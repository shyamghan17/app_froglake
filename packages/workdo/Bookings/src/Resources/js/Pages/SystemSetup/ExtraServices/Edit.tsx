import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { CurrencyInput } from '@/components/ui/currency-input';
import { EditExtraServiceProps, EditExtraServiceFormData } from './types';

export default function Edit({ extraservice, onSuccess }: EditExtraServiceProps) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditExtraServiceFormData>({
        name: extraservice.name ?? '',
        amount: extraservice.amount ?? '',
        status: extraservice.status ?? false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('bookings.booking-extra-services.update', extraservice.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Extra Service')}</DialogTitle>
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
                    <CurrencyInput
                        label={t('Amount')}
                        value={data.amount}
                        onChange={(value) => setData('amount', value)}
                        error={errors.amount}
                        required
                    />
                </div>
                
                <div className="flex items-center space-x-2">
                    <Switch
                        id="status"
                        checked={data.status || false}
                        onCheckedChange={(checked) => setData('status', !!checked)}
                    />
                    <Label htmlFor="status" className="cursor-pointer">{t('Status')}</Label>
                    <InputError message={errors.status} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={() => onSuccess()}>
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
