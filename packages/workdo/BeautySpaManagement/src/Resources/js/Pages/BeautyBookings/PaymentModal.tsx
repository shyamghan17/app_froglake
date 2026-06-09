import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useForm } from '@inertiajs/react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from '@/components/ui/button';
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { DatePicker } from '@/components/ui/date-picker';
import InputError from '@/components/ui/input-error';
import { BeautyBooking } from './types';
import { useFormFields } from '@/hooks/useFormFields';

interface PaymentModalProps {
    isOpen: boolean;
    booking: BeautyBooking | null;
    onClose: () => void;
    serviceName?: string;
}

interface PaymentFormData {
    payment_amount: string;
    description: string;
    service: string;
    total_person: string;
    payment_date: string;
    customer_name: string;
    reference_number: string;
    booking_id: string;
}

export default function PaymentModal({ isOpen, booking, onClose, serviceName }: PaymentModalProps) {
    const { t } = useTranslation();
    
    const { data, setData, post, processing, errors } = useForm<PaymentFormData>({
        payment_amount: booking?.price?.toString() || '',
        description: '',
        service: booking?.service?.toString() || '',
        total_person: booking?.person?.toString() || '',
        payment_date: new Date().toISOString().split('T')[0],
        customer_name: booking?.name || '',
        reference_number: '',
        booking_id: booking?.id?.toString() || '',
    });

    const bankAccountField = useFormFields('bankAccountField', data, setData, errors);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.beauty-bookings.payments.store'), {
            onSuccess: () => {
                onClose();
            }
        });
    };

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{t('Add Payment')} - {booking?.name}</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="payment_amount" required>{t('Payment Amount')}</Label>
                            <Input
                                id="payment_amount"
                                value={data.payment_amount}
                                disabled
                                className="bg-gray-50"
                            />
                            <InputError message={errors.payment_amount} />
                        </div>
                        <div>
                            <Label htmlFor="service" required>{t('Service')}</Label>
                            <Input
                                id="service"
                                value={serviceName || ''}
                                disabled
                                className="bg-gray-50"
                            />
                            <InputError message={errors.service} />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="total_person" required>{t('Total Person')}</Label>
                            <Input
                                id="total_person"
                                value={data.total_person}
                                disabled
                                className="bg-gray-50"
                            />
                            <InputError message={errors.total_person} />
                        </div>
                        <div>
                            <Label required>{t('Payment Date')}</Label>
                            <DatePicker
                                value={data.payment_date}
                                onChange={(date) => setData('payment_date', date)}
                                placeholder={t('Select payment date')}
                            />
                            <InputError message={errors.payment_date} />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="customer_name" required>{t('Customer Name')}</Label>
                            <Input
                                id="customer_name"
                                value={data.customer_name}
                                disabled
                                className="bg-gray-50"
                            />
                            <InputError message={errors.customer_name} />
                        </div>
                        <div>
                            <Label htmlFor="reference_number" required>{t('Reference Number')}</Label>
                            <Input
                                id="reference_number"
                                value={data.reference_number}
                                onChange={(e) => setData('reference_number', e.target.value)}
                                placeholder={t('Enter reference number')}
                                required
                            />
                            <InputError message={errors.reference_number} />
                        </div>
                    </div>

                    {bankAccountField.map((field) => (
                        <div key={field.id}>{field.component}</div>
                    ))}

                    <div>
                        <Label htmlFor="description">{t('Description')}</Label>
                        <Textarea
                            id="description"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            placeholder={t('Enter payment description')}
                            rows={3}
                        />
                        <InputError message={errors.description} />
                        <p className="text-sm text-amber-600 mt-2 font-medium">
                            {t('Note: After payment is made, booking cannot be edited or deleted.')}
                        </p>
                    </div>

                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={onClose}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? t('Processing...') : t('Add Payment')}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}