import { Head } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Calendar as CalendarIcon, Users, Clock, CheckCircle, Copy, QrCode } from 'lucide-react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LabelList } from 'recharts';
import CalendarView from '@/components/calendar-view';
import { useState, useMemo } from 'react';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import QRCode from 'qrcode';
import { AppointmentDialog } from './Appointments/components/AppointmentDialog';

interface BookingsProps {
    message: string;
    bookingUrl?: string;
    dashboardType: 'main' | 'staff';
    currentUser: {
        id: number;
        name: string;
        type: string;
    };
    stats?: {
        totalAppointments: number;
        todayAppointments: number;
        totalCustomers: number;
        pendingAppointments: number;
    };
    recentAppointments?: Array<{
        id: number;
        appointment_number: string;
        customer_name: string;
        service_name: string;
        date: string;
        time: string;
        status: string;
    }>;
    chartData?: Array<{
        date: string;
        appointments: number;
    }>;
    calendarAppointments?: Array<{
        id: number;
        title: string;
        date: string;
        start_time: string;
        status: string;
    }>;
}

export default function BookingsIndex({ message, bookingUrl, dashboardType, currentUser, stats, recentAppointments, chartData, calendarAppointments }: BookingsProps) {
    const { t } = useTranslation();
    
    const [qrCodeUrl, setQrCodeUrl] = useState('');
    const [dialogMode, setDialogMode] = useState<'create' | 'edit' | 'view' | null>(null);
    const [selectedAppointment, setSelectedAppointment] = useState<any>(null);
    
    const copyToClipboard = async () => {
        if (bookingUrl) {
            await navigator.clipboard.writeText(bookingUrl);
            toast.success(t('Link copied to clipboard!'));
        }
    };
    
    useMemo(() => {
        if (bookingUrl) {
            QRCode.toDataURL(bookingUrl)
                .then(setQrCodeUrl)
                .catch(console.error);
        }
    }, [bookingUrl]);
    
    const getStatusColor = (status: string) => {
        const colors = {
            pending: '#f59e0b',
            confirmed: '#3b82f6', 
            completed: '#10b981',
        };
        return colors[status as keyof typeof colors] || '#6b7280';
    };
    
    const calendarEvents = useMemo(() => {
        return calendarAppointments?.map(appointment => ({
            id: appointment.id,
            title: `${appointment.title} - ${appointment.time}`,
            startDate: appointment.date,
            endDate: appointment.date,
            time: appointment.start_time,
            color: getStatusColor(appointment.status),
            status: appointment.status
        })) || [];
    }, [calendarAppointments]);
    
    return (
        <AuthenticatedLayout
            breadcrumbs={[{label: t('Bookings')}]}
            pageTitle={dashboardType === 'staff' ? t('Staff Dashboard') : t('Bookings Dashboard')}
        >
            <Head title={dashboardType === 'staff' ? t('Staff Dashboard') : t('Bookings')} />
            
            {/* First Row - Welcome Banner Left, Stats Cards Right */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {/* Left Side - Welcome Banner */}
                <div>
                    <div className="bg-gradient-to-r from-primary/90 to-primary/70 rounded-lg p-8 text-white relative overflow-hidden" style={{ minHeight: '300px' }}>
                        {/* Background SVG Pattern */}
                        <div className="absolute inset-0 opacity-25">
                            <svg className="w-full h-full" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stopColor="currentColor" stopOpacity="0.3"/>
                                        <stop offset="100%" stopColor="currentColor" stopOpacity="0.1"/>
                                    </linearGradient>
                                    <filter id="glow">
                                        <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                        <feMerge>
                                            <feMergeNode in="coloredBlur"/>
                                            <feMergeNode in="SourceGraphic"/>
                                        </feMerge>
                                    </filter>
                                </defs>
                                
                                {/* Calendar Vector */}
                                <g transform="translate(60, 40)" filter="url(#glow)">
                                    <rect width="30" height="35" rx="4" fill="url(#grad1)">
                                        <animateTransform attributeName="transform" type="scale" values="1;1.05;1" dur="3s" repeatCount="indefinite"/>
                                    </rect>
                                    <rect x="3" y="8" width="24" height="24" rx="2" fill="none" stroke="currentColor" strokeWidth="1" opacity="0.4"/>
                                    <line x1="8" y1="3" x2="8" y2="12" stroke="currentColor" strokeWidth="2" opacity="0.6"/>
                                    <line x1="22" y1="3" x2="22" y2="12" stroke="currentColor" strokeWidth="2" opacity="0.6"/>
                                    <circle cx="10" cy="18" r="1.5" fill="currentColor" opacity="0.7">
                                        <animate attributeName="opacity" values="0.7;1;0.7" dur="2s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="15" cy="18" r="1.5" fill="currentColor" opacity="0.7">
                                        <animate attributeName="opacity" values="0.7;1;0.7" dur="2s" begin="0.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="20" cy="18" r="1.5" fill="currentColor" opacity="0.7">
                                        <animate attributeName="opacity" values="0.7;1;0.7" dur="2s" begin="1s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                {/* Clock Vector */}
                                <g transform="translate(280, 30)" filter="url(#glow)">
                                    <circle cx="20" cy="20" r="18" fill="url(#grad1)">
                                        <animate attributeName="r" values="18;20;18" dur="4s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="currentColor" strokeWidth="1.5" opacity="0.4">
                                        <animate attributeName="opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite"/>
                                    </circle>
                                    <line x1="20" y1="8" x2="20" y2="20" stroke="currentColor" strokeWidth="2" opacity="0.6"/>
                                    <line x1="20" y1="20" x2="28" y2="28" stroke="currentColor" strokeWidth="2" opacity="0.6"/>
                                    <circle cx="20" cy="20" r="2" fill="currentColor" opacity="0.8">
                                        <animate attributeName="r" values="2;3;2" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                {/* User Vector */}
                                <g transform="translate(150, 120)" filter="url(#glow)">
                                    <circle cx="15" cy="12" r="8" fill="url(#grad1)">
                                        <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" repeatCount="indefinite"/>
                                    </circle>
                                    <path d="M5 35 Q5 25 15 25 Q25 25 25 35" fill="url(#grad1)">
                                        <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" begin="1s" repeatCount="indefinite"/>
                                    </path>
                                    <circle cx="15" cy="12" r="5" fill="none" stroke="currentColor" strokeWidth="1.5" opacity="0.4"/>
                                    <path d="M7 30 Q7 22 15 22 Q23 22 23 30" stroke="currentColor" strokeWidth="1.5" fill="none" opacity="0.4"/>
                                </g>
                                
                                {/* Appointment Check Vector */}
                                <g transform="translate(320, 120)" filter="url(#glow)">
                                    <rect width="25" height="25" rx="5" fill="url(#grad1)">
                                        <animateTransform attributeName="transform" type="rotate" values="0 12.5 12.5;5 12.5 12.5;0 12.5 12.5;-5 12.5 12.5;0 12.5 12.5" dur="4s" repeatCount="indefinite"/>
                                    </rect>
                                    <path d="M8 12 L11 15 L17 9" stroke="currentColor" strokeWidth="2.5" fill="none" opacity="0.7">
                                        <animate attributeName="stroke-dasharray" values="0 20;20 0;0 20" dur="2s" repeatCount="indefinite"/>
                                    </path>
                                </g>
                                
                                {/* Flowing Lines */}
                                <path d="M0 100 Q100 80 200 100 T400 100" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.2">
                                    <animate attributeName="stroke-dasharray" values="0 400;200 200;400 0;0 400" dur="6s" repeatCount="indefinite"/>
                                </path>
                                <path d="M0 140 Q150 120 300 140 T400 140" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.15">
                                    <animate attributeName="stroke-dasharray" values="400 0;200 200;0 400;400 0" dur="8s" repeatCount="indefinite"/>
                                </path>
                            </svg>
                        </div>
                        
                        <div className="flex items-center justify-between relative z-10 h-full">
                            <div className="flex-1 pr-6">
                                <h2 className="text-3xl font-bold mb-3">
                                    {dashboardType === 'staff' 
                                        ? t('Welcome {{name}}', { name: currentUser.name })
                                        : t('Welcome to Bookings')
                                    }
                                </h2>
                                <p className="text-white/80 mb-3 text-lg">
                                    {dashboardType === 'staff'
                                        ? t('Manage your assigned appointments and provide excellent service to customers.')
                                        : t('Streamline your appointment scheduling with our comprehensive booking system.')
                                    }
                                </p>
                                <p className="text-white/70 mb-4 text-sm">
                                    {dashboardType === 'staff'
                                        ? t('View your schedule, update appointment status, and track your daily activities.')
                                        : t('Manage services, staff, customers, and track your business performance all in one place.')
                                    }
                                </p>
                                {bookingUrl && dashboardType === 'main' && (
                                    <div className="flex items-center gap-3">
                                        <Button 
                                            variant="secondary" 
                                            size="sm" 
                                            onClick={copyToClipboard}
                                            className="bg-white/20 hover:bg-white/30 text-white border-white/30"
                                        >
                                            <Copy className="h-4 w-4 mr-2" />
                                            {t('Copy Link')}
                                        </Button>
                                    </div>
                                )}
                            </div>
                            {bookingUrl && dashboardType === 'main' && (
                                <div className="bg-white p-6 rounded-lg shadow-lg">
                                    {qrCodeUrl ? (
                                        <img src={qrCodeUrl} alt="QR Code" className="w-36 h-36" />
                                    ) : (
                                        <div className="w-36 h-36 bg-gray-100 rounded flex items-center justify-center">
                                            <QrCode className="h-16 w-16 text-gray-400" />
                                        </div>
                                    )}
                                    <p className="text-sm text-gray-600 mt-3 text-center font-medium">{t('Scan to book')}</p>
                                </div>
                            )}
                            {dashboardType === 'staff' && (
                                <div className="bg-white/10 p-6 rounded-lg backdrop-blur-sm">
                                    <div className="text-center">
                                        <div className="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <Users className="h-12 w-12 text-white" />
                                        </div>
                                        <h3 className="text-lg font-semibold text-white mb-2">{t('Staff Dashboard')}</h3>
                                        <p className="text-white/80 text-sm">{t('Your personalized workspace')}</p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Right Side - Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-2 gap-4">
                        <Card className="bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-blue-700">
                                    {dashboardType === 'staff' ? t('My Appointments') : t('Total Appointments')}
                                </CardTitle>
                                <CalendarIcon className="h-6 w-6 text-blue-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-blue-700">{stats.totalAppointments}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-green-50 to-green-100 border-green-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-green-700">
                                    {dashboardType === 'staff' ? t('Today\'s Schedule') : t('Today\'s Appointments')}
                                </CardTitle>
                                <Clock className="h-6 w-6 text-green-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-green-700">{stats.todayAppointments}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-orange-700">
                                    {dashboardType === 'staff' ? t('My Customers') : t('Total Customers')}
                                </CardTitle>
                                <Users className="h-6 w-6 text-orange-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-orange-700">{stats.totalCustomers}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200">
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium text-purple-700">
                                    {dashboardType === 'staff' ? t('Pending Tasks') : t('Pending Appointments')}
                                </CardTitle>
                                <CheckCircle className="h-6 w-6 text-purple-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-purple-700">{stats.pendingAppointments}</div>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>

            {/* Second Row - Calendar Left, Recent Appointments Right */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {/* Left Side - Calendar */}
                {calendarAppointments && (
                    <CalendarView 
                        events={calendarEvents}
                        onEventClick={(event) => {
                            // Find the appointment from calendarAppointments using the event id
                            const appointment = calendarAppointments?.find(apt => apt.id === event.id);
                            if (appointment) {
                                setSelectedAppointment(appointment);
                                setDialogMode('view');
                            }
                        }}
                    />
                )}

                {/* Right Side - Chart and Recent Appointments */}
                <div className="space-y-6 h-full">
                    {/* Chart */}
                    {chartData && (
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    {dashboardType === 'staff' ? t('My Bookings Trend') : t('Bookings Trend')}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ResponsiveContainer width="100%" height={250}>
                                    <LineChart data={chartData} margin={{ top: 20, right: 30, left: 60, bottom: 40 }}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis 
                                            dataKey="date" 
                                            tick={{ fontSize: 12, fill: '#374151' }}
                                            tickLine={{ stroke: '#374151' }}
                                            label={{ value: t('Days'), position: 'insideBottom', offset: -5, style: { textAnchor: 'middle', fill: '#374151', fontSize: '14px' } }}
                                        />
                                        <YAxis 
                                            tick={{ fontSize: 12, fill: '#374151' }}
                                            tickLine={{ stroke: '#374151' }}
                                            label={{ value: t('Appointments'), angle: -90, position: 'insideLeft', style: { textAnchor: 'middle', fill: '#374151', fontSize: '14px' } }}
                                        />
                                        <Tooltip />
                                        <Line 
                                            type="monotone" 
                                            dataKey="appointments" 
                                            stroke="hsl(var(--primary))" 
                                            strokeWidth={2}
                                            dot={{ fill: 'hsl(var(--primary))', strokeWidth: 2, r: 4 }}
                                        >
                                            <LabelList dataKey="appointments" position="top" style={{ fill: '#374151', fontSize: '12px', fontWeight: 'bold' }} />
                                        </Line>
                                    </LineChart>
                                </ResponsiveContainer>
                            </CardContent>
                        </Card>
                    )}

                    {/* Recent Appointments */}
                    {recentAppointments && recentAppointments.length > 0 && (
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    {dashboardType === 'staff' ? t('My Recent Appointments') : t('Recent Appointments')}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {t('Appointment #')}
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {t('Customer')}
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {t('Service')}
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {t('Date & Time')}
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {t('Status')}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {recentAppointments.map((appointment) => (
                                                <tr key={appointment.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {appointment.appointment_number}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {appointment.customer_name}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {appointment.service_name}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {appointment.date} at {appointment.time}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                            appointment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                            appointment.status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                                            appointment.status === 'completed' ? 'bg-green-100 text-green-800' :
                                                            appointment.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                                            'bg-gray-100 text-gray-800'
                                                        }`}>
                                                            {appointment.status ? appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) : '-'}
                                                        </span>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>


            <AppointmentDialog
                mode={dialogMode || 'view'}
                open={!!dialogMode}
                onOpenChange={() => {
                    setDialogMode(null);
                    setSelectedAppointment(null);
                }}
                appointment={selectedAppointment}
                items={[]}
                packages={[]}
                users={[]}
                customers={[]}
                onSuccess={() => window.location.reload()}
            />

        </AuthenticatedLayout>
    );
}