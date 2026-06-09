import { useState, useEffect, useMemo, useCallback } from 'react';
import { useTranslation } from 'react-i18next';
import { router, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Plus, Edit, Trash2, Clock, UserIcon } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Employee, Shift } from './types';
import { formatDate, formatTime, getImagePath, formatCurrency } from '@/utils/helpers';
import { toast } from 'sonner';
import ShiftDialog from './ShiftDialog';

// Components to handle helper functions without conditional hooks
const CurrencyDisplay = ({ amount, pageProps }: { amount: number; pageProps: any }) => {
    return <>{formatCurrency(amount, pageProps)}</>;
};

const TimeDisplay = ({ time, pageProps }: { time: string; pageProps: any }) => {
    return <>{formatTime(time, pageProps)}</>;
};

const DateDisplay = ({ date, pageProps }: { date: string; pageProps: any }) => {
    return <>{formatDate(date, pageProps)}</>;
};



interface ScheduleBuilderProps {
    employees: Employee[];
    shifts: Shift[];
    onScheduleChange: () => void;
    startDate: string;
    endDate: string;
    currentWeek?: number;
    leaveApplications?: any;
    holidays?: any[];
}

interface ShiftData {
    id?: string;
    userId: number;
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

export default function ScheduleBuilder({
    employees,
    shifts,
    onScheduleChange,
    startDate,
    endDate,
    currentWeek = 0,
    leaveApplications,
    holidays = []
}: ScheduleBuilderProps) {
    const { t } = useTranslation();
    const { settings, auth, pageProps, companyAllSetting } = usePage().props as any;

    // Get rotas display settings
    const showEmployeeAvatars = companyAllSetting?.rotas_show_employee_avatars === '1' || companyAllSetting?.rotas_show_employee_avatars === 'true' || false;
    const hideEmployeeHours = companyAllSetting?.rotas_hide_employee_hours === '1' || companyAllSetting?.rotas_hide_employee_hours === 'true' || false;
    const showEmployeePrice = companyAllSetting?.rotas_show_employee_price === '1' || companyAllSetting?.rotas_show_employee_price === 'true' || false;

    // Get global work schedule settings
    const globalWorkSchedule = {
        sunday: companyAllSetting?.rotas_work_schedule_sunday === '1' || companyAllSetting?.rotas_work_schedule_sunday === 'true',
        monday: companyAllSetting?.rotas_work_schedule_monday === '1' || companyAllSetting?.rotas_work_schedule_monday === 'true',
        tuesday: companyAllSetting?.rotas_work_schedule_tuesday === '1' || companyAllSetting?.rotas_work_schedule_tuesday === 'true',
        wednesday: companyAllSetting?.rotas_work_schedule_wednesday === '1' || companyAllSetting?.rotas_work_schedule_wednesday === 'true',
        thursday: companyAllSetting?.rotas_work_schedule_thursday === '1' || companyAllSetting?.rotas_work_schedule_thursday === 'true',
        friday: companyAllSetting?.rotas_work_schedule_friday === '1' || companyAllSetting?.rotas_work_schedule_friday === 'true',
        saturday: companyAllSetting?.rotas_work_schedule_saturday === '1' || companyAllSetting?.rotas_work_schedule_saturday === 'true',
    };
    const [selectedCell, setSelectedCell] = useState<{ employeeId: number, date: string } | null>(null);
    const [shiftDialog, setShiftDialog] = useState(false);
    const [shiftForm, setShiftForm] = useState<ShiftData>({
        userId: 0,
        employeeId: 0,
        date: '',
        startTime: '',
        endTime: '',
        breakTime: 0,
        type: 'shift',
        sync_to_google_calendar: false,
        sync_to_outlook_calendar: false
    });
    const [showAllShifts, setShowAllShifts] = useState<{ [key: string]: boolean }>({});

    // Use the provided start and end dates to generate week dates
    const getWeekDates = () => {
        if (!startDate || !endDate) return [];

        const start = new Date(startDate);
        const end = new Date(endDate);

        if (isNaN(start.getTime()) || isNaN(end.getTime())) return [];

        const dates = [];
        const current = new Date(start);

        while (current <= end) {
            dates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
        }

        return dates;
    };

    const weekDates = getWeekDates();

    // Force re-render when currentWeek changes
    useEffect(() => {
        // This will trigger a re-render when currentWeek prop changes
    }, [currentWeek, startDate, endDate, employees]);
    // Generate day names based on week start setting
    const generateDayNames = () => {
        const allDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const weekStart = settings?.rotas_week_starts ?? 1; // Default Monday
        const dayNames = [];
        for (let i = 0; i < 7; i++) {
            dayNames.push(allDays[(weekStart + i) % 7]);
        }
        return dayNames;
    };

    const dayNames = generateDayNames();

    // Don't render if no valid dates
    if (weekDates.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('Weekly Schedule Builder')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="text-center py-8 text-muted-foreground">
                        {t('Please select start and end dates to build the schedule')}
                    </div>
                </CardContent>
            </Card>
        );
    }

