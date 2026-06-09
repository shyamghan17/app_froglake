import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import InputError from '@/components/ui/input-error';
import { TimeSlotPicker } from '../../components/time-slot-picker';
import { EditAppointmentProps } from './types';

export default function Edit({ appointment, items, packages, users, customers, onSuccess }: EditAppointmentProps) {
    const { t } = useTranslation();
    const [selectedTimeSlot, setSelectedTimeSlot] = useState<{start_time: string, end_time: string, label: string} | null>(null);

    const { data, setData, put, processing, errors, reset } = useForm({
        date: appointment.date,
        item_id: appointment.item_id?.toString() || '',
        package_id: appointment.package_id?.toString() || '',
        customer_id: appointment.customer_id?.toString() || '',
        start_time: appointment.start_time,
        end_time: appointment.end_time,
        range_start_time: '09:00',
        range_end_time: '17:00',
        status: appointment.status,
        payment_status: appointment.payment_status,
    });

    useEffect(() => {
        if (appointment.start_time && appointment.end_time) {
            setSelectedTimeSlot({
                start_time: appointment.start_time,
                end_time: appointment.end_time,
                label: `${appointment.start_time} - ${appointment.end_time}`
            });
        }
    }, [appointment]);

    useEffect(() => {
        if (selectedTimeSlot) {
            setData(prev => ({
                ...prev,
                start_time: selectedTimeSlot.start_time,
                end_time: selectedTimeSlot.end_time
            }));
        }
    }, [selectedTimeSlot]);

    useEffect(() => {
        setSelectedTimeSlot(null);
    }, [data.date, data.item_id, data.customer_id, data.package_id]);

    const availablePackages = packages.filter(pkg => 
        pkg.id?.toString() === data.package_id || 
        (!data.item_id || pkg.item_id?.toString() === data.item_id)
    );

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('bookings.appointments.update', appointment.id), {
            onSuccess: () => {
                reset();
                setSelectedTimeSlot(null);
                onSuccess?.();
            }
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div>
                <Label htmlFor="date" required>{t('Date')}</Label>
                <DatePicker
                    id="date"
                    value={data.date}
                    onChange={(value) => setData('date', value)}
                    placeholder={t('Select date')}
                    required
                />
                <InputError message={errors.date} />
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label htmlFor="item_id" required>{t('Item')}</Label>
                    <Select value={data.item_id} onValueChange={(value) => setData('item_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select item')} />
                        </SelectTrigger>
                        <SelectContent>
                            {items.map((item) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.item_id} />
                </div>
                <div>
                    <Label htmlFor="package_id" required>{t('Package')}</Label>
                    <Select value={data.package_id} onValueChange={(value) => setData('package_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select package')} />
                        </SelectTrigger>
                        <SelectContent>
                            {availablePackages.map((pkg) => (
                                <SelectItem key={pkg.id} value={pkg.id.toString()}>
                                    {pkg.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.package_id} />
                </div>
            </div>

            <div>
                <Label htmlFor="customer_id">{t('Customer')}</Label>
                <Select value={data.customer_id} onValueChange={(value) => setData('customer_id', value)}>
                    <SelectTrigger>
                        <SelectValue placeholder={t('Select customer')} />
                    </SelectTrigger>
                    <SelectContent>
                        {customers.map((customer) => (
                            <SelectItem key={customer.id} value={customer.id.toString()}>
                                {customer.first_name} {customer.last_name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <InputError message={errors.customer_id} />
            </div>

            <TimeSlotPicker
                date={data.date}
                startTime={appointment.start_time}
                endTime={appointment.end_time}
                itemId={data.item_id}
                customerId={data.customer_id}
                packageId={data.package_id}
                appointmentId={appointment.id}
                selectedSlot={selectedTimeSlot}
                onSlotSelect={setSelectedTimeSlot}
                slotDuration={30}
                autoLoad={true}
                primaryColor="hsl(var(--primary))"
            />
            <InputError message={errors.start_time} />
            <InputError message={errors.end_time} />

            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label htmlFor="status">{t('Status')}</Label>
                    <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                            <SelectItem value="confirmed">{t('Confirmed')}</SelectItem>
                            <SelectItem value="completed">{t('Completed')}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.status} />
                </div>
                <div>
                    <Label htmlFor="payment_status">{t('Payment Status')}</Label>
                    <Select value={data.payment_status} onValueChange={(value) => setData('payment_status', value)}>
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                            <SelectItem value="paid">{t('Paid')}</SelectItem>
                            <SelectItem value="failed">{t('Failed')}</SelectItem>
                            <SelectItem value="refunded">{t('Refunded')}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.payment_status} />
                </div>
            </div>

            <div className="flex justify-end gap-2 pt-4">
                <Button type="submit" disabled={processing || !selectedTimeSlot}>
                    {processing ? t('Updating...') : t('Update')}
                </Button>
            </div>
        </form>
    );
}
