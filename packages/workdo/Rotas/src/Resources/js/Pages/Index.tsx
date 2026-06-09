import { useMemo, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Calendar as CalendarIcon, Users, Clock, CheckCircle, UserCheck, Building } from 'lucide-react';
import CalendarView from '@/components/calendar-view';
import { formatDate, formatTime, formatDateTime } from '@/utils/helpers';

// Components to handle helper functions without conditional hooks
const DateDisplay = ({ date, pageProps }: { date: string; pageProps: any }) => {
    return <>{formatDate(date, pageProps)}</>;
};

const TimeDisplay = ({ time, pageProps }: { time: string; pageProps: any }) => {
    return <>{formatTime(time, pageProps)}</>;
};

interface RotasProps {
    message: string;
    stats?: {
        total_employees: number;
        total_rotas: number;
        published_rotas: number;
        pending_rotas: number;
    };
    calendarShifts?: Array<{
        id: number | string;
        employee_name: string;
        date: string;
        start_time: string;
        end_time: string;
        type: string;
        is_published: boolean;
        branch_name?: string;
        department_name?: string;
        designation_name?: string;
        shift_name?: string;
        break_time?: number;
        notes?: string;
        issued_by_name?: string;
        total_hours?: number;
        leave_type?: string;
        reason?: string;
    }>;
}

