import { useState, useMemo, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { AppointmentDialog } from './components/AppointmentDialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from '@/components/ui/badge';
import { List, Kanban, Plus, Clock, Users, Eye, Edit, Trash2, CalendarDays } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import CalendarView from '@/components/calendar-view';
import { formatDate, formatTime } from '@/utils/helpers';

interface Appointment {
    id: number;
    appointment_number: string;
    date: string;
    item_id?: number;
    package_id?: number;
    staff_id: number;
    customer_id: number;
    start_time: string;
    end_time: string;
    status: string;
    customer?: { first_name: string; last_name: string };
    staff?: { name: string };
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}


interface Item {
    id: number;
    name: string;
}

interface Package {
    id: number;
    name: string;
}

interface CalendarProps {
    appointments: Appointment[];
    items: Item[];
    packages: Package[];
    users: User[];
    customers: Customer[];
    auth: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Calendar() {
    const { t } = useTranslation();
    const { appointments, items, packages, users, customers, auth } = usePage<CalendarProps>().props;
    const [dialogMode, setDialogMode] = useState<'create' | 'edit' | 'view' | null>(null);
    const [selectedAppointment, setSelectedAppointment] = useState<Appointment | null>(null);
    const [selectedDate, setSelectedDate] = useState<string>(new Date().toISOString().split('T')[0]);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.appointments.destroy',
        defaultMessage: t('Are you sure you want to delete this appointment?')
    });

    const getStatusColor = (status: string) => {
        const colors = {
            pending: '#f59e0b',
            confirmed: '#3b82f6',
            completed: '#10b981',
            cancelled: '#ef4444',
        };
        return colors[status as keyof typeof colors] || '#6b7280';
    };

    const getStatusBadge = (status: string) => {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-blue-100 text-blue-800',
            completed: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800',
        };
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800'}`}>
                {status ? status.charAt(0).toUpperCase() + status.slice(1) : '-'}
            </span>
        );
    };

    const calendarEvents = useMemo(() => {
        return appointments.map(appointment => ({
            id: appointment.id,
            title: `${(appointment.customer?.first_name + ' '+ appointment.customer?.last_name) || 'No customer'} - ${appointment.start_time}`,
            startDate: appointment.date,
            endDate: appointment.date,
            time: appointment.start_time,
            description: `Staff: ${appointment.staff?.name || 'N/A'}`,
            type: appointment.status,
            color: getStatusColor(appointment.status),
            attendees: [((appointment.customer?.first_name + ' '+ appointment.customer?.last_name)) || 'No Customer', appointment.staff?.name || 'No Staff']
        }));
    }, [appointments]);

    const filteredAppointments = useMemo(() => {
        return appointments.filter(appointment => appointment.date === selectedDate);
    }, [appointments, selectedDate]);

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('Appointments'), url: route('bookings.appointments.index')},
                    {label: t('Calendar')}
                ]}
                pageTitle={t('Appointments Calendar')}
                pageActions={
                    <div className="flex gap-2">
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button size="sm" variant="outline" onClick={() => router.visit(route('bookings.appointments.index'))}>
                                    <List className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('List View')}</p>
                            </TooltipContent>
                        </Tooltip>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button size="sm" variant="outline" onClick={() => router.visit(route('bookings.appointments.kanban'))}>
                                    <Kanban className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Kanban View')}</p>
                            </TooltipContent>
                        </Tooltip>
                        {auth.user?.permissions?.includes('create-booking-appointments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => setDialogMode('create')}>
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Create Appointment')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </div>
                }
            >
                <Head title={t('Appointments Calendar')} />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-2">
                        <CalendarView 
                            events={calendarEvents} 
                            onEventClick={(event) => {
                                const appointment = appointments.find(apt => apt.id === event.id);
                                if (appointment) {
                                    setSelectedAppointment(appointment);
                                    setDialogMode('view');
                                }
                            }}
                            onDateClick={(date) => setSelectedDate(date.toISOString().split('T')[0])}
                        />
                    </div>

                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <Clock className="h-4 w-4" />
                                    {t('Appointments for')} {formatDate(selectedDate)}
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="max-h-[75vh] overflow-y-auto">
                                {filteredAppointments.length === 0 ? (
                                    <div className="text-center py-8 text-gray-500">
                                        <CalendarDays className="h-12 w-12 mx-auto mb-2 opacity-50" />
                                        <p>{t('No appointments for this date')}</p>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {filteredAppointments.map(appointment => (
                                            <div key={appointment.id} className="border rounded-lg p-4">
                                                <div className="flex items-start justify-between mb-2">
                                                    <h4 className="font-medium">{appointment.appointment_number}</h4>
                                                    {getStatusBadge(appointment.status)}
                                                </div>

                                                <div className="flex items-center justify-between mb-2">
                                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                                        <Clock className="h-4 w-4" />
                                                        <span>{formatDate(appointment.date)} - {appointment.start_time}-{appointment.end_time}</span>
                                                    </div>
                                                    <div className="flex items-center gap-1">
                                                        <TooltipProvider>
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button 
                                                                        variant="ghost" 
                                                                        size="sm" 
                                                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                                                        onClick={() => {
                                                                            setSelectedAppointment(appointment);
                                                                            setDialogMode('view');
                                                                        }}
                                                                    >
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('View')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                            {auth.user?.permissions?.includes('edit-booking-appointments') && (
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button 
                                                                            variant="ghost" 
                                                                            size="sm" 
                                                                            className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                                                            onClick={() => {
                                                                                setSelectedAppointment(appointment);
                                                                                setDialogMode('edit');
                                                                            }}
                                                                        >
                                                                            <Edit className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Edit')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                            )}
                                                            {auth.user?.permissions?.includes('delete-booking-appointments') && (
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button 
                                                                            variant="ghost" 
                                                                            size="sm" 
                                                                            className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                                                            onClick={() => openDeleteDialog(appointment.id)}
                                                                        >
                                                                            <Trash2 className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Delete')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                            )}
                                                        </TooltipProvider>
                                                    </div>
                                                </div>

                                                <p className="text-sm text-gray-600 mb-3">
                                                    {t('Customer')}: {(appointment.customer?.first_name + ' '+ appointment.customer?.last_name) || 'N/A'}
                                                </p>

                                                <div className="flex items-center gap-2 text-sm">
                                                    <Users className="h-4 w-4 text-gray-400" />
                                                    <span className="text-gray-600">
                                                        {t('Staff')}: {appointment.staff?.name || 'N/A'}
                                                    </span>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
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
                    items={items}
                    packages={packages}
                    users={users}
                    customers={customers}
                    onSuccess={() => router.reload()}
                />

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Appointment')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}