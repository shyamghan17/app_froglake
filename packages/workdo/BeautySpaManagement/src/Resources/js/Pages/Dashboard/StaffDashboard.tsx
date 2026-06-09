import { Head, router } from '@inertiajs/react';
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

interface StaffDashboardProps {
    message: string;
    bookingUrl?: string;
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

export default function StaffDashboard({ message, bookingUrl, stats, recentAppointments, chartData, calendarAppointments }: StaffDashboardProps) {
    const { t } = useTranslation();

    const [qrCodeUrl, setQrCodeUrl] = useState('');

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
            cancelled: '#ef4444',
        };
        return colors[status as keyof typeof colors] || '#6b7280';
    };

    const calendarEvents = useMemo(() => {
        return calendarAppointments?.map(appointment => ({
            id: appointment.id,
            title: `${appointment.title} - ${appointment.start_time}`,
            startDate: appointment.date,
            endDate: appointment.date,
            time: appointment.start_time,
            color: getStatusColor(appointment.status),
            status: appointment.status
        })) || [];
    }, [calendarAppointments]);

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t('Beauty Spa Management') }, { label: t('Dashboard') }]}
            pageTitle={t('Dashboard')}
        >
            <Head title={t('Staff Dashboard')} />

            {/* First Row - Welcome Banner Left, Stats Cards Right */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {/* Left Side - Welcome Banner */}
                <div>
                    <div className="bg-gradient-to-r from-primary/90 to-primary/70 rounded-lg p-6 text-white relative overflow-hidden" style={{ minHeight: '200px' }}>
                        {/* Background SVG Pattern */}
                        <div className="absolute inset-0 opacity-25">
                            <svg className="w-full h-full" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stopColor="currentColor" stopOpacity="0.3" />
                                        <stop offset="100%" stopColor="currentColor" stopOpacity="0.1" />
                                    </linearGradient>
                                </defs>

                                {/* Beauty Spa Icons */}
                                <g transform="translate(60, 40)">
                                    <circle cx="15" cy="15" r="12" fill="url(#grad1)">
                                        <animate attributeName="r" values="12;15;12" dur="3s" repeatCount="indefinite" />
                                    </circle>
                                    {/* Spa Lotus */}
                                    <path d="M8 15 Q15 8 22 15 Q15 22 8 15" fill="currentColor" opacity="0.4" />
                                    <path d="M12 15 Q15 12 18 15 Q15 18 12 15" fill="currentColor" opacity="0.6" />
                                </g>

                                {/* Scissors Icon */}
                                <g transform="translate(280, 30)">
                                    <circle cx="15" cy="15" r="15" fill="url(#grad1)">
                                        <animate attributeName="opacity" values="0.8;1;0.8" dur="2s" repeatCount="indefinite" />
                                    </circle>
                                    <path d="M8 8 L22 22 M8 22 L22 8" stroke="currentColor" strokeWidth="2" opacity="0.6" />
                                    <circle cx="8" cy="8" r="3" fill="none" stroke="currentColor" strokeWidth="1.5" opacity="0.5" />
                                    <circle cx="8" cy="22" r="3" fill="none" stroke="currentColor" strokeWidth="1.5" opacity="0.5" />
                                </g>

                                {/* Makeup Brush */}
                                <g transform="translate(150, 120)">
                                    <ellipse cx="15" cy="15" rx="10" ry="15" fill="url(#grad1)">
                                        <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" repeatCount="indefinite" />
                                    </ellipse>
                                    <rect x="12" y="5" width="6" height="20" rx="3" fill="none" stroke="currentColor" strokeWidth="1.5" opacity="0.4" />
                                    <ellipse cx="15" cy="8" rx="4" ry="2" fill="currentColor" opacity="0.6" />
                                </g>

                                {/* Mirror/Compact */}
                                <g transform="translate(320, 120)">
                                    <circle cx="15" cy="15" r="12" fill="url(#grad1)">
                                        <animateTransform attributeName="transform" type="rotate" values="0 15 15;5 15 15;0 15 15;-5 15 15;0 15 15" dur="4s" repeatCount="indefinite" />
                                    </circle>
                                    <circle cx="15" cy="15" r="8" fill="none" stroke="currentColor" strokeWidth="2" opacity="0.6" />
                                    <circle cx="15" cy="15" r="4" fill="currentColor" opacity="0.3">
                                        <animate attributeName="opacity" values="0.3;0.7;0.3" dur="2s" repeatCount="indefinite" />
                                    </circle>
                                </g>

                                <path d="M0 100 Q100 80 200 100 T400 100" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.2">
                                    <animate attributeName="stroke-dasharray" values="0 400;200 200;400 0;0 400" dur="6s" repeatCount="indefinite" />
                                </path>
                            </svg>
                        </div>

                        <div className="flex items-center justify-between relative z-10 h-full">
                            <div className="flex-1 pr-6">
                                <h2 className="text-3xl font-bold mb-3">{t('Beauty Spa Dashboard')}</h2>
                                <p className="text-white/80 mb-3 text-lg">{t('Manage your appointments and provide excellent beauty services.')}</p>
                                <p className="text-white/70 mb-4 text-sm">{t('Track your daily schedule, appointments, and customer interactions.')}</p>
                                {bookingUrl && (
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
                            {bookingUrl && (
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
                        </div>
                    </div>
                </div>

                {/* Right Side - Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-2 gap-4">
                        <Card className="bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200 cursor-pointer" onClick={() => router.visit(route('beauty-spa-management.beauty-bookings.index'))}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-xs font-medium text-blue-700">{t('Total Bookings')}</CardTitle>
                                <CalendarIcon className="h-6 w-6 text-blue-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-blue-700">{stats.totalAppointments}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-green-50 to-green-100 border-green-200 cursor-pointer" onClick={() => router.visit(route('beauty-spa-management.booking-order.index'))}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-xs font-medium text-green-700">{t('Complete Booking')}</CardTitle>
                                <Clock className="h-6 w-6 text-green-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-green-700">{stats.todayAppointments}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200 cursor-pointer" onClick={() => router.visit(route('beauty-spa-management.booking-order.index'))}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-xs font-medium text-orange-700">{t('Pending Bookings')}</CardTitle>
                                <Users className="h-6 w-6 text-orange-600" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold text-orange-700">{stats.totalCustomers}</div>
                            </CardContent>
                        </Card>
                        <Card className="bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200 cursor-pointer" onClick={() => router.visit(route('beauty-spa-management.booking-order.index'))}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-xs font-medium text-purple-700">{t('Total Customers')}</CardTitle>
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
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">{t('Calendar')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <CalendarView events={calendarEvents} />
                        </CardContent>
                    </Card>
                )}

                {/* Right Side - Chart and Recent Appointments */}
                <div className="space-y-6 h-full">
                    {/* Chart */}
                    {chartData && (
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-lg">{t('Bookings Trend')}</CardTitle>
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
                                <CardTitle>{t('Recent Appointments')}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
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
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {appointment.customer_name}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {appointment.service_name}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <div>{appointment.date}</div>
                                                        <div className="text-xs text-gray-500">{appointment.time}</div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${appointment.stage_id === 0 ? 'bg-yellow-100 text-yellow-800' :
                                                                appointment.stage_id === 1 ? 'bg-blue-100 text-blue-800' :
                                                                    appointment.stage_id === 2 ? 'bg-green-100 text-green-800' :
                                                                        appointment.stage_id === 3 ? 'bg-red-100 text-red-800' :
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
        </AuthenticatedLayout>
    );
}