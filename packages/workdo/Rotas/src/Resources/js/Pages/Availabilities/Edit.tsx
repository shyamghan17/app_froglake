import { DialogContent, DialogHeader, DialogTitle, DialogDescription } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditAvailabilityProps, UpdateAvailabilityFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog';
import FullCalendar from '@fullcalendar/react';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import './schedule.css';


const DAYS_OF_WEEK = [
    { key: 'monday', label: 'Monday' },
    { key: 'tuesday', label: 'Tuesday' },
    { key: 'wednesday', label: 'Wednesday' },
    { key: 'thursday', label: 'Thursday' },
    { key: 'friday', label: 'Friday' },
    { key: 'saturday', label: 'Saturday' },
    { key: 'sunday', label: 'Sunday' },
];

export default function Edit({ availability, onSuccess }: EditAvailabilityProps) {
    const { employees, currentUserEmployee, $startTime, $endTime, isNotEmpType, companyAllSetting } = usePage<any>().props;
    const { t } = useTranslation();
    const [showDialog, setShowDialog] = useState(false);
    const [selectedSlot, setSelectedSlot] = useState<any>(null);
    const [overlapError, setOverlapError] = useState<string>('');
    const calendarRef = useRef<FullCalendar>(null);

    const timeFormat = companyAllSetting?.timeFormat || 'H:i';
    const is12Hour = timeFormat === 'g:i A';

     useEffect(() => {
        const timer = setTimeout(() => {
            if (calendarRef.current) {
                calendarRef.current.getApi().updateSize();
            }
        }, 300);
        return () => clearTimeout(timer);
    }, []);
    useEffect(() => {
        const handleResize = () => {
            if (calendarRef.current) {
                calendarRef.current.getApi().updateSize();
            }
        };
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);
    // Function to check for overlaps
    const checkOverlap = (newSlot: any, existingSlots: any[]) => {
        const newStart = new Date(`2024-01-01T${newSlot.start_time}:00`);
        const newEnd = new Date(`2024-01-01T${newSlot.end_time}:00`);
        
        for (const slot of existingSlots) {
            if (slot.day === newSlot.day) {
                const existingStart = new Date(`2024-01-01T${slot.start_time}:00`);
                const existingEnd = new Date(`2024-01-01T${slot.end_time}:00`);
                
                // Check for overlap: slots overlap if start1 < end2 AND start2 < end1
                if (newStart < existingEnd && existingStart < newEnd) {
                    return {
                        hasOverlap: true,
                        message: `Time slot ${newSlot.start_time}-${newSlot.end_time} overlaps with existing ${slot.start_time}-${slot.end_time} on ${newSlot.day}`
                    };
                }
            }
        }
        return { hasOverlap: false, message: '' };
    };

    const handleSlotSelection = (day: string, startTime: string, endTime: string, type: string) => {
        const newSlot = { day, start_time: startTime, end_time: endTime, type };
        
        // Add new slot
        const newAvailability = [...data.availability, newSlot];
        
        setData('availability', newAvailability);
        setOverlapError('');
    };

    const { data, setData, put, processing, errors } = useForm<UpdateAvailabilityFormData>({
        employee_id: isNotEmpType ? (availability.employee_id?.toString() || '') : (currentUserEmployee?.id?.toString() || ''),
        name: availability.name || '',
        start_date: availability.start_date || '',
        end_date: availability.end_date || '',
        availability: availability.availability || [],
    });




    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('rotas.availabilities.update', availability.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Edit Availability')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-6">
                <div className={`grid ${isNotEmpType ? 'grid-cols-2' : 'grid-cols-1'} gap-4`}>
                    {isNotEmpType && (
                        <div>
                            <Label htmlFor="employee_id" required>{t('Employee')}</Label>
                            <Select value={data.employee_id?.toString() || ''} onValueChange={(value) => setData('employee_id', value)} required>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Employee')} />
                                </SelectTrigger>
                                <SelectContent searchable={true}>
                                    {employees?.map((item: any) => (
                                        <SelectItem key={item.id} value={item.id.toString()}>
                                            {item.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.employee_id} />
                        </div>
                    )}
                    
                    {!isNotEmpType && (
                        <input type="hidden" name="employee_id" value={data.employee_id} />
                    )}

                    <div>
                        <Label htmlFor="name" required>{t('Name')}</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder={t('Enter availability name')}
                            required
                        />
                        <InputError message={errors.name} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="start_date" required>{t('Start Date')}</Label>
                        <DatePicker
                            id="start_date"
                            value={data.start_date}
                            onChange={(date) => setData('start_date', date)}
                            placeholder={t('Select Start Date')}
                            required
                        />
                        <InputError message={errors.start_date} />
                    </div>

                    <div>
                        <Label htmlFor="end_date" required>{t('End Date')}</Label>
                        <DatePicker
                           id="end_date"
                            value={data.end_date}
                            onChange={(date) => setData('end_date', date)}
                            placeholder={t('Select End Date')}
                            required
                        />
                        <InputError message={errors.end_date} />
                    </div>
                </div>

                {overlapError && (
                    <div className="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
                        <svg className="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 9a1 1 0 100-2 1 1 0 000 2z" clipRule="evenodd" />
                        </svg>
                        <span>{overlapError}</span>
                    </div>
                )}

                <div className="grid grid-cols-1 gap-4">
                    {data.employee_id ? (
                        <>
                            <div style={{ height: '400px' }}>
                                <FullCalendar
                                    ref={calendarRef}
                                    plugins={[timeGridPlugin, interactionPlugin]}
                                    initialView="timeGridWeek"
                                    headerToolbar={{
                                        left: '',
                                        center: '',
                                        right: ''
                                    }}
                                    slotLabelFormat={{
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: is12Hour
                                    }}
                                    eventTimeFormat={{
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: is12Hour
                                    }}
                                    dayHeaderFormat={{ weekday: 'long' }}
                                    initialDate="2024-01-01"
                                    validRange={{
                                        start: '2024-01-01',
                                        end: '2024-01-08'
                                    }}
                                    events={data.availability.map((item: any, index: number) => {
                                        const dayIndex = DAYS_OF_WEEK.findIndex(d => d.key === item.day);
                                        const calendarDay = dayIndex + 1;
                                        const eventDate = `2024-01-0${calendarDay}`;
                                        const isAvailableSlot = item.type !== 'unavailable';
                                        
                                        // Handle overnight shifts (crossing midnight)
                                        const isOvernightShift = item.end_time < item.start_time;
                                        const nextDay = calendarDay === 7 ? 1 : calendarDay + 1;
                                        const endDate = isOvernightShift ? `2024-01-0${nextDay}` : eventDate;
                                        
                                        return {
                                            id: index.toString(),
                                            title: isAvailableSlot ? 'Available' : 'Unavailable',
                                            start: `${eventDate}T${item.start_time}:00`,
                                            end: `${endDate}T${item.end_time}:00`,
                                            backgroundColor: isAvailableSlot ? 'rgba(0, 200, 0, 0.5)' : 'rgba(200, 0, 0, 0.5)',
                                            borderColor: '#000'
                                        };
                                    })}
                                    selectable={true}
                                    selectMirror={true}
                                    select={(selectInfo) => {
                                        const dayIndex = selectInfo.start.getDay();
                                        const dayKey = DAYS_OF_WEEK[dayIndex === 0 ? 6 : dayIndex - 1].key;
                                        let startTime = selectInfo.start.toTimeString().slice(0, 5);
                                        let endTime = selectInfo.end.toTimeString().slice(0, 5);
                                        if (selectInfo.end.getDate() > selectInfo.start.getDate()) {
                                            const endHours = selectInfo.end.getHours();
                                            const endMinutes = selectInfo.end.getMinutes();
                                            endTime = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
                                        }
                                        
                                        setSelectedSlot({
                                            day: dayKey,
                                            start_time: startTime,
                                            end_time: endTime
                                        });
                                        setShowDialog(true);
                                    }}
                                    eventClick={(clickInfo) => {
                                        const eventId = parseInt(clickInfo.event.id);
                                        const newAvailability = data.availability.filter((_: any, index: number) => index !== eventId);
                                        setData('availability', newAvailability);
                                        setOverlapError('');
                                    }}
                                    height="100%"
                                    slotMinTime={(() => {
                                        const selectedEmployee = employees?.find((emp: any) => emp.id.toString() === data.employee_id);
                                        return selectedEmployee?.shift?.start_time || '00:00:00';
                                    })()}
                                    slotMaxTime={(() => {
                                        const selectedEmployee = employees?.find((emp: any) => emp.id.toString() === data.employee_id);
                                        const shiftStartTime = selectedEmployee?.shift?.start_time;
                                        const shiftEndTime = selectedEmployee?.shift?.end_time;
                                        
                                        if (shiftStartTime && shiftEndTime && shiftEndTime < shiftStartTime) {
                                            const [hours, minutes] = shiftEndTime.split(':');
                                            const nextDayHours = parseInt(hours) + 24;
                                            return `${nextDayHours}:${minutes}:00`;
                                        }
                                        
                                        return shiftEndTime || '24:00:00';
                                    })()}
                                    nextDayThreshold="00:00:00"
                                    selectOverlap={false}
                                    allDaySlot={false}
                                    firstDay={1}
                                    nowIndicator={false}
                                />
                            </div>
                            <div className="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
                                <svg className="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                                </svg>
                                <span>{t('Note: Time slots without any marking will be considered as Unavailable by default. Click on existing slots to remove them.')}</span>
                            </div>
                        </>
                    ) : (
                        <div className="h-96 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
                            <p className="text-gray-500">{t('Please select an employee to set availability')}</p>
                        </div>
                    )}
                    <InputError message={errors.availability} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>

            <AlertDialog open={showDialog} onOpenChange={setShowDialog}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('Set Availability')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t('Choose whether this time slot should be available or unavailable.')}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={() => setShowDialog(false)}>
                            {t('Cancel')}
                        </AlertDialogCancel>
                        <AlertDialogAction 
                            onClick={() => {
                                if (selectedSlot) {
                                    handleSlotSelection(selectedSlot.day, selectedSlot.start_time, selectedSlot.end_time, 'available');
                                }
                                setShowDialog(false);
                            }}
                            className="bg-green-500 hover:bg-green-600"
                        >
                            {t('Available')}
                        </AlertDialogAction>
                        <AlertDialogAction 
                            onClick={() => {
                                if (selectedSlot) {
                                    handleSlotSelection(selectedSlot.day, selectedSlot.start_time, selectedSlot.end_time, 'unavailable');
                                }
                                setShowDialog(false);
                            }}
                            className="bg-red-500 hover:bg-red-600"
                        >
                            {t('Unavailable')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DialogContent>
    );
}