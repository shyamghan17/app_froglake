import React, { useState, useEffect } from 'react';
import { useForm, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { DatePicker } from '@/components/ui/date-picker';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { toast } from 'sonner';

interface CreateProps {
    onSuccess: () => void;
    auth?: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Create({ onSuccess, auth }: CreateProps) {
    const { t } = useTranslation();
    const { beautyservices, reference_options, auth: pageAuth } = usePage().props as any;
    const authData = auth || pageAuth;
    const [servicePrice, setServicePrice] = useState('');
    const [offerNote, setOfferNote] = useState('');
    const [timeSlots, setTimeSlots] = useState([]);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        service: '',
        date: '',
        time_slot: '',
        person: 1,
        service_price: '',
        phone_number: '',
        gender: 'male',
        reference: '',
        additional_notes: '',
        start_time: '',
        end_time: ''
    });

    const loadServicePrice = async (serviceId: string, selectedDate?: string) => {
        if (!serviceId) {
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const requestData = { service_id: serviceId, date: selectedDate || '' };

            const response = await fetch(route('beauty-spa-management.beauty-bookings.get-service-price'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (result.formatted_price) {
                setServicePrice(result.formatted_price);
                setData('service_price', result.formatted_price);
                setOfferNote(result.offer_note || '');
            }
        } catch (error) {
        }
    };

    const loadTimeSlots = async () => {
        if (!data.service || !data.date) {
            setTimeSlots([]);
            return;
        }

        try {
            const response = await fetch(route('beauty-spa-management.beauty-bookings.check-holiday'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ service: data.service, date: data.date })
            });

            const result = await response.json();
            if (result.is_success && result.slots) {
                setTimeSlots(result.slots);
            } else {
                setTimeSlots([]);
                if (result.message) {
                    toast.error(result.message);
                }
            }
        } catch (error) {
            setTimeSlots([]);
        }
    };

    useEffect(() => {
        loadTimeSlots();
    }, [data.service, data.date]);

    const handleServiceChange = (value: string) => {
        setData('service', value);
        loadServicePrice(value, data.date);
    };

    const handleDateChange = (value: string) => {
        setData('date', value);
        if (data.service) {
            loadServicePrice(data.service, value);
        }
    };

    const handleTimeSlotChange = (value: string) => {
        setData('time_slot', value);
        if (value) {
            const times = value.split('-');
            setData('start_time', times[0]);
            setData('end_time', times[1]);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        // Validate slot capacity
        if (data.service && data.date && data.time_slot && data.person) {
            try {
                const response = await fetch(route('beauty-spa-management.beauty-bookings.validate-slot-capacity'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        service: data.service,
                        date: data.date,
                        time_slot: data.time_slot,
                        persons: data.person
                    })
                });

                const result = await response.json();
                if (!result.is_success) {
                    toast.error(result.message);
                    return;
                }
            } catch (error) {
            }
        }

        post(route('beauty-spa-management.beauty-bookings.store'), {
            onSuccess: () => onSuccess()
        });
    };

    return (
        <DialogContent className="max-w-4xl">
            <DialogHeader>
                <DialogTitle>{t('Create Booking')}</DialogTitle>
            </DialogHeader>

            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="name">{t('Name')}</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Enter Name"
                            required
                        />
                        {errors.name && <p className="text-red-500 text-sm">{errors.name}</p>}
                    </div>

                    <div>
                        <Label htmlFor="email">{t('Email')}</Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="Enter Email"
                            required
                        />
                        {errors.email && <p className="text-red-500 text-sm">{errors.email}</p>}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="service" required>{t('Service')}</Label>
                        <Select value={data.service} onValueChange={handleServiceChange}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select service" />
                            </SelectTrigger>
                            <SelectContent>
                                {beautyservices?.map((service: any) => (
                                    <SelectItem key={service.id} value={service.id.toString()}>
                                        {service.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.service && <p className="text-red-500 text-sm">{errors.service}</p>}
                        {beautyservices?.length === 0 && authData?.user?.permissions?.includes('create-beauty-services') && (
                            <p className="text-xs text-gray-500 mt-1">
                                {t('Create service here.')} <button type="button" onClick={() => router.get(route('beauty-spa-management.services.index'))} className="text-blue-600 hover:underline">{t('Create service')}</button>
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="date" required>{t('Date')}</Label>
                        <DatePicker
                            id="date"
                            value={data.date}
                            onChange={(value) => handleDateChange(value)}
                            required
                        />
                        {errors.date && <p className="text-red-500 text-sm">{errors.date}</p>}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="time_slot" required>{t('Time Slot')}</Label>
                        <Select value={data.time_slot} onValueChange={handleTimeSlotChange}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select time slot" />
                            </SelectTrigger>
                            <SelectContent>
                                {timeSlots.length === 0 ? (
                                    <SelectItem value="no-slots" disabled>{t('No time slots available')}</SelectItem>
                                ) : (
                                    timeSlots.map((slot: any, index: number) => {
                                        const slotValue = `${slot.start_time}-${slot.end_time}`;
                                        return (
                                            <SelectItem key={`${slot.start_time}-${slot.end_time}-${index}`} value={slotValue}>
                                                {slot.display} ({slot.available_seats} {t('seats')})
                                            </SelectItem>
                                        );
                                    })
                                )}
                            </SelectContent>
                        </Select>
                        {errors.time_slot && <p className="text-red-500 text-sm">{errors.time_slot}</p>}
                    </div>

                    <div>
                        <Label htmlFor="person">{t('Person')}</Label>
                        <Input
                            id="person"
                            type="number"
                            min="1"
                            value={data.person}
                            onChange={(e) => setData('person', parseInt(e.target.value) || 1)}
                            placeholder="Enter Total Person"
                            required
                        />
                        {errors.person && <p className="text-red-500 text-sm">{errors.person}</p>}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="service_price" required>{t('Service Price')}</Label>
                        <Input
                            id="service_price"
                            value={servicePrice}
                            readOnly
                            placeholder="Service Price"
                        />
                        {offerNote && (
                            <p className="text-green-600 text-sm mt-1">{offerNote}</p>
                        )}
                    </div>

                    <div>
                        <PhoneInputComponent
                            label="Phone Number"
                            value={data.phone_number}
                            onChange={(value) => setData('phone_number', value)}
                            error={errors.phone_number}
                            required
                        />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                   <div>
                        <Label htmlFor="reference">{t('How did you hear about us?')}</Label>
                        <Select value={data.reference} onValueChange={(value) => setData('reference', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select Reference" />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.entries(reference_options || {}).map(([key, value]) => (
                                    <SelectItem key={key} value={key}>
                                        {value as string}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.reference && <p className="text-red-500 text-sm">{errors.reference}</p>}
                    </div>
                    <div>
                        <Label required>{t('Gender')}</Label>
                        <RadioGroup value={data.gender} onValueChange={(value) => setData('gender', value)} className="mt-2">
                            <div className="flex items-center space-x-2">
                                <RadioGroupItem value="male" id="male" />
                                <Label htmlFor="male">{t('Male')}</Label>
                            </div>
                            <div className="flex items-center space-x-2">
                                <RadioGroupItem value="female" id="female" />
                                <Label htmlFor="female">{t('Female')}</Label>
                            </div>
                            <div className="flex items-center space-x-2">
                                <RadioGroupItem value="other" id="other" />
                                <Label htmlFor="other">{t('Other')}</Label>
                            </div>
                        </RadioGroup>
                        {errors.gender && <p className="text-red-500 text-sm">{errors.gender}</p>}
                    </div>

                </div>

                <div>
                    <Label htmlFor="additional_notes">{t('Additional Notes')}</Label>
                    <Textarea
                        id="additional_notes"
                        value={data.additional_notes}
                        onChange={(e) => setData('additional_notes', e.target.value)}
                        placeholder="Any special requests?"
                        rows={3}
                    />
                    {errors.additional_notes && <p className="text-red-500 text-sm">{errors.additional_notes}</p>}
                </div>

                <div className="flex justify-end gap-2 pt-4">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Creating...' : 'Create'}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}