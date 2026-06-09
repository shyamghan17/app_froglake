import { usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { formatDate, formatTime, getImagePath, formatCurrency } from '@/utils/helpers';
import { Employee } from './types';

interface UsePDFGeneratorProps {
    employees: Employee[];
    scheduleData?: any;
    startDate: string;
    endDate: string;
    leaveApplications?: any;
    onlyPublished?: boolean;
}

export const usePDFGenerator = ({
    employees,
    scheduleData,
    startDate,
    endDate,
    leaveApplications,
    onlyPublished = false
}: UsePDFGeneratorProps) => {
    const { t } = useTranslation();
    const { settings, pageProps, auth, companyAllSetting } = usePage().props as any;

    const hideEmployeeHours = companyAllSetting?.rotas_hide_employee_hours === '1' || companyAllSetting?.rotas_hide_employee_hours === 'true' || false;
    const showEmployeePrice = companyAllSetting?.rotas_show_employee_price === '1' || companyAllSetting?.rotas_show_employee_price === 'true' || false;
    const companyName = companyAllSetting?.titleText || 'Company Name';
    const logoDark = companyAllSetting?.logo_dark;
    const logoLight = companyAllSetting?.logo_light;
    const logoUrl = logoDark ? getImagePath(logoDark, pageProps) : (logoLight ? getImagePath(logoLight, pageProps) : '');
    const themeColor = companyAllSetting?.themeColor || 'blue';
    const customColor = companyAllSetting?.customColor || '#3b82f6';

    const colorMap: Record<string, string> = {
        blue: '#3b82f6',
        green: '#10b981',
        purple: '#8b5cf6',
        orange: '#f97316',
        red: '#ef4444'
    };
    const primaryColor = themeColor === 'custom' ? customColor : (colorMap[themeColor] || '#3b82f6');

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
        const start = new Date(startDate);
        const end = new Date(endDate);
        const dates = [];
        const current = new Date(start);
        while (current <= end) {
            dates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
        }
        return dates;
    };

    const generateDayNames = () => {
        const allDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const weekStart = settings?.rotas_week_starts ?? 1;
        const dayNames = [];
        for (let i = 0; i < 7; i++) {
            dayNames.push(allDays[(weekStart + i) % 7]);
        }
        return dayNames;
    };

    const isEmployeeOnLeave = (employeeId: number, date: string) => {
        if (!leaveApplications || !leaveApplications[employeeId]) return null;
        return leaveApplications[employeeId].find((leave: any) => {
            const leaveStart = new Date(leave.start_date);
            const leaveEnd = new Date(leave.end_date);
            const checkDate = new Date(date);
            return checkDate >= leaveStart && checkDate <= leaveEnd;
        });
    };

    const getEmployeeScheduleData = (employee: Employee) => {
        if (!scheduleData) {
            if (employee.weekSchedule) {
                const shifts: any[] = [];
                employee.weekSchedule.forEach((day: any) => {
                    if (day.shifts) {
                        day.shifts.forEach((shift: any) => {
                            const isPublished = shift.isPublished ?? shift.is_published;
                            if (!onlyPublished || isPublished) {
                                shifts.push({ ...shift, date: day.date });
                            }
                        });
                    }
                });
                return shifts;
            }
            return [];
        }
        // Get shifts from scheduleData
        const shifts = scheduleData[employee.user_id] || scheduleData[employee.id] || [];
        // Filter by published status if onlyPublished flag is set
        if (onlyPublished) {
            return shifts.filter((shift: any) => shift.isPublished ?? shift.is_published);
        }
        return shifts;
    };

    const calculateEmployeeHours = (employee: Employee) => {
        const shifts = getEmployeeScheduleData(employee);
        let totalMinutes = 0;
        shifts.forEach((shift: any) => {
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
            }
        });
        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.round(totalMinutes % 60);
        return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
    };

    const calculateEmployeeCost = (employee: Employee) => {
        if (!employee.rate_per_hour) return formatCurrency(0, pageProps);
        const shifts = getEmployeeScheduleData(employee);
        let totalMinutes = 0;
        shifts.forEach((shift: any) => {
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
            }
        });
        const totalHours = totalMinutes / 60;
        const totalCost = totalHours * employee.rate_per_hour;
        return formatCurrency(totalCost, pageProps);
    };

    const calculateOverallTotal = (filteredEmployees: Employee[]) => {
        let totalMinutes = 0;
        let totalCost = 0;
        filteredEmployees.forEach(employee => {
            const shifts = getEmployeeScheduleData(employee);
            shifts.forEach((shift: any) => {
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

    const calculateDayTotal = (date: string, filteredEmployees: Employee[]) => {
        let totalMinutes = 0;
        let totalCost = 0;
        filteredEmployees.forEach(employee => {
            const shifts = getEmployeeScheduleData(employee).filter((shift: any) => shift.date === date);
            shifts.forEach((shift: any) => {
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

    const generatePDF = async (filteredEmployees?: Employee[], title?: string) => {
        try {
            const jsPDF = (await import('jspdf')).jsPDF;
            const autoTable = (await import('jspdf-autotable')).default;
            const doc = new jsPDF('landscape', 'mm', 'a3');
            const pageWidth = doc.internal.pageSize.getWidth();
            const employeesToExport = filteredEmployees || employees;

            if (logoUrl) {
                try {
                    await new Promise((resolve) => {
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = () => {
                            try {
                                doc.addImage(img, 'PNG', 20, 15, 30, 20);
                            } catch (e) {
                                console.log('Logo rendering failed:', e);
                            }
                            resolve(true);
                        };
                        img.onerror = () => resolve(true);
                        img.src = logoUrl;
                        setTimeout(() => resolve(true), 2000);
                    });
                } catch (e) {
                    console.log('Logo loading failed:', e);
                }
            }

            doc.setFontSize(20);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(brandColor.r, brandColor.g, brandColor.b);
            doc.text(companyName, logoUrl ? 60 : 20, 25);

            doc.setFontSize(16);
            doc.setFont('helvetica', 'normal');
            doc.text(title || t('Weekly Schedule'), logoUrl ? 60 : 20, 35);

            doc.setFontSize(12);
            doc.setTextColor(100, 100, 100);
            doc.text(`${formatDate(startDate, pageProps)} - ${formatDate(endDate, pageProps)}`, logoUrl ? 60 : 20, 42);

            doc.setFontSize(10);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, pageWidth - 60, 25);
            doc.setTextColor(0, 0, 0);

            const weekDates = getWeekDates();
            const dayNames = generateDayNames();

            const headers = [{
                content: t('Employee'),
                styles: { fontStyle: 'bold', fillColor: [brandColor.r, brandColor.g, brandColor.b], textColor: 255, valign: 'middle' } as any
            }];

            dayNames.forEach((day, index) => {
                headers.push({
                    content: `${t(day)}\n${formatDate(weekDates[index], pageProps)}`,
                    styles: {
                        fontStyle: 'bold',
                        fillColor: [Math.max(brandColor.r - 20, 0), Math.max(brandColor.g - 20, 0), Math.max(brandColor.b - 20, 0)],
                        textColor: 255,
                        halign: 'center',
                        valign: 'middle'
                    } as any
                });
            });

            const rows = employeesToExport.map((employee, empIndex) => {
                let employeeContent = employee.user?.name || t('No User Assigned');
                if (!hideEmployeeHours) employeeContent += `\n${calculateEmployeeHours(employee)}`;
                if (showEmployeePrice && employee.rate_per_hour) employeeContent += `\n${calculateEmployeeCost(employee)}`;

                const row = [{
                    content: employeeContent,
                    styles: {
                        fontStyle: 'bold',
                        fillColor: empIndex % 2 === 0 ? [248, 249, 250] : [255, 255, 255],
                        valign: 'middle'
                    } as any
                }];

                weekDates.forEach(date => {
                    const shifts = getEmployeeScheduleData(employee).filter((shift: any) => shift.date === date);
                    const leaveInfo = isEmployeeOnLeave(employee.id, date);
                    let cellContent = '';
                    let cellColor = [255, 255, 255];

                    if (leaveInfo) {
                        cellColor = [255, 235, 238];
                        cellContent = `${t('On Leave')}\n(${leaveInfo.leave_type})`;
                    }

                    if (shifts.length > 0) {
                        const shiftsText = shifts.map((shift: any) => {
                            let text = "";
                            if (shift.type === 'shift' && shift.startTime && shift.endTime) {
                                cellColor = [232, 245, 233];
                                text = `${formatTime(shift.startTime, pageProps)} - ${formatTime(shift.endTime, pageProps)}`;
                            } else if (shift.type === 'dayoff') {
                                cellColor = [255, 243, 224];
                                text = t('Day Off');
                            } else {
                                cellColor = [255, 235, 238];
                                text = t('Leave');
                            }
                            if (shift.notes) {
                                text += `\n(${shift.notes})`;
                            }
                            return text;
                        }).join('\n\n');
                        cellContent = cellContent ? `${cellContent}\n\n${shiftsText}` : shiftsText;
                    }

                    if (shifts.length === 0 && !leaveInfo) {
                        cellContent = t('No shifts');
                        cellColor = empIndex % 2 === 0 ? [248, 249, 250] : [255, 255, 255];
                    }

                    row.push({
                        content: cellContent,
                        styles: { fillColor: cellColor, halign: 'left', fontSize: 8, valign: 'top' } as any
                    });
                });
                return row;
            });

            if (!hideEmployeeHours || showEmployeePrice) {
                const totalsRow = [{
                    content: `${t('Total')}\n${calculateOverallTotal(employeesToExport)}`,
                    styles: { fontStyle: 'bold', fillColor: [149, 165, 166], textColor: 255, valign: 'middle' } as any
                }];
                weekDates.forEach(date => {
                    totalsRow.push({
                        content: calculateDayTotal(date, employeesToExport),
                        styles: { fontStyle: 'bold', fillColor: [189, 195, 199], halign: 'center', valign: 'middle', fontSize: 10 } as any
                    });
                });
                rows.push(totalsRow as any);
            }

            autoTable(doc, {
                head: [headers as any],
                body: rows as any,
                startY: 55,
                styles: { fontSize: 9, cellPadding: 4, lineColor: [189, 195, 199], lineWidth: 0.5 } as any,
                headStyles: { fontSize: 10, fontStyle: 'bold', halign: 'center', valign: 'middle' } as any,
                columnStyles: { 0: { cellWidth: 40, fontStyle: 'bold' } } as any,
                margin: { left: 15, right: 15 },
                tableWidth: 'auto',
                theme: 'grid'
            });

            const finalY = (doc as any).lastAutoTable.finalY || 200;
            doc.setFontSize(8);
            doc.setTextColor(100, 100, 100);
            doc.text(`Page 1 of 1 | ${companyName} - ${title || 'Weekly Schedule'}`, 20, finalY + 15);

            doc.save(`schedule-${startDate}-to-${endDate}.pdf`);
        } catch (error) {
            console.error('PDF generation failed:', error);
        }
    };

    return { generatePDF, calculateEmployeeHours, calculateEmployeeCost };
};