export default function RotasIndex({ message, stats, calendarShifts }: RotasProps) {
    const { t } = useTranslation();
    const [selectedShift, setSelectedShift] = useState<any>(null);
    const [shiftDialog, setShiftDialog] = useState(false);
    const { pageProps } = usePage().props as any;
    
    const getShiftColor = (type: string) => {
        const colors = {
            shift: '#3b82f6',
            dayoff: '#f59e0b', 
            leave: '#ef4444',
        };
        return colors[type as keyof typeof colors] || '#6b7280';
    };
    
    const calendarEvents = (() => {
        if (!calendarShifts || calendarShifts.length === 0) return [];
        
        return calendarShifts.map(shift => {
            let title = shift.employee_name;
            if (shift.type === 'shift' && shift.start_time && shift.end_time) {
                title += ` (${shift.start_time}-${shift.end_time})`;
            } else if (shift.type === 'dayoff') {
                title += ` - ${t('Day Off')}`;
            } else if (shift.type === 'leave') {
                title += ` - ${t('Leave')}`;
            }
            
            return {
                id: shift.id,
                title: title,
                startDate: shift.date,
                endDate: shift.date,
                time: shift.start_time || '00:00',
                color: getShiftColor(shift.type),
                type: shift.type
            };
        });
    })();
    
    return (
        <AuthenticatedLayout
            breadcrumbs={[{label: t('Rotas')}]}
            pageTitle={t('Rotas Dashboard')}
        >
            <Head title={t('Rotas')} />
            
            {/* First Row - Welcome Banner Left, Stats Cards Right */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {/* Left Side - Welcome Banner */}
                <div className="bg-gradient-to-r from-primary/90 to-primary/70 rounded-lg p-8 text-white relative overflow-hidden" style={{ minHeight: '200px' }}>
                    {/* Background SVG Pattern */}
                    <div className="absolute inset-0 opacity-20">
                        <svg className="w-full h-full" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="rotasGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stopColor="currentColor" stopOpacity="0.4"/>
                                    <stop offset="100%" stopColor="currentColor" stopOpacity="0.1"/>
                                </linearGradient>
                            </defs>
                            
                            {/* Schedule Grid */}
                            <g transform="translate(50, 30)">
                                <rect width="80" height="60" rx="4" fill="url(#rotasGrad)" opacity="0.6">
                                    <animate attributeName="opacity" values="0.6;0.8;0.6" dur="3s" repeatCount="indefinite"/>
                                </rect>
                                <line x1="10" y1="15" x2="70" y2="15" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <line x1="10" y1="30" x2="70" y2="30" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <line x1="10" y1="45" x2="70" y2="45" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <line x1="25" y1="5" x2="25" y2="55" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <line x1="40" y1="5" x2="40" y2="55" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <line x1="55" y1="5" x2="55" y2="55" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                <circle cx="32" cy="22" r="3" fill="currentColor" opacity="0.7">
                                    <animate attributeName="r" values="3;5;3" dur="2s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="47" cy="37" r="3" fill="currentColor" opacity="0.7">
                                    <animate attributeName="r" values="3;5;3" dur="2.5s" repeatCount="indefinite"/>
                                </circle>
                            </g>
                            
                            {/* Clock */}
                            <g transform="translate(280, 40)">
                                <circle cx="20" cy="20" r="18" fill="url(#rotasGrad)" opacity="0.6">
                                    <animate attributeName="opacity" values="0.6;0.9;0.6" dur="4s" repeatCount="indefinite"/>
                                </circle>
                                <line x1="20" y1="8" x2="20" y2="20" stroke="currentColor" strokeWidth="2" opacity="0.5"/>
                                <line x1="20" y1="20" x2="28" y2="28" stroke="currentColor" strokeWidth="2" opacity="0.5"/>
                            </g>
                            
                            {/* Employee Icons */}
                            <g transform="translate(150, 120)">
                                <circle cx="15" cy="15" r="12" fill="url(#rotasGrad)" opacity="0.7">
                                    <animate attributeName="opacity" values="0.7;1;0.7" dur="3s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="15" cy="12" r="4" fill="currentColor" opacity="0.4"/>
                                <path d="M7 25 Q7 18 15 18 Q23 18 23 25" fill="currentColor" opacity="0.4"/>
                            </g>
                        </svg>
                    </div>
                    
                    <div className="flex items-center justify-between relative z-10 h-full">
                        <div className="flex-1">
                            <h2 className="text-3xl font-bold mb-2">{t('Welcome to Rotas')}</h2>
                            <p className="text-white/80 mb-2 text-lg">{t('Manage employee schedules and shift planning efficiently.')}</p>
                            <p className="text-white/70 mb-4 text-sm">{t('Create, publish, and track work rotas for your team with ease.')}</p>
                        </div>
                    </div>
                </div>
                
                {/* Right Side - Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-2 gap-4">
                        <Card className="bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-blue-700">{t('Total Employees')}</CardTitle>
                                <Users className="h-8 w-8 text-blue-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-bold text-blue-700">{stats.total_employees}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-green-50 to-green-100 border-green-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-green-700">{t('Total Rotas')}</CardTitle>
                                <Clock className="h-8 w-8 text-green-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-bold text-green-700">{stats.total_rotas}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-purple-700">{t('Published Rotas')}</CardTitle>
                                <CheckCircle className="h-8 w-8 text-purple-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-bold text-purple-700">{stats.published_rotas}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-orange-700">{t('Pending Rotas')}</CardTitle>
                                <UserCheck className="h-8 w-8 text-orange-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-bold text-orange-700">{stats.pending_rotas}</div>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
            
            {/* Calendar Section */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2">
                   <CalendarView 
                        events={calendarEvents}
                        onEventClick={(event) => {
                            const shift = calendarShifts?.find(s => s.id === event.id);
                            if (shift) {
                                setSelectedShift(shift);
                                setShiftDialog(true);
                            }
                        }}
                    />
                </div>
                
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">{t('Current Month Rotas')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {calendarShifts && calendarShifts.length > 0 ? (
                                <div className="space-y-3 max-h-96 overflow-y-auto">
                                    {calendarShifts.map((shift) => (
                                        <div key={shift.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div className="flex-1">
                                                <div className="font-medium text-sm">{shift.employee_name}</div>
                                                <div className="text-xs text-muted-foreground">
                                                    <DateDisplay date={shift.date} pageProps={pageProps} />
                                                </div>
                                                {shift.type === 'shift' && shift.start_time && shift.end_time && (
                                                    <div className="text-xs text-muted-foreground">
                                                        <TimeDisplay time={shift.start_time} pageProps={pageProps} /> - <TimeDisplay time={shift.end_time} pageProps={pageProps} />
                                                    </div>
                                                )}
                                            </div>
                                            <div className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                shift.type === 'shift' ? 'bg-blue-100 text-blue-700' :
                                                shift.type === 'dayoff' ? 'bg-orange-100 text-orange-700' :
                                                'bg-red-100 text-red-700'
                                            }`}>
                                                {shift.type === 'shift' ? t('Shift') :
                                                 shift.type === 'dayoff' ? t('Day Off') : t('Leave')}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground">
                                    <Clock className="h-8 w-8 mx-auto mb-2 opacity-50" />
                                    <p className="text-sm">{t('No rotas for this month')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Shift Details Dialog */}
            <Dialog open={shiftDialog} onOpenChange={(open) => {
                setShiftDialog(open);
                if (!open) {
                    setSelectedShift(null);
                }
            }}>
                <DialogContent className="max-w-md z-[9999]" style={{ zIndex: 9999 }}>
                    <DialogHeader>
                        <DialogTitle>{selectedShift?.type === 'leave' ? t('Leave Details') : t('Rota Details')}</DialogTitle>
                        <DialogDescription>
                            {selectedShift?.type === 'leave' ? t('View detailed information about this leave') : t('View detailed information about this rota assignment')}
                        </DialogDescription>
                    </DialogHeader>
                    {selectedShift && (
                        <div className="space-y-4">
                            <div className="grid grid-cols-3 gap-4 items-center">
                                <label className="text-sm font-medium text-gray-700">{t('Employee')}:</label>
                                <p className="text-sm text-gray-900 col-span-2">{selectedShift.employee_name}</p>
                            </div>
                            <div className="grid grid-cols-3 gap-4 items-center">
                                <label className="text-sm font-medium text-gray-700">{t('Date')}:</label>
                                <p className="text-sm text-gray-900 col-span-2"><DateDisplay date={selectedShift.date} pageProps={pageProps} /></p>
                            </div>
                            {selectedShift.type === 'shift' && selectedShift.start_time && selectedShift.end_time && (
                                <>
                                    <div className="grid grid-cols-3 gap-4 items-center">
                                        <label className="text-sm font-medium text-gray-700">{t('Start Time')}:</label>
                                        <p className="text-sm text-gray-900 col-span-2">{formatDateTime(selectedShift.start_time)}</p>
                                    </div>
                                    <div className="grid grid-cols-3 gap-4 items-center">
                                        <label className="text-sm font-medium text-gray-700">{t('End Time')}:</label>
                                        <p className="text-sm text-gray-900 col-span-2">{formatDateTime(selectedShift.end_time)}</p>
                                    </div>
                                    {selectedShift.break_time !== undefined && selectedShift.break_time > 0 && (
                                        <div className="grid grid-cols-3 gap-4 items-center">
                                            <label className="text-sm font-medium text-gray-700">{t('Break Time')}:</label>
                                            <p className="text-sm text-gray-900 col-span-2">{selectedShift.break_time} {t('minutes')}</p>
                                        </div>
                                    )}
                                    {selectedShift.total_hours !== undefined && selectedShift.total_hours > 0 && (
                                        <div className="grid grid-cols-3 gap-4 items-center">
                                            <label className="text-sm font-medium text-gray-700">{t('Total Hours')}:</label>
                                            <p className="text-sm text-gray-900 col-span-2">{selectedShift.total_hours} {t('hours')}</p>
                                        </div>
                                    )}
                                </>
                            )}
                            {selectedShift.branch_name && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Branch')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.branch_name}</p>
                                </div>
                            )}
                            {selectedShift.department_name && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Department')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.department_name}</p>
                                </div>
                            )}
                            {selectedShift.designation_name && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Designation')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.designation_name}</p>
                                </div>
                            )}
                            {selectedShift.shift_name && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Shift')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.shift_name}</p>
                                </div>
                            )}
                            {selectedShift.type === 'leave' && selectedShift.leave_type && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Leave Type')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.leave_type}</p>
                                </div>
                            )}
                            {selectedShift.type === 'leave' && selectedShift.reason && (
                                <div className="grid grid-cols-3 gap-4 items-start">
                                    <label className="text-sm font-medium text-gray-700">{t('Reason')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.reason}</p>
                                </div>
                            )}
                            {selectedShift.issued_by_name && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Issued By')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.issued_by_name}</p>
                                </div>
                            )}
                            {selectedShift.type !== 'leave' && (
                                <div className="grid grid-cols-3 gap-4 items-center">
                                    <label className="text-sm font-medium text-gray-700">{t('Status')}:</label>
                                    <p className={`text-sm font-medium col-span-2 ${
                                        selectedShift.is_published ? 'text-green-700' : 'text-gray-600'
                                    }`}>
                                        {selectedShift.is_published ? t('Published') : t('Unpublished')}
                                    </p>
                                </div>
                            )}
                            {selectedShift.notes && (
                                <div className="grid grid-cols-3 gap-4 items-start">
                                    <label className="text-sm font-medium text-gray-700">{t('Rota Note')}:</label>
                                    <p className="text-sm text-gray-900 col-span-2">{selectedShift.notes}</p>
                                </div>
                            )}
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}