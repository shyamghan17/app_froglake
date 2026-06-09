import { useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';
import { DatePicker } from '@/components/ui/date-picker';
import { Textarea } from '@/components/ui/textarea';
import { useFormFields } from '@/hooks/useFormFields';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Appointment } from './types';

interface PaymentProps {
    appointment: Appointment;
    onSuccess: () => void;
}

export default function Payment({ appointment, onSuccess }: PaymentProps) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        appointment_id: appointment.id,
        payment_date: new Date().toISOString().split('T')[0],
        bank_account_id: '',
        reference_number: `PAY-${Date.now()}`,
        amount: appointment.amount?.toString() || '0',
        notes: ''
    });

    useEffect(() => {
        setData({
            appointment_id: appointment.id,
            payment_date: new Date().toISOString().split('T')[0],
            bank_account_id: '',
            reference_number: `PAY-${Date.now()}`,
            amount: appointment.amount?.toString() || '0',
            notes: ''
        });
    }, [appointment.id]);

    const bankAccountField = useFormFields('bankAccountField', data, setData, errors);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.appointments.store-payment'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{t('Process Payment')}</DialogTitle>
            </DialogHeader>

            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="payment_date" required>{t('Payment Date')}</Label>
                        <DatePicker
                            id="payment_date"
                            value={data.payment_date}
                            onChange={(value) => setData('payment_date', value)}
                            placeholder={t('Select payment date')}
                        />
                        <InputError message={errors.payment_date} />
                    </div>

                    {bankAccountField.map((field) => (
                        <div key={field.id}>{field.component}</div>
                    ))}
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="reference_number">{t('Reference Number')}</Label>
                        <Input
                            id="reference_number"
                            value={data.reference_number}
                            onChange={(e) => setData('reference_number', e.target.value)}
                            placeholder={t('Enter reference number')}
                        />
                        <InputError message={errors.reference_number} />
                    </div>

                    <div>
                        <Label htmlFor="amount" required>{t('Payment Amount')}</Label>
                        <CurrencyInput
                            id="amount"
                            type="number"
                            step="0.01"
                            value={data.amount}
                            onChange={(e) => setData('amount', e.target.value)}
                            disabled
                        />
                        <InputError message={errors.amount} />
                    </div>
                </div>

                <div>
                    <Label htmlFor="notes">{t('Notes')}</Label>
                    <Textarea
                        id="notes"
                        value={data.notes}
                        onChange={(e) => setData('notes', e.target.value)}
                        placeholder={t('Enter payment notes')}
                        rows={3}
                    />
                    <InputError message={errors.notes} />
                </div>

                <div className="flex justify-end gap-2 pt-4">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Processing...') : t('Add Payment')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
