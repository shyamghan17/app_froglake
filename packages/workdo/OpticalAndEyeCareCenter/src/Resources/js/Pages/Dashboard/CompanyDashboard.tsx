import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Eye, Users, ShoppingCart, Calendar, Stethoscope, Plus, TrendingUp, Clock, DollarSign, AlertTriangle, Activity } from 'lucide-react';
import { PieChart, LineChart } from '@/components/charts';
import { formatCurrency, formatDate } from '@/utils/helpers';

interface CompanyDashboardProps {
    stats: {
        total_patients: number;
        total_doctors: number;
        total_orders: number;
        total_appointments: number;
        total_prescriptions: number;
        draft_orders: number;
        paid_orders: number;
        pending_appointments: number;
        completed_appointments: number;
        cancelled_appointments: number;
        total_revenue: number;
        monthly_revenue: number;
    };
    appointmentStatus?: Array<{ name: string; value: number; color: string }>;
    orderStatus?: Array<{ name: string; value: number; color: string }>;
    monthlyStats?: Array<{ month: string; appointments: number; orders: number; revenue: number }>;
    recentAppointments?: Array<{
        id: number;
        patient_name: string;
        doctor_name: string;
        appointment_date: string;
        appointment_time: string;
        status: string;
    }>;
    recentOrders?: Array<{
        id: number;
        patient_name: string;
        order_number: string;
        total_amount: number;
        status: string;
        created_at: string;
    }>;
    topDoctors?: Array<{
        name: string;
        total_appointments: number;
        completed_appointments: number;
        specialization: string;
    }>;
}

