import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Eye, Users, Calendar, FileText, Clock, Plus, Activity } from 'lucide-react';

interface DoctorDashboardProps {
    stats: {
        my_patients: number;
        my_appointments: number;
        my_prescriptions: number;
        pending_appointments: number;
        today_appointments: number;
    };
    todayAppointments?: Array<{
        id: number;
        patient_name: string;
        appointment_date: string;
        appointment_time: string;
        status: string;
    }>;
    recentPrescriptions?: Array<{
        id: number;
        patient_name: string;
        prescription_date: string;
        diagnosis: string;
    }>;
}

export default function DoctorDashboard() {
    const { t } = useTranslation();
    const { stats, todayAppointments = [], recentPrescriptions = [] } = usePage<DoctorDashboardProps>().props;

    const getStatusColor = (status: string) => {
        const colors: any = {"0": "bg-blue-100 text-blue-800", "1": "bg-green-100 text-green-800", "2": "bg-purple-100 text-purple-800", "3": "bg-red-100 text-red-800"};
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status: string) => {
        const labels: any = {"0": "Scheduled", "1": "Confirmed", "2": "Completed", "3": "Cancelled"};
        return labels[status] || status;
    };

    const StatCard = ({ title, value, subtitle, color = "blue", icon: Icon }: any) => {
        const colorClasses = {
            blue: "bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200",
            green: "bg-gradient-to-r from-green-50 to-green-100 border-green-200",
            purple: "bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200",
            orange: "bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200"
        };
        const textColors = {
            blue: "text-blue-700",
            green: "text-green-700",
            purple: "text-purple-700",
            orange: "text-orange-700"
        };
        return (
            <Card className={`relative overflow-hidden ${colorClasses[color as keyof typeof colorClasses]}`}>
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
            breadcrumbs={[{ label: t('Doctor Dashboard') }]}
            pageTitle={t('Doctor Dashboard')}
        >
            <Head title={t('Doctor Dashboard')} />

            <div className="space-y-6">
                {/* Stats */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div onClick={() => router.get(route('optical-and-eye-care-center.eye-patients.index'))} className="cursor-pointer">
                        <StatCard
                            title={t('My Patients')}
                            value={stats.my_patients}
                            subtitle="Total patients"
                            color="blue"
                            icon={Users}
                        />
                    </div>
                    <div onClick={() => router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'))} className="cursor-pointer">
                        <StatCard
                            title={t('My Appointments')}
                            value={stats.my_appointments}
                            subtitle={`${stats.today_appointments} today`}
                            color="green"
                            icon={Calendar}
                        />
                    </div>
                    <div onClick={() => router.get(route('optical-and-eye-care-center.eye-test-prescriptions.index'))} className="cursor-pointer">
                        <StatCard
                            title={t('My Prescriptions')}
                            value={stats.my_prescriptions}
                            subtitle="Total prescriptions"
                            color="purple"
                            icon={Eye}
                        />
                    </div>
                    <div onClick={() => router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'))} className="cursor-pointer">
                        <StatCard
                            title={t('Pending')}
                            value={stats.pending_appointments}
                            subtitle="Pending appointments"
                            color="orange"
                            icon={FileText}
                        />
                    </div>
                </div>

                {/* Today's Schedule & Recent Activity */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Today's Appointments */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <Clock className="h-5 w-5" />
                                {t("Today's Appointments")}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {todayAppointments.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {todayAppointments.map((appointment) => (
                                        <div key={appointment.id} className="flex items-center justify-between border-b pb-3 last:border-0">
                                            <div className="flex-1">
                                                <p className="font-medium text-sm">{appointment.patient_name}</p>
                                                <p className="text-xs text-gray-500 mt-1">{appointment.appointment_time}</p>
                                            </div>
                                            <Badge className={getStatusColor(appointment.status)}>
                                                {t(getStatusLabel(appointment.status))}
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <Calendar className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm">{t('No appointments today')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Recent Prescriptions */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg flex items-center gap-2">
                                <Eye className="h-5 w-5" />
                                {t('Recent Prescriptions')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recentPrescriptions.length > 0 ? (
                                <div className="space-y-3 max-h-80 overflow-y-auto">
                                    {recentPrescriptions.map((prescription) => (
                                        <div key={prescription.id} className="border-b pb-3 last:border-0">
                                            <p className="font-medium text-sm">{prescription.patient_name}</p>
                                            <p className="text-xs text-gray-500 mt-1">{prescription.diagnosis}</p>
                                            <p className="text-xs text-gray-400 mt-1">{prescription.prescription_date}</p>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-gray-500">
                                    <Eye className="h-12 w-12 mx-auto mb-3 opacity-30" />
                                    <p className="text-sm">{t('No recent prescriptions')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
