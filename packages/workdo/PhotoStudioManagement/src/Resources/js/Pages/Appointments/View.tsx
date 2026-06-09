import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { useTranslation } from 'react-i18next';
import { CalendarCheck, User, Mail, Phone, Users, CalendarDays, Briefcase, DollarSign } from 'lucide-react';
import { ViewAppointmentProps } from './types';
import { formatDateTime, formatCurrency, getImagePath } from '@/utils/helpers';

const statusClass: Record<string, string> = {
    pending:   'bg-yellow-100 text-yellow-800',
    scheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const paymentClass: Record<string, string> = {
    pending:   'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
};

export default function View({ appointment, teamMembers }: ViewAppointmentProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="mb-4">
                <DialogTitle className="flex items-center gap-2">
                    <CalendarCheck className="h-4 w-4" />
                    {t('Appointment Details')}
                </DialogTitle>
            </DialogHeader>

            <div className="space-y-4">

                {/* Appointment Number Header Card */}
                <Card>
                    <CardContent className="px-4 py-4">
                        <div className="relative text-center">
                            <div className="absolute top-0 right-0 flex items-start gap-2">
                                <div className="text-center">
                                    <span className={`text-[12px] px-2 py-1 rounded-full ${statusClass[appointment.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                        {appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                                    </span>
                                    <div className="text-[10px] text-gray-400 mt-0.5">{t('Status')}</div>
                                </div>
                                <div className="text-center">
                                    <span className={`text-[12px] px-2 py-1 rounded-full ${paymentClass[appointment.payment_status] ?? 'bg-gray-100 text-gray-800'}`}>
                                        {appointment.payment_status.charAt(0).toUpperCase() + appointment.payment_status.slice(1)}
                                    </span>
                                    <div className="text-[10px] text-gray-400 mt-0.5">{t('Payment')}</div>
                                </div>
                            </div>
                            <div className="p-2 bg-blue-100 rounded-lg mb-2 mx-auto w-fit">
                                <CalendarCheck className="h-4 w-4 text-blue-600" />
                            </div>
                            <div className="font-bold text-lg text-gray-900">{appointment.appointment_number || '-'}</div>
                            <div className="text-xs text-gray-500 mt-0.5">{t('Appointment No.')}</div>
                        </div>
                    </CardContent>
                </Card>

                {/* Contact Information */}
                <Card>
                    <CardHeader className="pb-2 pt-4 px-4">
                        <span className="text-sm font-semibold text-gray-700">{t('Contact Information')}</span>
                    </CardHeader>
                    <CardContent className="px-4 pb-4">
                        <div className="grid grid-cols-3 gap-4">
                            <InfoTile icon={<User className="h-4 w-4 text-purple-600" />} iconBg="bg-purple-100" value={appointment.name} label={t('Name')} />
                            <InfoTile icon={<Mail className="h-4 w-4 text-pink-600" />} iconBg="bg-pink-100" value={appointment.email} label={t('Email')} />
                            <InfoTile icon={<Phone className="h-4 w-4 text-green-600" />} iconBg="bg-green-100" value={appointment.mobile_no || '-'} label={t('Mobile No.')} />
                        </div>
                    </CardContent>
                </Card>

                {/* Booking & Service Details merged */}
                <Card>
                    <CardHeader className="pb-2 pt-4 px-4">
                        <span className="text-sm font-semibold text-gray-700">{t('Booking & Service Details')}</span>
                    </CardHeader>
                    <CardContent className="px-4 pb-4">
                        <div className="grid grid-cols-2 gap-4">
                            <InfoTile icon={<CalendarDays className="h-4 w-4 text-blue-600" />} iconBg="bg-blue-100" value={formatDateTime(appointment.booking_start_date)} label={t('Start Date')} />
                            <InfoTile icon={<CalendarDays className="h-4 w-4 text-red-500" />} iconBg="bg-red-100" value={formatDateTime(appointment.booking_end_date)} label={t('End Date')} />
                            <InfoTile icon={<Briefcase className="h-4 w-4 text-indigo-600" />} iconBg="bg-indigo-100" value={appointment.service?.name || '-'} label={t('Service')} />
                            <InfoTile icon={<DollarSign className="h-4 w-4 text-emerald-600" />} iconBg="bg-emerald-100" value={formatCurrency(appointment.price)} label={t('Price')} valueClass="text-emerald-600 font-bold" />
                        </div>
                    </CardContent>
                </Card>

                {/* Team Members */}
                <Card>
                    <CardHeader className="pb-2 pt-4 px-4">
                        <span className="text-sm font-semibold text-gray-700">{t('Team Members')}</span>
                    </CardHeader>
                    <CardContent className="px-4 pb-4">
                        {appointment.team_member_ids?.length > 0 ? (
                            <div className="flex flex-wrap gap-3">
                                {appointment.team_member_ids.map((id) => {
                                    const member = teamMembers.find(m => m.id.toString() === id.toString());
                                    if (!member) return null;
                                    return (
                                        <div key={member.id} className="flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2">
                                            <div className="w-7 h-7 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center shrink-0">
                                                {member.user?.avatar ? (
                                                    <img src={getImagePath(member.user.avatar)} alt={member.user?.name} className="w-full h-full object-cover" />
                                                ) : (
                                                    <User className="w-4 h-4 text-gray-400" />
                                                )}
                                            </div>
                                            <span className="text-sm font-medium text-gray-700">{member.user?.name || '-'}</span>
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <span className="text-sm text-gray-400">-</span>
                        )}
                    </CardContent>
                </Card>

            </div>
        </DialogContent>
    );
}

function InfoTile({ icon, iconBg, value, label, valueClass = '' }: {
    icon: React.ReactNode;
    iconBg: string;
    value: string;
    label: string;
    valueClass?: string;
}) {
    return (
        <div className="text-center border border-gray-200 rounded-xl p-3">
            <div className={`p-1.5 ${iconBg} rounded-lg mb-2 mx-auto w-fit`}>
                {icon}
            </div>
            <div className={`font-medium text-sm text-gray-800 truncate px-1 ${valueClass}`}>{value}</div>
            <div className="text-xs text-gray-500 mt-0.5">{label}</div>
        </div>
    );
}
