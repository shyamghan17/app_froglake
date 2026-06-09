import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Card, CardContent } from '@/components/ui/card';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import { CalendarCheck, User, Mail, Phone, Briefcase, CalendarDays, DollarSign, Users } from 'lucide-react';
import { AssignTeamMemberProps } from './types';
import { formatDateTime, formatCurrency } from '@/utils/helpers';

const statusClass: Record<string, string> = {
    pending:   'bg-yellow-100 text-yellow-800',
    scheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

export default function AssignTeamMember({ appointment, onClose }: Omit<AssignTeamMemberProps, 'teamMembers'>) {
    const { teamMembers } = usePage<any>().props;
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        team_member_ids: (appointment.team_member_ids?.map(id => id.toString()) as string[]) || [],
    });

    useEffect(() => {
        setData({
            team_member_ids: (appointment.team_member_ids?.map(id => id.toString()) as string[]) || [],
        });
    }, [appointment]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.appointments.assign-team-members', appointment.id), {
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle className="flex items-center gap-2 mb-3">
                    <Users className="h-5 w-5" />
                    {t('Assign Team Members')}
                </DialogTitle>
            </DialogHeader>

            <form onSubmit={submit} className="space-y-4">
                {/* Appointment Details */}
                <Card>
                    <CardContent className="p-4 space-y-3">
                        <div className="flex items-center gap-2 mb-1">
                            <CalendarCheck className="h-4 w-4 text-primary" />
                            <span className="font-semibold text-sm">{appointment.appointment_number}</span>
                            <span className={`ml-auto px-2 py-0.5 rounded-full text-xs font-medium ${statusClass[appointment.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                {appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                            </span>
                        </div>

                        <div className="grid grid-cols-2 gap-3 text-sm">
                            <div className="flex items-center gap-2">
                                <User className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Name')}:</span>
                                <span className="font-medium truncate">{appointment.name}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <Mail className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Email')}:</span>
                                <span className="font-medium truncate">{appointment.email}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <Phone className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Mobile')}:</span>
                                <span className="font-medium">{appointment.mobile_no}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <Briefcase className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Service')}:</span>
                                <span className="font-medium truncate">{appointment.service?.name || '-'}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <CalendarDays className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Start')}:</span>
                                <span className="font-medium">{formatDateTime(appointment.booking_start_date)}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <CalendarDays className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('End')}:</span>
                                <span className="font-medium">{formatDateTime(appointment.booking_end_date)}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <DollarSign className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="text-muted-foreground">{t('Price')}:</span>
                                <span className="font-medium text-green-600">{formatCurrency(appointment.price)}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Team Member Assignment */}
                <div>
                    <Label>{t('Team Members')}</Label>
                    <MultiSelectEnhanced
                        options={teamMembers.map(m => ({ value: m.id.toString(), label: m.user?.name || '-' }))}
                        value={data.team_member_ids}
                        onValueChange={value => setData('team_member_ids', value)}
                        placeholder={t('Select Team Members...')}
                        searchable
                    />
                    <InputError message={errors.team_member_ids} />
                </div>

                <div className="flex justify-end gap-2 pt-2">
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Assigning...') : t('Assign')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
