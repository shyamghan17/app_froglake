import { useTranslation } from 'react-i18next';
import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Clock, UserIcon, Download } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Employee, Shift } from '../Rotas/types';
import { formatDate, formatTime, getImagePath, formatCurrency } from '@/utils/helpers';
import { usePDFGenerator } from '../Rotas/usePDFGenerator';

interface ScheduleViewerProps {
    employees: Employee[];
    shifts: Shift[];
    scheduleData: any;
    startDate: string;
    endDate: string;
    leaveApplications?: any;
    showHeader?: boolean;
}

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
    is_published?: boolean;
}

export default function ScheduleViewer({
    employees,
    shifts,
    scheduleData,
    startDate,
    endDate,
    leaveApplications,
    showHeader = true
}: ScheduleViewerProps) {
    const { t } = useTranslation();
    const { settings, pageProps, auth, companyAllSetting } = usePage().props as any;

    const { generatePDF, calculateEmployeeHours, calculateEmployeeCost } = usePDFGenerator({
        employees,
        scheduleData,
        startDate,
        endDate,
        leaveApplications,
        onlyPublished: false
    });

    const handleDownloadPDF = () => generatePDF();

    // Expose download function to window for the shared schedule header
    useEffect(() => {
        (window as any).downloadSchedulePDF = handleDownloadPDF;
        return () => {
            delete (window as any).downloadSchedulePDF;
        };
    }, [handleDownloadPDF]);

    // Get rotas display settings
    const showEmployeeAvatars = companyAllSetting?.rotas_show_employee_avatars === '1' || companyAllSetting?.rotas_show_employee_avatars === 'true' || false;
    const hideEmployeeHours = companyAllSetting?.rotas_hide_employee_hours === '1' || companyAllSetting?.rotas_hide_employee_hours === 'true' || false;
    const showEmployeePrice = companyAllSetting?.rotas_show_employee_price === '1' || companyAllSetting?.rotas_show_employee_price === 'true' || false;

    // Get company settings for header and PDF
    const companyName = companyAllSetting?.titleText || 'Company Name';
    const logoDark = companyAllSetting?.logo_dark;
    const logoLight = companyAllSetting?.logo_light;
    const logoUrl = logoDark ? getImagePath(logoDark, pageProps) : (logoLight ? getImagePath(logoLight, pageProps) : '');
    const themeColor = companyAllSetting?.themeColor || 'blue';
    const customColor = companyAllSetting?.customColor || '#3b82f6';

    // Get actual color value
    const colorMap: Record<string, string> = {
        blue: '#3b82f6',
        green: '#10b981',
        purple: '#8b5cf6',
        orange: '#f97316',
        red: '#ef4444'
    };
    const primaryColor = themeColor === 'custom' ? customColor : (colorMap[themeColor] || '#3b82f6');

    // Convert hex color to RGB
    const hexToRgb = (hex: string) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : { r: 59, g: 130, b: 246 };
    };

    const brandColor = hexToRgb(primaryColor);

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

    const generateDayNames = () => {
        const allDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const weekStart = settings?.rotas_week_starts ?? 1;
        const dayNames = [];
        for (let i = 0; i < 7; i++) {
            dayNames.push(allDays[(weekStart + i) % 7]);
        }
        return dayNames;
    };

    const dayNames = generateDayNames();

    // Check if employee is on leave on a specific date
    const isEmployeeOnLeave = (employeeId: number, date: string) => {
        if (!leaveApplications || !leaveApplications[employeeId]) return null;

        return leaveApplications[employeeId].find((leave: any) => {
            const leaveStart = new Date(leave.start_date);
            const leaveEnd = new Date(leave.end_date);
            const checkDate = new Date(date);

            return checkDate >= leaveStart && checkDate <= leaveEnd;
        });
    };

    const getShiftsForCell = (employeeId: number, date: string) => {
        if (!scheduleData) return [];
        const employee = employees.find(emp => emp.id === employeeId);
        if (!employee) return [];
        const shifts = scheduleData[employee.user_id] || scheduleData[employeeId] || [];
        return shifts.filter((shift: ShiftData) => shift.date === date);
    };

    const calculateOverallTotal = () => {
        if (!scheduleData) return '0h';
        let totalMinutes = 0;
        let totalCost = 0;
        employees.forEach(employee => {
            const shifts = scheduleData[employee.user_id] || scheduleData[employee.id] || [];
            shifts.forEach((shift: ShiftData) => {
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
                    totalMinutes += shiftMinutes;
                    if (showEmployeePrice && employee?.rate_per_hour) {
                        totalCost += (shiftMinutes / 60) * employee.rate_per_hour;
                    }
                }
            });
        });
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        let result = '';
        if (!hideEmployeeHours) {
            result = minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
        }
        if (showEmployeePrice && totalCost > 0) {
            result += result ? ` | ${formatCurrency(totalCost, pageProps)}` : formatCurrency(totalCost, pageProps);
        }
        return result || '0';
    };

    const calculateDayTotal = (date: string) => {
        if (!scheduleData) return '0h';
        let totalMinutes = 0;
        let totalCost = 0;
        employees.forEach(employee => {
            const shifts = (scheduleData[employee.user_id] || scheduleData[employee.id] || []).filter((shift: ShiftData) => shift.date === date);
            shifts.forEach((shift: ShiftData) => {
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
                    totalMinutes += shiftMinutes;
                    if (showEmployeePrice && employee?.rate_per_hour) {
                        totalCost += (shiftMinutes / 60) * employee.rate_per_hour;
                    }
                }
            });
        });
        const hours = Math.floor(totalMinutes / 60);
        let result = '';
        if (!hideEmployeeHours) {
            result = `${hours}h`;
        }
        if (showEmployeePrice && totalCost > 0) {
            result += result ? ` | ${formatCurrency(totalCost, pageProps)}` : formatCurrency(totalCost, pageProps);
        }
        return result || '';
    };

    const renderShiftBlock = (shift: ShiftData) => {
        const getShiftColor = () => {
            switch (shift.type) {
                case 'shift':
                    return 'bg-primary/10 border-primary/20 text-primary';
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
                key={shift.id}
                className={`border rounded p-2 text-xs ${getShiftColor()}`}
            >
                <div className="font-medium">
                    {shift.type === 'shift' && shift.startTime && shift.endTime
                        ? `${formatTime(shift.startTime, pageProps)} - ${formatTime(shift.endTime, pageProps)}`
                        : shift.type === 'dayoff'
                            ? t('Day Off')
                            : t('Leave')
                    }
                </div>
                {shift.notes && (
                    <div className="text-muted-foreground mt-1 text-xs leading-tight break-words whitespace-normal" style={{ wordWrap: 'break-word', overflowWrap: 'break-word' }} title={shift.notes}>
                        {shift.notes}
                    </div>
                )}
            </div>
        );
    };

    if (weekDates.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('Schedule')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="text-center py-8 text-muted-foreground">
                        {t('No schedule data available')}
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <div className="flex flex-col h-full w-full overflow-hidden">
            {showHeader && (
                <div className="mb-6 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        {logoUrl && (
                            <img
                                src={logoUrl}
                                alt="Company Logo"
                                className="h-12 w-auto object-contain"
                            />
                        )}
                        <div>
                            <h1 className="text-2xl font-bold" style={{ color: primaryColor }}>{companyName}</h1>
                            <p className="text-sm text-muted-foreground">
                                {t('Weekly Schedule')} - {formatDate(startDate, pageProps)} to {formatDate(endDate, pageProps)}
                            </p>
                        </div>
                    </div>
                    <Button
                        id="schedule-viewer-download-btn"
                        onClick={handleDownloadPDF}
                        variant="outline"
                        className={showHeader ? '' : 'hidden'}
                        style={{ borderColor: primaryColor, color: primaryColor }}
                    >
                        <Download className="h-4 w-4 mr-2" />
                        {t('Download PDF')}
                    </Button>
                </div>
            )}

            <div className="w-full overflow-x-auto scrollbar-hide border rounded-lg border-gray-200 shadow-sm" id="schedule-table">
                <table className="w-full border-collapse min-w-[1000px]">
                    <thead>
                        <tr className="border-b bg-gray-50/50">
                            <th className="text-center p-4 min-w-[200px] font-semibold text-gray-700">{t('Employee')}</th>
                            {dayNames.map((day, index) => (
                                <th key={day} className="text-center p-4 min-w-[160px] border-l border-gray-100">
                                    <div className="font-bold text-sm tracking-tight" style={{ color: primaryColor }}>{t(day)}</div>
                                    <div className="text-[11px] font-medium text-muted-foreground mt-0.5 uppercase tracking-wider">
                                        {formatDate(weekDates[index], pageProps)}
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {employees.map((employee) => (
                            <tr key={employee.id} className="border-b border-gray-100 hover:bg-gray-50/30 transition-colors">
                                <td className="p-4 border-r border-gray-100 bg-white sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                    <div className="flex items-center gap-3">
                                        {showEmployeeAvatars && (
                                            <div className="w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: `${primaryColor}20` }}>
                                                {employee?.user?.avatar ? (
                                                    <img
                                                        src={getImagePath(employee?.user?.avatar, pageProps)}
                                                        alt="Avatar"
                                                        className="w-8 h-8 rounded-full object-cover"
                                                    />
                                                ) : (
                                                    <UserIcon className="w-8 h-8 text-gray-400" />
                                                )}
                                            </div>
                                        )}
                                        <div>
                                            <div className="font-medium">{employee.user.name}</div>
                                            {!hideEmployeeHours && (
                                                <div className="text-xs text-muted-foreground">
                                                    {calculateEmployeeHours(employee)}
                                                </div>
                                            )}
                                            {showEmployeePrice && employee.rate_per_hour && (
                                                <div className="text-xs text-green-600 font-medium">
                                                    {calculateEmployeeCost(employee)}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </td>
                                {weekDates.map((date) => {
                                    const leaveInfo = isEmployeeOnLeave(employee.id, date);
                                    const shifts = getShiftsForCell(employee.id, date);

                                    return (
                                        <td key={date} className={`p-3 border-l border-gray-100 min-h-[120px] align-top ${leaveInfo ? 'bg-red-50/30' : 'bg-white'}`}>
                                            <div className="space-y-2 min-h-[80px]">
                                                {(() => {
                                                    const leaveInfo = isEmployeeOnLeave(employee.id, date);
                                                    const shifts = getShiftsForCell(employee.id, date);

                                                    return (
                                                        <>
                                                            {leaveInfo && (
                                                                <div className="bg-red-100 border border-red-200 rounded p-2 text-xs mb-1">
                                                                    <div className="font-medium text-red-700">{t('On Leave')}</div>
                                                                    <div className="text-red-600 mt-1">{leaveInfo.leave_type}</div>
                                                                </div>
                                                            )}
                                                            {shifts.map((shift: ShiftData) => renderShiftBlock(shift))}
                                                            {shifts.length === 0 && !leaveInfo && (
                                                                <div className="flex items-center justify-center h-16 text-muted-foreground text-xs">
                                                                    {t('No shifts')}
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
                            <tr className="bg-gray-50/80 font-semibold border-t-2 border-gray-100">
                                <td className="p-4 border-r border-gray-100 bg-gray-50/80 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                    <div className="flex items-center gap-2 text-gray-700">
                                        <Clock className="h-4 w-4 text-primary" />
                                        <span className="text-sm">{t('Total')}</span>
                                    </div>
                                    <div className="text-sm font-bold text-primary mt-1">
                                        {calculateOverallTotal()}
                                    </div>
                                </td>
                                {weekDates.map((date) => (
                                    <td key={date} className="p-4 text-center border-l border-gray-100 text-sm font-bold text-gray-600">
                                        {calculateDayTotal(date)}
                                    </td>
                                ))}
                            </tr>
                        </tfoot>
                    )}
                </table>
            </div>
        </div>
    );
}