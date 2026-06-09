import React, { useEffect } from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DateTimeRangePicker } from '@/components/ui/datetime-range-picker';
import { EditAppointmentProps, AppointmentFormData } from './types';

export default function Edit({ appointment, onClose, services }: Omit<EditAppointmentProps, 'teamMembers'>) {
    const { t } = useTranslation();

const { data, setData, put, processing, errors } = useForm<AppointmentFormData>({
        name: appointment.name ?? '',
        email: appointment.email ?? '',
        mobile_no: appointment.mobile_no ?? '',
        booking_start_date: appointment.booking_start_date?.replace('T', ' ').substring(0, 16) ?? '',
        booking_end_date: appointment.booking_end_date?.replace('T', ' ').substring(0, 16) ?? '',
        service_id: appointment.service_id?.toString() ?? '',
        price: appointment.price?.toString() ?? '',
    });

    useEffect(() => {
        if (data.service_id) {
            const selected = services.find(s => s.id.toString() === data.service_id);
            if (selected) setData('price', selected.price.toString());
        }
    }, [data.service_id]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('photo-studio-management.appointments.update', appointment.id), {
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Edit Appointment')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="name">{t('Name')}</Label>
                        <Input id="name" value={data.name} onChange={e => setData('name', e.target.value)} placeholder={t('Enter Name')} required />
                        <InputError message={errors.name} />
                    </div>
                    <div>
                        <Label htmlFor="email">{t('Email')}</Label>
                        <Input id="email" type="email" value={data.email} onChange={e => setData('email', e.target.value)} placeholder={t('Enter Email')} required />
                        <InputError message={errors.email} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <PhoneInputComponent
                            label={t('Mobile No.')}
                            value={data.mobile_no}
                            onChange={value => setData('mobile_no', value || '')}
                            error={errors.mobile_no}
                            required
                        />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Booking Start Date')}</Label>
                        <DateTimeRangePicker id="booking_start_date" mode="single" value={data.booking_start_date} onChange={value => setData('booking_start_date', value)} placeholder={t('Select start date & time')} required />
                        <InputError message={errors.booking_start_date} />
                    </div>
                    <div>
                        <Label required>{t('Booking End Date')}</Label>
                        <DateTimeRangePicker id="booking_end_date" mode="single" value={data.booking_end_date} onChange={value => setData('booking_end_date', value)} placeholder={t('Select end date & time')} required />
                        <InputError message={errors.booking_end_date} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Service')}</Label>
                        <Select value={data.service_id} onValueChange={value => setData('service_id', value)} required>
                            <SelectTrigger><SelectValue placeholder={t('Select Service')} /></SelectTrigger>
                            <SelectContent>
                                {services.map(s => (
                                    <SelectItem key={s.id} value={s.id.toString()}>{s.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.service_id} />
                    </div>
                    <div>
                        <Label htmlFor="price">{t('Price')}</Label>
                        <Input id="price" type="number" min="0" step="0.01" value={data.price} onChange={e => setData('price', e.target.value)} placeholder="0.00" required className="bg-gray-50" readOnly />
                        <InputError message={errors.price} />
                    </div>
                </div>

                <div className="flex justify-end gap-2 pt-2">
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>{processing ? t('Updating...') : t('Update')}</Button>
                </div>
            </form>
        </DialogContent>
    );
}
