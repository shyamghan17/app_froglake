import { useTranslation } from 'react-i18next';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { TimePicker } from '@/components/ui/time-picker';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Shift } from './types';
import { useFormFields } from '@/hooks/useFormFields';

interface ShiftData {
    id?: string;
    employeeId: number;
    shiftId?: number;
    date: string;
    startTime: string;
    endTime: string;
    breakTime?: number;
    notes?: string;
    type: 'shift' | 'dayoff' | 'leave';
    sync_to_google_calendar?: boolean;
    sync_to_outlook_calendar?: boolean;
}

interface ShiftDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    shiftForm: ShiftData;
    setShiftForm: (form: ShiftData) => void;
    shifts: Shift[];
    onSave: () => void;
}

export default function ShiftDialog({
    open,
    onOpenChange,
    shiftForm,
    setShiftForm,
    shifts,
    onSave
}: ShiftDialogProps) {
    const { t } = useTranslation();
    const { pageProps } = usePage().props as any;
    
    // Calendar sync fields - use a proper form data structure
    const calendarData = {
        sync_to_google_calendar: shiftForm.sync_to_google_calendar || false,
        sync_to_outlook_calendar: shiftForm.sync_to_outlook_calendar || false
    };
    
    const setCalendarData = (key: string, value: any) => {
        setShiftForm({ ...shiftForm, [key]: value });
    };
    
    const calendarFields = useFormFields('getCalendarSyncFields', calendarData, setCalendarData, {}, 'create', t, 'Rotas');
    
    // Get existing shifts for this employee and date to check for overlaps
    const getExistingShifts = () => {
        const { scheduleData, employees } = usePage().props as any;
        const employee = employees?.find((emp: any) => emp.id === shiftForm.employeeId);
        const userId = employee?.user_id;
        if (!scheduleData || !userId || !scheduleData[userId]) return [];
        return scheduleData[userId].filter((shift: any) => 
            shift.date === shiftForm.date && shift.id !== shiftForm.id
        );
    };
    
    const existingShifts = getExistingShifts();
    
    // Check if time overlaps with existing shifts
    const hasTimeOverlap = (startTime: string, endTime: string) => {
        return existingShifts.some((shift: any) => {
            const shiftStart = shift.startTime;
            const shiftEnd = shift.endTime;
            const overlaps = (startTime < shiftEnd && endTime > shiftStart);
            return overlaps;
        });
    };
    
    const timeValidationError = shiftForm.startTime && shiftForm.endTime && hasTimeOverlap(shiftForm.startTime, shiftForm.endTime) 
        ? t('Time overlaps with existing shift') 
        : null;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-md">
                <DialogHeader>
                    <DialogTitle>
                        {shiftForm.id ? t('Edit Shift') : t('Add Shift')}
                    </DialogTitle>
                    <DialogDescription>
                        {shiftForm.id ? t('Modify the shift details below') : t('Create a new shift for the selected date')}
                    </DialogDescription>
                </DialogHeader>
                <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <Label>{t('Start Time')}</Label>
                            <TimePicker
                                value={shiftForm.startTime}
                                onChange={(value) => setShiftForm({ ...shiftForm, startTime: value })}
                                placeholder={t('Select start time')}
                            />
                        </div>
                        <div>
                            <Label>{t('End Time')}</Label>
                            <TimePicker
                                value={shiftForm.endTime}
                                onChange={(value) => setShiftForm({ ...shiftForm, endTime: value })}
                                placeholder={t('Select end time')}
                            />
                        </div>
                    </div>

                    <div>
                        <Label>{t('Break Time (minutes)')}</Label>
                        <Input
                            type="number"
                            min="0"
                            value={shiftForm.breakTime || 0}
                            onChange={(e) => setShiftForm({ ...shiftForm, breakTime: parseInt(e.target.value) || 0 })}
                            placeholder={t('Enter break time in minutes')}
                        />
                    </div>

                    <div>
                        <Label>{t('Notes')}</Label>
                        <Textarea
                            value={shiftForm.notes || ''}
                            onChange={(e) => setShiftForm({ ...shiftForm, notes: e.target.value })}
                            placeholder={t('Add notes for this shift')}
                            rows={2}
                        />
                    </div>
                    
                    {timeValidationError && (
                        <div className="text-sm text-red-600 bg-red-50 p-2 rounded">
                            {timeValidationError}
                        </div>
                    )}
                    
                    {/* Calendar Sync Fields */}
                    {calendarFields.map((field) => (
                        <div key={field.id}>
                            {field.component}
                        </div>
                    ))}
                    
                    {existingShifts.length > 0 && (
                        <div>
                            <Label>{t('Existing Shifts')}</Label>
                            <div className="flex flex-wrap gap-2 mt-2">
                                {existingShifts.map((shift: any, index: number) => (
                                    <span key={index} className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                        {shift.startTime} - {shift.endTime}
                                    </span>
                                ))}
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end gap-2">
                        <Button variant="outline" onClick={() => onOpenChange(false)}>
                            {t('Cancel')}
                        </Button>
                        <Button onClick={() => {
                            // Ensure calendar sync data is included in the form
                            const updatedForm = {
                                ...shiftForm,
                                sync_to_google_calendar: shiftForm.sync_to_google_calendar || false,
                                sync_to_outlook_calendar: shiftForm.sync_to_outlook_calendar || false
                            };
                            setShiftForm(updatedForm);
                            onSave();
                        }} disabled={!!timeValidationError}>
                            {shiftForm.id ? t('Update') : t('Add')}
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}