    // Get shifts for a specific employee and date from employee.weekSchedule
    const getShiftsForCell = (employee: Employee, date: string) => {
        const daySchedule = employee.weekSchedule?.find(day => day.date === date);
        return daySchedule?.shifts || [];
    };

    // Memoized calculation functions for better performance
    const calculateEmployeeHours = useCallback((employee: Employee) => {
        if (!employee.weekSchedule) return '0h';

        let totalMinutes = 0;
        employee.weekSchedule.forEach((day) => {
            day.shifts?.forEach((shift: any) => {
                if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                    const start = new Date(`2000-01-01T${shift.startTime}:00`);
                    let end = new Date(`2000-01-01T${shift.endTime}:00`);
                    if (end <= start) {
                        end = new Date(`2000-01-02T${shift.endTime}:00`);
                    }
                    const diffMs = end.getTime() - start.getTime();
                    let shiftMinutes = diffMs / (1000 * 60);

                    if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                        shiftMinutes -= shift.breakTime;
                    }

                    totalMinutes += Math.max(0, shiftMinutes);
                }
            });
        });

        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.round(totalMinutes % 60);
        return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
    }, [companyAllSetting?.rotas_break_type]);

    // Calculate employee cost based on hours worked
    const calculateEmployeeCost = useCallback((employee: Employee) => {
        if (!employee.weekSchedule || !employee.rate_per_hour) return 0;

        let totalMinutes = 0;
        employee.weekSchedule.forEach((day) => {
            day.shifts?.forEach((shift: any) => {
                if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                    const start = new Date(`2000-01-01T${shift.startTime}:00`);
                    let end = new Date(`2000-01-01T${shift.endTime}:00`);
                    if (end <= start) {
                        end = new Date(`2000-01-02T${shift.endTime}:00`);
                    }
                    const diffMs = end.getTime() - start.getTime();
                    let shiftMinutes = diffMs / (1000 * 60);

                    if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                        shiftMinutes -= shift.breakTime;
                    }

                    totalMinutes += Math.max(0, shiftMinutes);
                }
            });
        });

        const totalHours = totalMinutes / 60;
        return totalHours * employee.rate_per_hour;
    }, [companyAllSetting?.rotas_break_type]);

    // Check for time overlap between shifts
    const hasTimeOverlap = (employee: Employee, date: string, startTime: string, endTime: string, excludeShiftId?: string) => {
        const daySchedule = employee.weekSchedule?.find(day => day.date === date);
        if (!daySchedule?.shifts) return false;

        const existingShifts = daySchedule.shifts.filter((shift: any) =>
            shift.type === 'shift' && shift.id !== excludeShiftId
        );

        const newStart = new Date(`2000-01-01T${startTime}:00`);
        const newEnd = new Date(`2000-01-01T${endTime}:00`);

        return existingShifts.some((shift: any) => {
            if (!shift.startTime || !shift.endTime) return false;

            const existingStart = new Date(`2000-01-01T${shift.startTime}:00`);
            const existingEnd = new Date(`2000-01-01T${shift.endTime}:00`);

            return (newStart < existingEnd && newEnd > existingStart);
        });
    };

    // Calculate overall hours
    const overallHours = useMemo(() => {
        let totalMinutes = 0;

        employees.forEach((employee) => {
            employee.weekSchedule?.forEach((day) => {
                day.shifts?.forEach((shift: any) => {
                    if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                        const start = new Date(`2000-01-01T${shift.startTime}:00`);
                        let end = new Date(`2000-01-01T${shift.endTime}:00`);
                        if (end <= start) {
                            end = new Date(`2000-01-02T${shift.endTime}:00`);
                        }
                        const diffMs = end.getTime() - start.getTime();
                        let shiftMinutes = diffMs / (1000 * 60);

                        if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                            shiftMinutes -= shift.breakTime;
                        }

                        totalMinutes += Math.max(0, shiftMinutes);
                    }
                });
            });
        });

        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.round(totalMinutes % 60);
        return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
    }, [employees, companyAllSetting?.rotas_break_type]);

    // Calculate overall cost
    const overallCost = useMemo(() => {
        let totalCost = 0;

        employees.forEach((employee) => {
            if (!employee.rate_per_hour) return;

            employee.weekSchedule?.forEach((day) => {
                day.shifts?.forEach((shift: any) => {
                    if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                        const start = new Date(`2000-01-01T${shift.startTime}:00`);
                        let end = new Date(`2000-01-01T${shift.endTime}:00`);
                        if (end <= start) {
                            end = new Date(`2000-01-02T${shift.endTime}:00`);
                        }
                        const diffMs = end.getTime() - start.getTime();
                        let shiftMinutes = diffMs / (1000 * 60);

                        if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                            shiftMinutes -= shift.breakTime;
                        }

                        totalCost += (Math.max(0, shiftMinutes) / 60) * employee.rate_per_hour;
                    }
                });
            });
        });

        return totalCost;
    }, [employees, companyAllSetting?.rotas_break_type]);

    // Calculate day totals
    const getDayHours = useCallback((date: string) => {
        let totalMinutes = 0;

        employees.forEach((employee) => {
            const daySchedule = employee.weekSchedule?.find(day => day.date === date);
            daySchedule?.shifts?.forEach((shift: any) => {
                if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                    const start = new Date(`2000-01-01T${shift.startTime}:00`);
                    let end = new Date(`2000-01-01T${shift.endTime}:00`);
                    if (end <= start) {
                        end = new Date(`2000-01-02T${shift.endTime}:00`);
                    }
                    const diffMs = end.getTime() - start.getTime();
                    let shiftMinutes = diffMs / (1000 * 60);

                    if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                        shiftMinutes -= shift.breakTime;
                    }

                    totalMinutes += Math.max(0, shiftMinutes);
                }
            });
        });

        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.round(totalMinutes % 60);
        return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
    }, [employees, companyAllSetting?.rotas_break_type]);

    const getDayCost = useCallback((date: string) => {
        let totalCost = 0;

        employees.forEach((employee) => {
            if (!employee.rate_per_hour) return;

            const daySchedule = employee.weekSchedule?.find(day => day.date === date);
            daySchedule?.shifts?.forEach((shift: any) => {
                if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                    const start = new Date(`2000-01-01T${shift.startTime}:00`);
                    let end = new Date(`2000-01-01T${shift.endTime}:00`);
                    if (end <= start) {
                        end = new Date(`2000-01-02T${shift.endTime}:00`);
                    }
                    const diffMs = end.getTime() - start.getTime();
                    let shiftMinutes = diffMs / (1000 * 60);

                    if (companyAllSetting?.rotas_break_type !== 'paid' && shift.breakTime) {
                        shiftMinutes -= shift.breakTime;
                    }

                    totalCost += (Math.max(0, shiftMinutes) / 60) * employee.rate_per_hour;
                }
            });
        });

        return totalCost;
    }, [employees, companyAllSetting?.rotas_break_type]);

    // Check if date is a holiday
    const isHoliday = (date: string) => {
        if (!holidays || holidays.length === 0) return null;

        return holidays.find((holiday: any) => {
            const holidayStart = new Date(holiday.start_date);
            const holidayEnd = new Date(holiday.end_date);
            const checkDate = new Date(date);

            return checkDate >= holidayStart && checkDate <= holidayEnd;
        });
    };

    // Check if employee is on leave on a specific date
    const isEmployeeOnLeave = (employeeId: number, userId: number, date: string) => {
        if (!leaveApplications || !leaveApplications[employeeId]) return null;

        return leaveApplications[employeeId].find((leave: any) => {
            const leaveStart = new Date(leave.start_date);
            const leaveEnd = new Date(leave.end_date);
            const checkDate = new Date(date);

            return checkDate >= leaveStart && checkDate <= leaveEnd;
        });
    };

    // Check if employee is available on a specific date
    const isEmployeeAvailable = (employee: Employee, date: string) => {
        const daySchedule = employee.weekSchedule?.find(day => day.date === date);
        if (!daySchedule) return false;

        // Check if there's availability data and it's not empty
        if (!daySchedule.availability || daySchedule.availability.length === 0) {
            return false;
        }

        // Check if any availability slot is of type 'available'
        return daySchedule.availability.some((slot: any) => slot.type === 'available');
    };
    const canEmployeeWork = (employee: Employee, date: string) => {
        const holidayInfo = isHoliday(date);
        if (holidayInfo) return false;

        const leaveInfo = isEmployeeOnLeave(employee.id, employee.user_id, date);
        if (leaveInfo) return false;

        const daySchedule = employee.weekSchedule?.find(day => day.date === date);
        return daySchedule?.isWorkingDay || false;
    };

    // Handle cell click
    const handleCellClick = (employee: Employee, date: string) => {
        const holidayInfo = isHoliday(date);
        const leaveInfo = isEmployeeOnLeave(employee.id, employee.user_id, date);

        if (holidayInfo || leaveInfo) return;

        setSelectedCell({ employeeId: employee.id, date });
        setShiftForm({
            userId: employee.user_id,
            employeeId: employee.id,
            date,
            startTime: '',
            endTime: '',
            breakTime: 0,
            type: 'shift',
            sync_to_google_calendar: false,
            sync_to_outlook_calendar: false
        });
        setShiftDialog(true);
    };

    // Handle shift save
    const handleShiftSave = () => {
        const employee = employees.find(emp => emp.id === shiftForm.employeeId);
        if (!employee) return;

        if (shiftForm.type === 'shift' && hasTimeOverlap(
            employee,
            shiftForm.date,
            shiftForm.startTime,
            shiftForm.endTime,
            shiftForm.id
        )) {
            toast.error(t('Time slot overlaps with existing shift. Please choose different times.'));
            return;
        }

        const newShift: ShiftData = {
            id: shiftForm.id || `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
            ...shiftForm
        };

        if (shiftForm.id) {
            router.put(route('rotas.schedule.update', shiftForm.id), {
                user_id: shiftForm.userId,
                employee_id: shiftForm.employeeId,
                shift_data: {
                    date: newShift.date,
                    startTime: newShift.startTime,
                    endTime: newShift.endTime,
                    breakTime: newShift.breakTime || 0,
                    type: newShift.type,
                    notes: newShift.notes || '',
                    shiftId: newShift.shiftId,
                    sync_to_outlook_calendar: newShift.sync_to_outlook_calendar || false,
                    sync_to_google_calendar: newShift.sync_to_google_calendar || false
                }
            }, {
                preserveState: true
            });
        } else {
            router.post(route('rotas.schedule.save'), {
                user_id: shiftForm.userId,
                employee_id: shiftForm.employeeId,
                shift_data: {
                    date: newShift.date,
                    startTime: newShift.startTime,
                    endTime: newShift.endTime,
                    breakTime: newShift.breakTime || 0,
                    type: newShift.type,
                    notes: newShift.notes || '',
                    shiftId: newShift.shiftId,
                    sync_to_outlook_calendar: newShift.sync_to_outlook_calendar || false,
                    sync_to_google_calendar: newShift.sync_to_google_calendar || false
                }
            }, {
                preserveState: true
            });
        }

        onScheduleChange();
        setShiftDialog(false);
    };

    // Handle shift delete
    const handleShiftDelete = (shiftId: string) => {
        router.delete(route('rotas.schedule.delete', shiftId), {
            preserveState: true
        });
        onScheduleChange();
    };

    // Handle shift edit
    const handleShiftEdit = (shift: ShiftData) => {
        setShiftForm(shift);
        setShiftDialog(true);
    };

    // Render shift block
    const renderShiftBlock = (shift: any, employee: Employee) => {
        const getShiftColor = () => {
            switch (shift.type) {
                case 'shift':
                    return 'bg-primary/10 border-primary/20 text-primary hover:bg-primary/20';
                case 'dayoff':
                    return 'bg-orange-50 border-orange-200 text-orange-700';
                case 'leave':
                    return 'bg-red-50 border-red-200 text-red-700';
                default:
                    return 'bg-gray-50 border-gray-200 text-gray-700';
            }
        };

        return (
            <div
                key={shift.id ? `shift-${shift.id}` : `shift-${employee.user_id}-${shift.date}-${shift.startTime}`}
                className={`border rounded p-2 text-xs cursor-pointer transition-colors mb-1 ${getShiftColor()}`}
                onClick={(e) => {
                    e.stopPropagation();
                }}
            >
                <div className="flex items-center justify-between">
                    <div className="font-medium">
                        {shift.type === 'shift' && shift.startTime && shift.endTime && (
                            <><TimeDisplay time={shift.startTime} pageProps={pageProps} /> - <TimeDisplay time={shift.endTime} pageProps={pageProps} /></>
                        )}
                    </div>
                    {shift.type === 'shift' && (
                        <div className="flex gap-1">
                            <TooltipProvider>
                                {auth.user?.permissions?.includes('edit-rotas') && (
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                className="h-4 w-4 p-0 text-blue-600 hover:text-blue-700 hover:bg-transparent"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    handleShiftEdit(shift);
                                                }}
                                            >
                                                <Edit className="h-3 w-3" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Edit Shift')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                                {auth.user?.permissions?.includes('delete-rotas') && (
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                className="h-4 w-4 p-0 text-red-600 hover:text-red-600 hover:bg-transparent"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    handleShiftDelete(shift.id!);
                                                }}
                                            >
                                                <Trash2 className="h-3 w-3" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Delete Shift')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </TooltipProvider>
                        </div>
                    )}
                </div>
                {shift.notes && (
                    <div className="text-muted-foreground mt-1 text-xs leading-tight" style={{ wordWrap: 'break-word', overflowWrap: 'break-word' }} title={shift.notes}>
                        {shift.notes}
                    </div>
                )}
            </div>
        );
    };
    return (
        <>
            <div className="overflow-x-auto">
                <table className="w-full border-collapse">
                    <thead>
                        <tr className="border-b">
                            <th className="text-center p-3 min-w-[200px]">{t('Employee')}</th>
                            {dayNames.map((day, index) => (
                                <th key={`${day}-tabel-header`} className="text-center p-3 min-w-[150px] border-l">
                                    <div className="text-primary font-semibold">{t(day)}</div>
                                    <div className="text-xs text-muted-foreground">
                                        <DateDisplay date={weekDates[index]} pageProps={pageProps} />
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {employees.map((employee) => (
                            <tr key={`${employee.id}-${employee.user_id}-employee-body`} className="border-b hover:bg-gray-50">
                                <td className="p-3 border-r">
                                    <div className="flex items-center gap-3">
                                        {showEmployeeAvatars && (
                                            <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                                <span className="text-xs font-medium text-primary">
                                                    {employee?.user?.avatar ? (
                                                        <img
                                                            src={getImagePath(employee?.user?.avatar, pageProps)}
                                                            alt="Avatar"
                                                            className="w-8 h-8 rounded-full object-cover"
                                                        />
                                                    ) : (
                                                        <UserIcon className="w-8 h-8 text-gray-400" />
                                                    )}
                                                </span>
                                            </div>
                                        )}
                                        <div>
                                            <div className="font-medium">{employee.user?.name || t('No User Assigned')}</div>
                                            {!hideEmployeeHours && (
                                                <div className="text-xs text-muted-foreground">
                                                    {calculateEmployeeHours(employee)}
                                                </div>
                                            )}
                                            {showEmployeePrice && employee.rate_per_hour && (
                                                <div className="text-xs text-green-600 font-medium">
                                                    <CurrencyDisplay amount={calculateEmployeeCost(employee)} pageProps={pageProps} />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </td>
                                {weekDates.map((date, dayIndex) => {
                                    const holidayInfo = isHoliday(date);
                                    const leaveInfo = isEmployeeOnLeave(employee.id, employee.user_id, date);
                                    const canWork = auth.user?.permissions?.includes('create-rotas') && canEmployeeWork(employee, date);
                                    const isAvailable = isEmployeeAvailable(employee, date);

                                    return (
                                        <td
                                            key={`${date}-${dayIndex}-date`}
                                            className={`p-2 border-l min-h-[160px] align-top ${holidayInfo ? 'bg-blue-50' :
                                                leaveInfo ? 'bg-red-50' :
                                                    canWork ? 'cursor-pointer hover:bg-gray-50' : 'bg-gray-100 cursor-pointer hover:bg-gray-50'
                                                }`}
                                            onClick={(e) => {
                                                if (e.target === e.currentTarget && !leaveInfo && !holidayInfo && canWork && isAvailable) {
                                                    handleCellClick(employee, date);
                                                }
                                            }}
                                        >
                                            <div className="space-y-2 flex flex-col min-h-[140px]">
                                                {/* Holiday - Show first */}
                                                {holidayInfo && (
                                                    <div className="bg-blue-100 border border-blue-200 rounded p-2 text-xs flex items-center justify-center w-full flex-1 min-h-[40px] mt-auto grid">
                                                        <div className="font-medium text-blue-700">{t('Holiday')}</div>
                                                        <div className="text-blue-600 mt-1">{holidayInfo.name}</div>
                                                    </div>
                                                )}
                                                {/* Leave - Show second */}
                                                {leaveInfo && (
                                                    <div className="bg-red-100 border border-red-200 rounded p-2 text-xs flex items-center justify-center w-full flex-1 min-h-[40px] mt-auto grid">
                                                        <div className="font-medium text-red-700">{t('On Leave')}</div>
                                                        <div className="text-red-600 mt-1">{leaveInfo.leave_type}</div>
                                                    </div>
                                                )}
                                                {/* Day Off - Show third - only when employee is scheduled to not work */}
                                                {!holidayInfo && !leaveInfo && !canEmployeeWork(employee, date) && (
                                                    <div className="bg-orange-50 border border-orange-200 text-orange-700 rounded p-2 text-xs flex items-center justify-center w-full flex-1 min-h-[40px] mt-auto">
                                                        <div className="font-medium">
                                                            {t('Day Off')}
                                                        </div>
                                                    </div>
                                                )}
                                                {/* Shifts - Show last */}
                                                {(() => {
                                                    const shifts = getShiftsForCell(employee, date);
                                                    const cellKey = `${employee.id}-${employee.user_id}-${date}`;
                                                    const showAll = showAllShifts[cellKey] || false;
                                                    const visibleShifts = showAll ? shifts : shifts.slice(0, 1);
                                                    const remainingCount = shifts.length - 1;

                                                    return (
                                                        <>
                                                            {visibleShifts.map((shift: any, index: number) =>
                                                                renderShiftBlock(shift, employee)
                                                            )}
                                                            {shifts.length > 1 && !showAll && (
                                                                <button
                                                                    onClick={(e) => {
                                                                        e.stopPropagation();
                                                                        setShowAllShifts(prev => ({ ...prev, [cellKey]: true }));
                                                                    }}
                                                                    className="w-full text-xs text-blue-600 hover:text-blue-700 py-1 border border-blue-200 rounded bg-blue-50 hover:bg-blue-100"
                                                                >
                                                                    +{remainingCount} {t('more')}
                                                                </button>
                                                            )}
                                                            {shifts.length > 1 && showAll && (
                                                                <button
                                                                    onClick={(e) => {
                                                                        e.stopPropagation();
                                                                        setShowAllShifts(prev => ({ ...prev, [cellKey]: false }));
                                                                    }}
                                                                    className="w-full text-xs text-gray-600 hover:text-gray-700 py-1 border border-gray-200 rounded bg-gray-50 hover:bg-gray-100"
                                                                >
                                                                    {t('Show less')}
                                                                </button>
                                                            )}
                                                            {canWork && isAvailable && (
                                                                <Tooltip>
                                                                    <TooltipTrigger asChild>
                                                                        <div
                                                                            className={`flex items-center justify-center w-full text-muted-foreground border-2 border-dashed border-gray-300 rounded hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer ${showAll ? 'h-10 mt-2' : 'flex-1 min-h-[40px] mt-auto'}`}
                                                                            onClick={(e) => {
                                                                                e.stopPropagation();
                                                                                handleCellClick(employee, date);
                                                                            }}
                                                                        >
                                                                            <Plus className="h-5 w-5" />
                                                                        </div>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Add Shift')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                            )}

                                                            {!isAvailable && !holidayInfo && !leaveInfo && canEmployeeWork(employee, date) && (
                                                                <div className="flex items-center justify-center w-full bg-gray-100 border border-gray-300 text-gray-600 rounded p-2 text-xs flex-1 min-h-[40px] mt-auto">
                                                                    <div className="font-medium">{t('Unavailable')}</div>
                                                                </div>
                                                            )}

                                                        </>
                                                    );
                                                })()}
                                            </div>
                                        </td>
                                    );
                                })}
                            </tr>
                        ))}
                    </tbody>
                    {(!hideEmployeeHours || showEmployeePrice) && (
                        <tfoot>
                            <tr className="bg-gray-100 font-medium">
                                <td className="p-3 border-r">
                                    <div className="flex items-center gap-2">
                                        <Clock className="h-4 w-4" />
                                        <span>{t('Total')}</span>
                                    </div>
                                    <div className="text-sm font-semibold text-primary mt-1">
                                        {!hideEmployeeHours && overallHours}
                                        {!hideEmployeeHours && showEmployeePrice && overallCost > 0 && ' | '}
                                        {showEmployeePrice && overallCost > 0 && <CurrencyDisplay amount={overallCost} pageProps={pageProps} />}
                                    </div>
                                </td>
                                {weekDates.map((date) => (
                                    <td key={`${date}-footer`} className="p-3 text-center border-l text-sm">
                                        <div className="whitespace-nowrap">
                                            {!hideEmployeeHours && getDayHours(date)}
                                            {!hideEmployeeHours && showEmployeePrice && getDayCost(date) > 0 && ' | '}
                                            {showEmployeePrice && getDayCost(date) > 0 && <CurrencyDisplay amount={getDayCost(date)} pageProps={pageProps} />}
                                        </div>
                                    </td>
                                ))}
                            </tr>
                        </tfoot>
                    )}
                </table>
            </div>

            {/* Shift Dialog */}
            <ShiftDialog
                open={shiftDialog}
                onOpenChange={setShiftDialog}
                shiftForm={shiftForm}
                setShiftForm={setShiftForm}
                shifts={shifts}
                onSave={handleShiftSave}
            />
        </>
    );
}