import { useState, useEffect } from 'react';
import { Head, usePage, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Calendar, Save, User } from 'lucide-react';

import { Employee, WorkScheduleIndexProps, WorkScheduleData } from './types';

const DAYS_OF_WEEK = [
    { key: 'monday', label: 'Monday' },
    { key: 'tuesday', label: 'Tuesday' },
    { key: 'wednesday', label: 'Wednesday' },
    { key: 'thursday', label: 'Thursday' },
    { key: 'friday', label: 'Friday' },
    { key: 'saturday', label: 'Saturday' },
    { key: 'sunday', label: 'Sunday' },
];

export default function Index() {
    const { t } = useTranslation();
    const { employees, auth, companyAllSetting } = usePage<WorkScheduleIndexProps & { companyAllSetting?: Record<string, any> }>().props;
    const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(null);
    
    // Get global work schedule settings
    const globalWorkSchedule = {
        monday: companyAllSetting?.rotas_work_schedule_monday === '1' || companyAllSetting?.rotas_work_schedule_monday === 'true',
        tuesday: companyAllSetting?.rotas_work_schedule_tuesday === '1' || companyAllSetting?.rotas_work_schedule_tuesday === 'true',
        wednesday: companyAllSetting?.rotas_work_schedule_wednesday === '1' || companyAllSetting?.rotas_work_schedule_wednesday === 'true',
        thursday: companyAllSetting?.rotas_work_schedule_thursday === '1' || companyAllSetting?.rotas_work_schedule_thursday === 'true',
        friday: companyAllSetting?.rotas_work_schedule_friday === '1' || companyAllSetting?.rotas_work_schedule_friday === 'true',
        saturday: companyAllSetting?.rotas_work_schedule_saturday === '1' || companyAllSetting?.rotas_work_schedule_saturday === 'true',
        sunday: companyAllSetting?.rotas_work_schedule_sunday === '1' || companyAllSetting?.rotas_work_schedule_sunday === 'true',
    };
    
    const { data, setData, put, processing } = useForm<{ work_schedule: WorkScheduleData[] }>({
        work_schedule: []
    });

    useFlashMessages();

    useEffect(() => {
        if (employees && employees.length > 0 && !selectedEmployee) {
            handleEmployeeSelect(employees[0].id.toString());
        }
    }, [employees]);

    const handleEmployeeSelect = (employeeId: string) => {
        const employee = employees.find(emp => emp.id.toString() === employeeId);
        setSelectedEmployee(employee || null);
        
        if (employee?.work_schedule) {
            setData('work_schedule', employee.work_schedule);
        } else {
            // Initialize default schedule based on global settings
            const defaultSchedule = DAYS_OF_WEEK.map(day => ({
                day: day.key,
                is_working: globalWorkSchedule[day.key as keyof typeof globalWorkSchedule] || false
            }));
            setData('work_schedule', defaultSchedule);
        }
    };

    const updateSchedule = (day: string, field: keyof WorkScheduleData, value: any) => {
        // Check if the day is globally disabled
        const isGloballyDisabled = !globalWorkSchedule[day as keyof typeof globalWorkSchedule];
        
        if (isGloballyDisabled && field === 'is_working' && value === true) {
            // Don't allow enabling a day that's globally disabled
            return;
        }
        
        const updatedSchedule = data.work_schedule.map(item => 
            item.day === day ? { ...item, [field]: isGloballyDisabled ? false : value } : item
        );
        setData('work_schedule', updatedSchedule);
    };

    const handleSave = () => {
        if (selectedEmployee) {
            put(route('rotas.work-schedules.update', selectedEmployee.id));
        }
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Rotas'), url: route('rotas.dashboard.index') },
                { label: t('Work Schedules') }
            ]}
            pageTitle={t('Manage Work Schedules')}
        >
            <Head title={t('Work Schedules')} />

            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <div>
                        <h3 className="tracking-tight flex font-sm items-center gap-2 text-lg">
                            <User className="h-5 w-5" />
                            {t('Employee Work Schedule')}
                        </h3>
                        <p className="text-sm text-muted-foreground mt-1">
                            {t('Configure working days for individual employees')}
                        </p>
                    </div>
                    <div className="flex items-center gap-4">
                        <div className="w-64">
                            <Select onValueChange={handleEmployeeSelect} value={selectedEmployee?.id.toString() || ''}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Employee')} />
                                </SelectTrigger>
                                <SelectContent searchable={true}>
                                    {employees?.map((employee) => (
                                        <SelectItem key={employee.id} value={employee.id.toString()}>
                                            {employee.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        {selectedEmployee && auth.user?.permissions?.includes('edit-rotas-work-schedules') && (
                            <Button onClick={handleSave} disabled={processing} size="sm">
                                <Save className="h-4 w-4 mr-2" />
                                {processing ? t('Saving...') : t('Save Changes')}
                            </Button>
                        )}
                    </div>
                </CardHeader>

                {selectedEmployee && (
                    <CardContent>
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div className="lg:col-span-2 space-y-4">
                                <h3 className="font-sm text-lg mb-4">{t('Schedule for')} {selectedEmployee.name}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {DAYS_OF_WEEK.map((day) => {
                                        const scheduleItem = data.work_schedule.find(item => item.day === day.key);
                                        const isGloballyDisabled = !globalWorkSchedule[day.key as keyof typeof globalWorkSchedule];
                                        const isDisabled = !auth.user?.permissions?.includes('edit-rotas-work-schedules') || isGloballyDisabled;
                                        
                                        return (
                                            <div key={day.key} className={`flex items-center justify-between p-3 border rounded-lg ${
                                                isGloballyDisabled ? 'bg-gray-50 opacity-60' : ''
                                            }`}>
                                                <div className="flex items-center gap-2">
                                                    <Label htmlFor={day.key} className="font-medium">
                                                        {t(day.label)}
                                                    </Label>
                                                    {isGloballyDisabled && (
                                                        <span className="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                                            {t('Disabled in Settings')}
                                                        </span>
                                                    )}
                                                </div>
                                                <div className="flex items-center space-x-2">
                                                    <span className="text-sm text-muted-foreground">
                                                        {isGloballyDisabled ? t('Off') : (scheduleItem?.is_working ? t('Working') : t('Off'))}
                                                    </span>
                                                    <Switch
                                                        id={day.key}
                                                        checked={isGloballyDisabled ? false : (scheduleItem?.is_working || false)}
                                                        onCheckedChange={(checked) => 
                                                            updateSchedule(day.key, 'is_working', checked)
                                                        }
                                                        disabled={isDisabled}
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="border rounded-lg p-4 bg-muted/30">
                                <h4 className="font-medium mb-3 flex items-center gap-2">
                                    <Calendar className="h-4 w-4" />
                                    {t('Schedule Summary')}
                                </h4>
                                
                                <div className="space-y-4 text-sm">
                                    <div>
                                        <h5 className="font-medium text-green-700 mb-2">
                                            {t('Working Days')} ({data.work_schedule.filter(item => item.is_working).length})
                                        </h5>
                                        <div className="space-y-1">
                                            {data.work_schedule.filter(item => item.is_working).length > 0 ? (
                                                data.work_schedule.filter(item => item.is_working).map(item => {
                                                    const day = DAYS_OF_WEEK.find(d => d.key === item.day);
                                                    return (
                                                        <div key={item.day} className="flex items-center gap-2">
                                                            <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                                            <span>{t(day?.label || item.day)}</span>
                                                        </div>
                                                    );
                                                })
                                            ) : (
                                                <span className="text-muted-foreground italic">{t('No working days selected')}</span>
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <h5 className="font-medium text-red-700 mb-2">
                                            {t('Off Days')} ({data.work_schedule.filter(item => !item.is_working).length})
                                        </h5>
                                        <div className="space-y-1">
                                            {data.work_schedule.filter(item => !item.is_working).length > 0 ? (
                                                data.work_schedule.filter(item => !item.is_working).map(item => {
                                                    const day = DAYS_OF_WEEK.find(d => d.key === item.day);
                                                    return (
                                                        <div key={item.day} className="flex items-center gap-2">
                                                            <div className="w-2 h-2 bg-red-500 rounded-full"></div>
                                                            <span>{t(day?.label || item.day)}</span>
                                                        </div>
                                                    );
                                                })
                                            ) : (
                                                <span className="text-muted-foreground italic">{t('No off days selected')}</span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                )}
            </Card>
        </AuthenticatedLayout>
    );
}