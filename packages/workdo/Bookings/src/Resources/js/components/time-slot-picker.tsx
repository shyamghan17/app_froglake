import React, { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useTranslation } from 'react-i18next';
import { Clock } from 'lucide-react';
import axios from 'axios';
import { formatTime } from '@/utils/helpers';
import { usePage } from '@inertiajs/react';

interface TimeSlot {
    start_time: string;
    end_time: string;
    label: string;
}

interface TimeSlotPickerProps {
    date?: string;
    startTime?: string;
    endTime?: string;
    staffId?: string;
    itemId?: string;
    customerId?: string;
    packageId?: string;
    appointmentId?: number;
    selectedSlot?: TimeSlot | null;
    onSlotSelect?: (slot: TimeSlot | null) => void;
    slotDuration?: number;
    className?: string;
    autoLoad?: boolean;
    primaryColor?: string;
}

export function TimeSlotPicker({ 
    date,
    startTime,
    endTime, 
    staffId,
    itemId,
    customerId,
    packageId,
    appointmentId,
    selectedSlot, 
    onSlotSelect, 
    slotDuration = 30,
    className = '',
    autoLoad = false,
    primaryColor = '#000000'
}: TimeSlotPickerProps) {
    const { t } = useTranslation();
    const { props } = usePage();
    const [timeSlots, setTimeSlots] = useState<TimeSlot[]>([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        // Reset slots when dependencies change
        setTimeSlots([]);
        
        // Auto-fetch if autoLoad is enabled and required fields are present
        if (autoLoad && date && itemId && packageId) {
            fetchTimeSlots();
        }
    }, [date, startTime, endTime, staffId, itemId, customerId, packageId, slotDuration, autoLoad]);

    const fetchTimeSlots = async () => {
        if (!date) return;
        
        setLoading(true);
        try {
            const response = await axios.get(route('bookings.appointments.time-slots'), {
                params: {
                    date,
                    start_time: startTime,
                    end_time: endTime,
                    staff_id: staffId,
                    item_id: itemId,
                    customer_id: customerId,
                    package_id: packageId,
                    appointment_id: appointmentId,
                    slot_duration: slotDuration
                }
            });
            const slots = response.data.slots || [];
            setTimeSlots(slots);
            
            // Auto-select the current appointment slot if in edit mode
            if (startTime && endTime) {
                const currentSlot = slots.find((slot: any) => 
                    slot.start_time === startTime && slot.end_time === endTime
                );
                
                if (currentSlot) {
                    const formattedSlot = {
                        start_time: currentSlot.start_time,
                        end_time: currentSlot.end_time,
                        label: `${formatTime(currentSlot.start_time, props)} - ${formatTime(currentSlot.end_time, props)}`
                    };
                    onSlotSelect?.(formattedSlot);
                }
            }
        } catch (error) {
            console.error('Error fetching time slots:', error);
            setTimeSlots([]);
        } finally {
            setLoading(false);
        }
    };

    const handleSlotSelect = (slot: TimeSlot) => {
        if (selectedSlot?.start_time === slot.start_time) {
            onSlotSelect?.(null);
        } else {
            const formattedSlot = {
                ...slot,
                label: `${formatTime(slot.start_time, props)} - ${formatTime(slot.end_time, props)}`
            };
            onSlotSelect?.(formattedSlot);
        }
    };

    const handleChooseTimeslots = async () => {
        if (!date) {
            console.warn('Date is required for timeslots request');
            return;
        }
        
        await fetchTimeSlots();
    };

    if (!date) {
        return (
            <div className={className}>
                <div className="flex items-center justify-between">
                    <Label>{t('Available Time Slots')}</Label>
                    {!autoLoad && (
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={handleChooseTimeslots}
                            disabled={true}
                            className="text-xs"
                        >
                            {t('Choose Timeslots')}
                        </Button>
                    )}
                </div>
                <div className="mt-2 p-4 border border-dashed border-gray-300 rounded-md text-center text-gray-500">
                    {t('Please select date first')}
                </div>
            </div>
        );
    }

    if (loading) {
        return (
            <div className={className}>
                <div className="flex items-center justify-between">
                    <Label>{t('Available Time Slots')}</Label>
                    {!autoLoad && (
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={handleChooseTimeslots}
                            disabled={true}
                            className="text-xs"
                        >
                            {t('Choose Timeslots')}
                        </Button>
                    )}
                </div>
                <div className="mt-2 p-4 border border-dashed border-gray-300 rounded-md text-center text-gray-500">
                    {t('Loading available slots...')}
                </div>
            </div>
        );
    }

    if (timeSlots.length === 0 && !autoLoad) {
        return (
            <div className={className}>
                <div className="flex items-center justify-between">
                    <Label>{t('Available Time Slots')}</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={handleChooseTimeslots}
                        disabled={loading || !date || !itemId}
                        className="text-xs"
                    >
                        {t('Choose Timeslots')}
                    </Button>
                </div>
                <div className="mt-2 p-4 border border-dashed border-gray-300 rounded-md text-center text-gray-500">
                    <Clock className="w-8 h-8 mx-auto mb-2 text-gray-400" />
                    {t('No available time slots for this date')}
                </div>
            </div>
        );
    }
    
    if (timeSlots.length === 0 && autoLoad) {
        return (
            <div className={className}>
                <Label>{t('Available Time Slots')}</Label>
                <div className="mt-2 p-4 border border-dashed border-gray-300 rounded-md text-center text-gray-500">
                    <Clock className="w-8 h-8 mx-auto mb-2 text-gray-400" />
                    {t('No available time slots for this date')}
                </div>
            </div>
        );
    }

    return (
        <div className={className}>
            <div className="flex items-center justify-between">
                <Label>{t('Available Time Slots')}</Label>
                {!autoLoad && (
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={handleChooseTimeslots}
                        disabled={loading || !date}
                        className="text-xs"
                    >
                        {t('Choose Timeslots')}
                    </Button>
                )}
            </div>
            <div className="mt-2 grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                {timeSlots.map((slot) => (
                    <Button
                        key={slot.start_time}
                        type="button"
                        variant={selectedSlot?.start_time === slot.start_time ? 'default' : 'outline'}
                        size="sm"
                        onClick={() => handleSlotSelect(slot)}
                        className="text-xs"
                        style={selectedSlot?.start_time === slot.start_time ? {
                            backgroundColor: primaryColor,
                            borderColor: primaryColor,
                            color: 'white'
                        } : {}}
                    >
                        {`${formatTime(slot.start_time, props)} - ${formatTime(slot.end_time, props)}`}
                    </Button>
                ))}
            </div>
            {selectedSlot && (
                <div className="mt-2 text-sm text-gray-600">
                    {t('Selected')}: {selectedSlot.label}
                </div>
            )}
        </div>
    );
}