import { useState, useMemo } from 'react';
import { Head } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Camera, Users, DollarSign, Clock, Wrench, CalendarCheck, QrCode } from 'lucide-react';
import SocialLinks from '@/components/SocialLinks';
import { PieChart } from '@/components/charts';
import { formatDate, formatCurrency } from '@/utils/helpers';
import { toast } from 'sonner';
import { route } from 'ziggy-js';
import QRCode from 'qrcode';

interface CompanyDashboardProps {
    message: string;
    userSlug: string;
    welcomeCard?: {
        title: string;
        description: string;
        buttonText: string;
        buttonIcon: string;
    };
    stats?: {
        total_appointments: number;
        total_team_members: number;
        total_services: number;
        total_revenue: number;
        pending_appointments: number;
    };
    recentAppointments?: any[];
    recentTeamMembers?: any[];
    appointmentStatusChart?: any[];
    paymentStatusChart?: any[];
}

const STATUS_COLORS: Record<string, string> = {
    pending: '#eab308',
    scheduled: '#6b7280',
    completed: '#10b981',
    cancelled: '#ef4444',
    cleared: '#10b981',
};

export default function CompanyDashboard({
    message, userSlug, welcomeCard, stats,
    recentAppointments, recentTeamMembers,
    appointmentStatusChart, paymentStatusChart,
}: CompanyDashboardProps) {
    const { t } = useTranslation();
    const [qrCodeUrl, setQrCodeUrl] = useState('');

    const frontendUrl = route('photo-studio-management.frontend.index', { userSlug });

    useMemo(() => {
        if (frontendUrl) QRCode.toDataURL(frontendUrl).then(setQrCodeUrl);
    }, [frontendUrl]);

    const copyToClipboard = async () => {
        await navigator.clipboard.writeText(frontendUrl);
        toast.success(t('Link copied to clipboard!'));
    };

    const appointmentChartWithColors = appointmentStatusChart?.map(item => ({
        ...item,
        color: STATUS_COLORS[item.name?.toLowerCase()] ?? '#6b7280',
    }));

    const paymentChartWithColors = paymentStatusChart?.map(item => ({
        ...item,
        color: STATUS_COLORS[item.name?.toLowerCase()] ?? '#6b7280',
    }));

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t('Photo Studio Management') }]}
            pageTitle={t('Photo Studio Dashboard')}
        >
            <Head title={t('Photo Studio Management')} />

            <div className="space-y-6">
                {/* Welcome Banner + Stats */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-gradient-to-r from-primary/90 to-primary/70 rounded-lg p-8 text-white relative overflow-hidden" style={{ minHeight: '300px' }}>
                        <div className="absolute inset-0 opacity-20">
                            <svg className="w-full h-full" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="320" cy="60" r="50" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.4" />
                                <circle cx="320" cy="60" r="30" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.3" />
                                <rect x="60" y="80" width="80" height="60" rx="4" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.3" />
                                <path d="M0 150 Q100 130 200 150 T400 150" stroke="currentColor" strokeWidth="1" fill="none" opacity="0.2" />
                            </svg>
                        </div>
                        <div className="flex items-center justify-between relative z-10 h-full">
                            <div className="flex-1 pr-6">
                                <h3 className="text-3xl font-bold mb-3">{welcomeCard?.title || t('Photo Studio Frontend')}</h3>
                                <p className="text-white/80 mb-3 text-lg">{welcomeCard?.description || t('Share your photo studio portal with clients')}</p>
                                <p className="text-white/70 mb-4 text-sm">{t('Manage appointments, team members, and track your studio performance.')}</p>
                                {frontendUrl && (
                                    <Button onClick={copyToClipboard} variant="secondary" size="sm" className="bg-white/20 hover:bg-white/30 text-white border-white/30">
                                        <SocialLinks icon={welcomeCard?.buttonIcon || 'Copy'} className="h-4 w-4 mr-2" />
                                        {welcomeCard?.buttonText || t('Copy Link')}
                                    </Button>
                                )}
                            </div>
                            {frontendUrl && (
                                <div className="bg-white p-4 rounded-lg shadow-lg">
                                    {qrCodeUrl ? (
                                        <img src={qrCodeUrl} alt="QR Code" className="w-32 h-32" />
                                    ) : (
                                        <div className="w-32 h-32 bg-gray-100 rounded flex items-center justify-center">
                                            <QrCode className="h-12 w-12 text-gray-400" />
                                        </div>
                                    )}
                                    <p className="text-xs text-gray-600 mt-2 text-center font-medium">{t('Scan to view')}</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {stats && (
                        <div className="grid grid-cols-2 gap-4">
                            <Card className="bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200">
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium text-blue-700">{t('Total Appointments')}</CardTitle>
                                    <CalendarCheck className="h-6 w-6 text-blue-600" />
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-blue-700">{stats.total_appointments}</div>
                                </CardContent>
                            </Card>
                            <Card className="bg-gradient-to-r from-green-50 to-green-100 border-green-200">
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium text-green-700">{t('Team Members')}</CardTitle>
                                    <Users className="h-6 w-6 text-green-600" />
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-green-700">{stats.total_team_members}</div>
                                </CardContent>
                            </Card>
                            <Card className="bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200">
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium text-purple-700">{t('Total Revenue')}</CardTitle>
                                    <DollarSign className="h-6 w-6 text-purple-600" />
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-purple-700">{formatCurrency(stats.total_revenue || 0)}</div>
                                </CardContent>
                            </Card>
                            <Card className="bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200">
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium text-orange-700">{t('Pending Appointments')}</CardTitle>
                                    <Clock className="h-6 w-6 text-orange-600" />
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-orange-700">{stats.pending_appointments}</div>
                                </CardContent>
                            </Card>
                        </div>
                    )}
                </div>

                {/* Charts */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="flex items-center gap-2">
                                <CalendarCheck className="h-5 w-5 text-primary" />
                                {t('Appointment Status')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {appointmentChartWithColors && appointmentChartWithColors.some(i => i.value > 0) ? (
                                <>
                                    <PieChart data={appointmentChartWithColors} dataKey="value" nameKey="name" height={200} donut showTooltip />
                                    <div className="flex flex-wrap justify-center gap-3 mt-4">
                                        {appointmentChartWithColors.map((item, i) => (
                                            <div key={i} className="flex items-center gap-1.5">
                                                <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }} />
                                                <span className="text-sm text-gray-600">{t(item.name)}</span>
                                            </div>
                                        ))}
                                    </div>
                                </>
                            ) : (
                                <PieChart data={[{ name: t('No Data'), value: 1, color: '#e5e7eb' }]} dataKey="value" nameKey="name" height={200} donut showTooltip={false} showLegend={false} />
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="flex items-center gap-2">
                                <DollarSign className="h-5 w-5 text-green-600" />
                                {t('Payment Status')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {paymentChartWithColors && paymentChartWithColors.some(i => i.value > 0) ? (
                                <>
                                    <PieChart data={paymentChartWithColors} dataKey="value" nameKey="name" height={200} donut showTooltip />
                                    <div className="flex flex-wrap justify-center gap-3 mt-4">
                                        {paymentChartWithColors.map((item, i) => (
                                            <div key={i} className="flex items-center gap-1.5">
                                                <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }} />
                                                <span className="text-sm text-gray-600">{t(item.name)}</span>
                                            </div>
                                        ))}
                                    </div>
                                </>
                            ) : (
                                <PieChart data={[{ name: t('No Data'), value: 1, color: '#e5e7eb' }]} dataKey="value" nameKey="name" height={200} donut showTooltip={false} showLegend={false} />
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Activity */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Clock className="h-5 w-5 text-primary" />
                                {t('Recent Appointments')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recentAppointments && recentAppointments.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {recentAppointments.map((appt) => (
                                        <div key={appt.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div className="flex-1">
                                                <h4 className="font-medium text-sm text-gray-900">{appt.name}</h4>
                                                <p className="text-xs text-gray-600 mt-1">{appt.service?.name}</p>
                                                <p className="text-xs text-gray-500">{formatDate(appt.booking_start_date)}</p>
                                            </div>
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                                appt.status === 'completed' ? 'bg-green-100 text-green-800' :
                                                appt.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-blue-100 text-blue-800'
                                            }`}>
                                                {t(appt.status?.charAt(0).toUpperCase() + appt.status?.slice(1))}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <CalendarCheck className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm font-medium">{t('No recent appointments')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="h-5 w-5 text-primary" />
                                {t('Team Members')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recentTeamMembers && recentTeamMembers.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {recentTeamMembers.map((member) => (
                                        <div key={member.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div className="flex-1">
                                                <h4 className="font-medium text-sm text-gray-900">{member.user?.name}</h4>
                                                <p className="text-xs text-gray-600 mt-1">{member.designation}</p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-xs text-gray-500">{formatCurrency(member.rate_per_hour)}/hr</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <Users className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm font-medium">{t('No team members')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
