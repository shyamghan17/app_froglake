import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Textarea } from '@/components/ui/textarea';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { EditCustomerProps, EditCustomerFormData } from './types';

export default function Edit({ customer, onSuccess }: EditCustomerProps) {
    const { t } = useTranslation();

    const { data, setData, put, processing, errors, reset } = useForm<EditCustomerFormData>({
        first_name: customer.first_name,
        last_name: customer.last_name,
        email: customer.email,
        mobile_number: customer.mobile_number || '',
        description: customer.description || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('bookings.customers.update', customer.id), {
            onSuccess: () => {
                reset();
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{t('Edit Customer')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="edit_first_name">{t('First Name')}</Label>
                        <Input
                            id="edit_first_name"
                            value={data.first_name}
                            onChange={(e) => setData('first_name', e.target.value)}
                            placeholder={t('Enter first name')}
                            required
                        />
                        <InputError message={errors.first_name} />
                    </div>
                    <div>
                        <Label htmlFor="edit_last_name">{t('Last Name')}</Label>
                        <Input
                            id="edit_last_name"
                            value={data.last_name}
                            onChange={(e) => setData('last_name', e.target.value)}
                            placeholder={t('Enter last name')}
                            required
                        />
                        <InputError message={errors.last_name} />
                    </div>
                </div>
                <div>
                    <Label htmlFor="edit_email">{t('Email')}</Label>
                    <Input
                        id="edit_email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        placeholder={t('Enter email address')}
                        required
                    />
                    <InputError message={errors.email} />
                </div>
                <div>
                    <PhoneInputComponent
                        label={t('Mobile Number')}
                        id="edit_mobile_number"
                        value={data.mobile_number}
                        onChange={(value) => setData('mobile_number', value || '')}
                        placeholder={t('Enter mobile number')}
                        error={errors.mobile_number}
                    />
                </div>
                <div>
                    <Label htmlFor="edit_description">{t('Description')}</Label>
                    <Textarea
                        id="edit_description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter description')}
                        rows={3}
                    />
                    <InputError message={errors.description} />
                </div>
                <div className="flex justify-end gap-2 pt-4">
                    <Button type="button" variant="outline" onClick={() => {
                        reset();
                        onSuccess();
                    }}>
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