export default function CompanyDashboard() {
    const { t } = useTranslation();
    const { stats, appointmentStatus = [], orderStatus = [], monthlyStats = [], recentAppointments = [], recentOrders = [], topDoctors = [] } = usePage<CompanyDashboardProps>().props;

    const getStatusColor = (status: string) => {
        switch (status.toLowerCase()) {
            case 'completed': case 'paid': return 'bg-green-100 text-green-800';
            case 'pending': case 'draft': return 'bg-yellow-100 text-yellow-800';
            case 'cancelled': return 'bg-red-100 text-red-800';
            case 'confirmed': return 'bg-blue-100 text-blue-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const StatCard = ({ title, value, subtitle, color = "blue", icon: Icon, onClick }: any) => {
        const colorClasses = {
            blue: "bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200",
            green: "bg-gradient-to-r from-green-50 to-green-100 border-green-200",
            purple: "bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200",
            orange: "bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200",
            red: "bg-gradient-to-r from-red-50 to-red-100 border-red-200"
        };
        const textColors = {
            blue: "text-blue-700",
            green: "text-green-700",
            purple: "text-purple-700",
            orange: "text-orange-700",
            red: "text-red-700"
        };
        return (
            <Card className={`relative overflow-hidden ${colorClasses[color as keyof typeof colorClasses]} ${onClick ? 'cursor-pointer hover:shadow-lg transition-shadow' : ''}`} onClick={onClick}>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle className={`text-sm font-medium ${textColors[color as keyof typeof textColors]}`}>{title}</CardTitle>
                    {Icon && <Icon className={`h-8 w-8 ${textColors[color as keyof typeof textColors]} opacity-80`} />}
                </CardHeader>
                <CardContent>
                    <div className={`text-2xl font-bold ${textColors[color as keyof typeof textColors]}`}>{value}</div>
                    {subtitle && (
                        <p className={`text-xs ${textColors[color as keyof typeof textColors]} opacity-80 mt-1`}>{subtitle}</p>
                    )}
                </CardContent>
            </Card>
        );
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t('Optical & Eye Care Dashboard') }]}
            pageTitle={t('Optical & Eye Care Dashboard')}
        >
            <Head title={t('Optical & Eye Care Dashboard')} />

            <div className="space-y-6">
                {/* Main Stats */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <StatCard
                        title={t('Total Patients')}
                        value={stats.total_patients}
                        subtitle={t('Registered patients')}
                        color="blue"
                        icon={Users}
                        onClick={() => router.get(route('optical-and-eye-care-center.eye-patients.index'))}
                    />
                    <StatCard
                        title={t('Total Doctors')}
                        value={stats.total_doctors}
                        subtitle={t('Active doctors')}
                        color="green"
                        icon={Stethoscope}
                        onClick={() => router.get(route('optical-and-eye-care-center.optical-doctors.index'))}
                    />
                    <StatCard
                        title={t('Total Orders')}
                        value={stats.total_orders}
                        subtitle={`${stats.draft_orders} draft, ${stats.paid_orders} paid`}
                        color="purple"
                        icon={ShoppingCart}
                        onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.index'))}
                    />
                    <StatCard
                        title={t('Appointments')}
                        value={stats.total_appointments}
                        subtitle={`${stats.pending_appointments} pending`}
                        color="orange"
                        icon={Calendar}
                        onClick={() => router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'))}
                    />
                    <StatCard
                        title={t('Prescriptions')}
                        value={stats.total_prescriptions}
                        subtitle={t('Total prescriptions')}
                        color="red"
                        icon={Eye}
                        onClick={() => router.get(route('optical-and-eye-care-center.eye-test-prescriptions.index'))}
                    />
                </div>

                {/* Charts */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <Calendar className="h-5 w-5" />
                                {t('Appointment Status')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {appointmentStatus.length > 0 && appointmentStatus.some(item => item.value > 0) ? (
                                <PieChart
                                    data={appointmentStatus.filter(item => item.value > 0)}
                                    dataKey="value"
                                    nameKey="name"
                                    height={250}
                                    donut={true}
                                    showTooltip={true}
                                    showLegend={true}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-[250px] text-gray-500">
                                    <p className="text-sm">{t('No appointment data')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <ShoppingCart className="h-5 w-5" />
                                {t('Order Status')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {orderStatus.length > 0 && orderStatus.some(item => item.value > 0) ? (
                                <PieChart
                                    data={orderStatus.filter(item => item.value > 0)}
                                    dataKey="value"
                                    nameKey="name"
                                    height={250}
                                    donut={true}
                                    showTooltip={true}
                                    showLegend={true}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-[250px] text-gray-500">
                                    <p className="text-sm">{t('No order data')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Monthly Trends */}
                {monthlyStats.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <TrendingUp className="h-5 w-5" />
                                {t('Monthly Trends')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <LineChart
                                data={monthlyStats}
                                height={300}
                                showTooltip={true}
                                showGrid={true}
                                lines={[
                                    { dataKey: 'appointments', color: '#f97316', name: t('Appointments') },
                                    { dataKey: 'orders', color: '#8b5cf6', name: t('Orders') }
                                ]}
                                xAxisKey="month"
                                showLegend={true}
                            />
                        </CardContent>
                    </Card>
                )}

                {/* Recent Activity */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <Clock className="h-5 w-5" />
                                {t('Recent Appointments')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recentAppointments.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {recentAppointments.slice(0, 5).map((appointment) => (
                                        <div key={appointment.id} className="flex items-start justify-between border-b pb-3 last:border-0">
                                            <div className="flex-1">
                                                <p className="font-medium text-sm">{appointment.patient_name}</p>
                                                <p className="text-xs text-gray-500">{appointment.doctor_name}</p>
                                                <p className="text-xs text-gray-400 mt-1">{appointment.appointment_date} {appointment.appointment_time}</p>
                                            </div>
                                            <Badge className={getStatusColor(appointment.status)}>
                                                {appointment.status}
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <Calendar className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm">{t('No recent appointments')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <ShoppingCart className="h-5 w-5" />
                                {t('Recent Orders')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recentOrders.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {recentOrders.slice(0, 5).map((order) => (
                                        <div key={order.id} className="flex items-start justify-between border-b pb-3 last:border-0">
                                            <div className="flex-1">
                                                <p className="font-medium text-sm">{order.order_number}</p>
                                                <p className="text-xs text-gray-500">{order.patient_name}</p>
                                                <p className="text-xs text-gray-600 mt-1 font-semibold">{formatCurrency(order.total_amount)}</p>
                                            </div>
                                            <Badge className={getStatusColor(order.status)}>
                                                {order.status}
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <ShoppingCart className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm">{t('No recent orders')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}
