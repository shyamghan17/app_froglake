import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import InputError from '@/components/ui/input-error';
import { TimeSlotPicker } from '../../components/time-slot-picker';
import { CreateAppointmentProps } from './types';
import { formatCurrency } from '@/utils/helpers';

export default function Create({ items, packages, users, customers, onSuccess }: CreateAppointmentProps) {
    const { t } = useTranslation();
    const [selectedTimeSlot, setSelectedTimeSlot] = useState<{start_time: string, end_time: string, label: string} | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        date: '',
        item_id: '',
        package_id: '',
        customer_id: '',
        start_time: '',
        end_time: '',
        range_start_time: '',
        range_end_time: '',
    });

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

    const filteredPackages = packages.filter(pkg => !data.item_id || pkg.item_id?.toString() === data.item_id);

    useEffect(() => {
        if (data.item_id && data.package_id) {
            const isPackageValid = filteredPackages.some(pkg => pkg.id.toString() === data.package_id);
            if (!isPackageValid) {
                setData('package_id', '');
            }
        }
    }, [data.item_id]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.appointments.store'), {
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
                            {filteredPackages.map((pkg) => (
                                <SelectItem key={pkg.id} value={pkg.id.toString()}>
                                    {pkg.name} ({formatCurrency(pkg.price)})
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
                itemId={data.item_id}
                customerId={data.customer_id}
                packageId={data.package_id}
                selectedSlot={selectedTimeSlot}
                onSlotSelect={setSelectedTimeSlot}
                slotDuration={30}
                primaryColor="hsl(var(--primary))"
            />
            <InputError message={errors.start_time} />
            <InputError message={errors.end_time} />

            <div className="flex justify-end gap-2 pt-4">
                <Button type="submit" disabled={processing || !selectedTimeSlot}>
                    {processing ? t('Creating...') : t('Create')}
                </Button>
            </div>
        </form>
    );
}
