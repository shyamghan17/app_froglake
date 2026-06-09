import React from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/ui/input-error';
import { DatePicker } from '@/components/ui/date-picker';
import { CreatePaymentProps, CreatePaymentFormData } from './types';
import { useFormFields } from '@/hooks/useFormFields';

export default function Create({ appointment, onSuccess }: CreatePaymentProps) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm<CreatePaymentFormData>({
        appointment_id: appointment.id.toString(),
        payment_date:   new Date().toISOString().split('T')[0],
        description:    '',
    });

    const bankAccountField = useFormFields('bankAccountField', data, setData, errors);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.appointment-payments.store'), {
            onSuccess: () => onSuccess(),
        });
    };

    return (
        <DialogContent>
            <DialogHeader className="mb-3">
                <DialogTitle>{t('Create Payment')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <Label>{t('Appointment No.')}</Label>
                        <Input value={appointment.appointment_number} disabled />
                    </div>
                    <div>
                        <Label>{t('Customer Name')}</Label>
                        <Input value={appointment.name} disabled />
                    </div>
                    <div>
                        <Label>{t('Service')}</Label>
                        <Input value={appointment.service?.name || '-'} disabled />
                    </div>
                    <div>
                        <Label>{t('Date')} <span className="text-red-500">*</span></Label>
                        <DatePicker
                            value={data.payment_date}
                            onChange={(date) => setData('payment_date', date)}
                            placeholder={t('Select Date')}
                            required
                        />
                        <InputError message={errors.payment_date} />
                    </div>
                </div>

                <div>
                    <Label>{t('Amount')}</Label>
                    <Input type="number" value={appointment.price.toString()} disabled />
                </div>

                {bankAccountField.map((field) => (
                    <div key={field.id}>{field.component}</div>
                ))}

                <div>
                    <Label>{t('Description')}</Label>
                    <Textarea
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter Description')}
                    />
                    <InputError message={errors.description} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>{processing ? t('Creating...') : t('Create')}</Button>
                </div>
            </form>
        </DialogContent>
    );
}
