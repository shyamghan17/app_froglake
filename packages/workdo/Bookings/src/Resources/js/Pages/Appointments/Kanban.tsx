import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { getImagePath } from '@/utils/helpers';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { List, Calendar, Plus, Edit, Trash2, MoreVertical, Clock, User } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import KanbanBoard, { KanbanTask, KanbanColumn } from '@/components/kanban-board';
import { formatDate } from '@/utils/helpers';
import { AppointmentDialog } from './components/AppointmentDialog';

interface Appointment {
    id: number;
    appointment_number: string;
    date: string;
    start_time: string;
    end_time: string;
    status: string;
    staff_id: number;
    customer_id: number;
    item_id?: number;
    package_id?: number;
    payment_status: string;
    created_at?: string;
    customer?: { first_name: string; last_name: string };
    staff?: { name: string; avatar?: string };
    item?: { name: string };
    package?: { name: string };
}

interface KanbanProps {
    appointments: Appointment[];
    items?: any[];
    packages?: any[];
    users?: any[];
    customers?: any[];
    auth: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Kanban() {
    const { t } = useTranslation();
    const { appointments, items = [], packages = [], users = [], customers = [], auth } = usePage<KanbanProps>().props;
    const [appointmentDialog, setAppointmentDialog] = useState({ open: false, mode: 'create' as 'create' | 'edit' | 'view', appointment: null as Appointment | null });

    // Debug log for dialog state changes
    const handleSetAppointmentDialog = (newState: typeof appointmentDialog) => {
        setAppointmentDialog(newState);
    };

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.appointments.destroy',
        defaultMessage: t('Are you sure you want to delete this appointment?')
    });

    const columns: KanbanColumn[] = [
        { id: 'pending', title: 'Pending', color: '#f59e0b' },
        { id: 'confirmed', title: 'Confirmed', color: '#3b82f6' },
        { id: 'completed', title: 'Completed', color: '#10b981' },
        { id: 'cancelled', title: 'Cancelled', color: '#ef4444' },
    ];

    const tasksByStatus = appointments
        .sort((a, b) => new Date(b.created_at || b.date).getTime() - new Date(a.created_at || a.date).getTime())
        .reduce((acc, appointment) => {
        const task: KanbanTask = {
            id: appointment.id,
            title: appointment.appointment_number,
            description: `${(appointment.customer?.first_name + ' ' + appointment.customer?.last_name) || 'No Customer'} - ${appointment.start_time}-${appointment.end_time}`,
            priority: appointment.status === 'completed' ? 'low' :
                     appointment.status === 'confirmed' ? 'medium' : 'high',
            assigned_to: appointment.staff ? { name: appointment.staff.name } : null,
            due_date: appointment.date,
            appointment: appointment
        };

        if (!acc[appointment.status]) {
            acc[appointment.status] = [];
        }
        acc[appointment.status].push(task);
        return acc;
    }, {} as Record<string, KanbanTask[]>);

    const handleMove = (taskId: number, fromStatus: string, toStatus: string) => {
        router.patch(route('bookings.appointments.update-status', taskId), {
            status: toStatus
        }, {
            preserveState: false,
            preserveScroll: true,
            onError: () => {
                router.reload();
            }
        });
    };

   

    const getPaymentStatusBadge = (status: string) => {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            paid: 'bg-green-100 text-green-800',
            failed: 'bg-red-100 text-red-800',
            refunded: 'bg-purple-100 text-purple-800',
        };
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800'}`}>
                {status ? status.charAt(0).toUpperCase() + status.slice(1) : '-'}
            </span>
        );
    };

    const TaskCard = ({ task }: { task: KanbanTask }) => {
        const appointment = task.appointment as Appointment;
        const isOverdue = task.due_date && new Date(task.due_date) < new Date();

        const handleDragStart = (e: React.DragEvent) => {
            e.dataTransfer.setData('application/json', JSON.stringify({ taskId: task.id }));
            e.dataTransfer.effectAllowed = 'move';
        };

        return (
            <div
                className="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-2 hover:shadow-md transition-all cursor-move select-none group"
                draggable={true}
                onDragStart={handleDragStart}
            >
                {/* Header with title and actions */}
                <div className="flex items-start justify-between mb-2">
                    <h4 className="font-medium text-sm text-gray-900 leading-tight pr-2 flex-1">{task.title}</h4>
                    {(auth.user?.permissions?.includes('view-booking-appointments') ||
                      auth.user?.permissions?.includes('edit-booking-appointments') ||
                      auth.user?.permissions?.includes('delete-booking-appointments')) && (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="sm" className="h-6 w-6 p-0 opacity-0 group-hover:opacity-100 shrink-0">
                                    <MoreVertical className="h-3 w-3" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                {auth.user?.permissions?.includes('edit-booking-appointments') && (
                                    <DropdownMenuItem onClick={(e) => {
                                        e.stopPropagation();
                                        handleSetAppointmentDialog({ open: true, mode: 'edit', appointment: appointment });
                                    }}>
                                        <Edit className="h-3 w-3 mr-2" />
                                        {t('Edit')}
                                    </DropdownMenuItem>
                                )}
                                {auth.user?.permissions?.includes('delete-booking-appointments') && (
                                    <DropdownMenuItem onClick={() => openDeleteDialog(task.id)} className="text-red-600 hover:!text-red-600 focus:text-red-600">
                                        <Trash2 className="h-3 w-3 mr-2" />
                                        {t('Delete')}
                                    </DropdownMenuItem>
                                )}
                            </DropdownMenuContent>
                        </DropdownMenu>
                    )}
                </div>
 

                {/* Customer and service info */}
                <div className="space-y-1 mb-3">
                    <div className="flex items-center gap-2 text-xs text-gray-600">
                        <User className="h-3 w-3" />
                        <span>{(appointment.customer?.first_name + ' ' + appointment.customer?.last_name) || 'No Customer'}</span>
                    </div>
                    
                    <div className="flex items-center gap-2 text-xs text-gray-600">
                        <Clock className="h-3 w-3" />
                        <span>{appointment.start_time} - {appointment.end_time}</span>
                    </div>

                    {(appointment.item || appointment.package) && (
                        <p className="text-xs text-gray-600">
                            <span className="font-medium">{t('Service')}:</span>{' '}
                            {appointment.item?.name || appointment.package?.name}
                        </p>
                    )}
                </div>

                {/* Footer with staff and date */}
                <div className="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                    {/* Staff member */}
                    <div className="flex items-center">
                        {task.assigned_to ? (
                            <TooltipProvider>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger>
                                        <div className="h-8 w-8 rounded-full border-2 border-white overflow-hidden bg-gray-100 flex items-center justify-center">
                                            {appointment.staff?.avatar ? (
                                                <img
                                                    src={getImagePath(appointment.staff.avatar)}
                                                    alt={appointment.staff.name}
                                                    className="h-full w-full object-cover"
                                                />
                                            ) : (
                                                <User className="h-3 w-3 text-gray-400" />
                                            )}
                                        </div>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{task.assigned_to.name}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        ) : (
                            <span className="text-xs text-gray-400">{t('Unassigned')}</span>
                        )}
                    </div>

                    {/* Date */}
                    {task.due_date && (
                        <div className="flex items-center space-x-1 text-xs text-gray-500">
                            <Calendar className="h-3 w-3" />
                            <span>{formatDate(task.due_date)}</span>
                        </div>
                    )}
                </div>
            </div>
        );
    };

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('Appointments'), url: route('bookings.appointments.index')},
                    {label: t('Kanban')}
                ]}
                pageTitle={t('Appointments Kanban')}
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
                                <Button size="sm" variant="outline" onClick={() => router.visit(route('bookings.appointments.calendar'))}>
                                    <Calendar className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Calendar View')}</p>
                            </TooltipContent>
                        </Tooltip>
                        {auth.user?.permissions?.includes('create-booking-appointments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => handleSetAppointmentDialog({ open: true, mode: 'create', appointment: null })}>
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
                <Head title={t('Appointments Kanban')} />

                <KanbanBoard
                    tasks={tasksByStatus}
                    columns={columns}
                    onMove={handleMove}
                    taskCard={TaskCard}
                />

                <AppointmentDialog
                    mode={appointmentDialog.mode}
                    open={appointmentDialog.open}
                    onOpenChange={(open) => {
                        handleSetAppointmentDialog(prev => ({ ...prev, open }));
                    }}
                    appointment={appointmentDialog.appointment}
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