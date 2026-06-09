import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { MoreVertical, Edit as EditIcon, Calendar, DollarSign, Trash2 } from "lucide-react";
import KanbanBoard from '@/components/kanban-board';
import EditBeautyBooking from '../BeautyBookings/Edit';
import { formatDate, formatCurrency } from '@/utils/helpers';

interface BeautyBooking {
    id: number;
    name: string;
    email: string;
    service: number;
    date: string;
    time_slot: string;
    person: number;
    service_price: number;
    phone_number: string;
    gender: string;
    status?: string;
}

interface BookingOrderProps {
    beautybookings: {
        data: BeautyBooking[];
    };
    beautyservices: Array<{
        id: number;
        name: string;
        price: number;
    }>;
    auth: {
        user: {
            permissions: string[];
        };
    };
}

export default function Index() {
    const { t } = useTranslation();
    const { beautybookings, beautyservices, auth } = usePage<BookingOrderProps>().props;

    const [editingBooking, setEditingBooking] = useState<BeautyBooking | null>(null);
    const [deletingBooking, setDeletingBooking] = useState<BeautyBooking | null>(null);

    useFlashMessages();

    const handleMove = (bookingId: number, fromStatus: string, toStatus: string) => {
        router.post(route('beauty-spa-management.booking-order.update-status'), {
            booking_id: bookingId,
            stage_id: parseInt(toStatus)
        }, {
            preserveState: true,
            onSuccess: () => {
                router.reload({ only: ['beautybookings'] });
            }
        });
    };

    const handleDelete = (booking: BeautyBooking) => {
        setDeletingBooking(booking);
    };

    const confirmDelete = () => {
        if (deletingBooking) {
            router.delete(route('beauty-spa-management.beauty-bookings.destroy', deletingBooking.id), {
                preserveState: true,
                onSuccess: () => {
                    setDeletingBooking(null);
                    router.reload({ only: ['beautybookings'] });
                }
            });
        }
    };

    const getKanbanData = () => {
        const columns = [
            { id: '0', title: t('Draft'), color: '#f59e0b' },
            { id: '1', title: t('Open'), color: '#3b82f6' },
            { id: '2', title: t('Invoiced'), color: '#8b5cf6' },
            { id: '3', title: t('Closed'), color: '#10b981' }
        ];

        const tasksByStatus = {};
        columns.forEach(col => {
            tasksByStatus[col.id] = [];
        });

        beautybookings?.data?.forEach(booking => {
            const stageId = booking.stage_id?.toString() || '0';
            if (tasksByStatus[stageId]) {
                tasksByStatus[stageId].push({
                    id: booking.id,
                    title: booking.name,
                    description: booking.email,
                    status: stageId,
                    due_date: booking.date,
                    booking: booking
                });
            }
        });

        return { columns, tasks: tasksByStatus };
    };

    const BookingCard = ({ task }: { task: any }) => {
        const booking = task.booking;
        const service = beautyservices?.find(s => s.id.toString() === booking.service?.toString());

        const handleDragStart = (e: React.DragEvent) => {
            e.dataTransfer.setData('application/json', JSON.stringify({ taskId: task.id, fromStatus: task.status }));
            e.dataTransfer.effectAllowed = 'move';
        };

        return (
            <div
                className="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-2 hover:shadow-md transition-all cursor-move select-none group"
                draggable={true}
                onDragStart={handleDragStart}
            >
                <div className="flex items-start justify-between mb-2">
                    <h4 className="font-medium text-sm text-gray-900 leading-tight pr-2">
                        {task.title}
                    </h4>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm" className="h-8 w-8 p-0 opacity-0 group-hover:opacity-100">
                                <MoreVertical className="h-3 w-3" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {auth.user?.permissions?.includes('edit-beauty-bookings') && (
                                <DropdownMenuItem onClick={() => setEditingBooking(booking)}>
                                    <EditIcon className="h-3 w-3 mr-2" />
                                    {t('Edit')}
                                </DropdownMenuItem>
                            )}
                            {auth.user?.permissions?.includes('delete-beauty-bookings') && (
                                <DropdownMenuItem
                                    onClick={() => handleDelete(booking)}
                                    className="text-red-600 hover:text-red-700"
                                >
                                    <Trash2 className="h-3 w-3 mr-2" />
                                    {t('Delete')}
                                </DropdownMenuItem>
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>



                <div className="flex items-center justify-between mb-3">
                    <Tooltip>
                        <TooltipTrigger>
                            <div className="flex items-center space-x-1 text-sm font-medium px-2 py-1 rounded bg-blue-50 text-blue-600">
                                <Calendar className="h-3 w-3" />
                                <span>{formatDate(booking.date)}</span>
                            </div>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{t('Booking Date')}</p>
                        </TooltipContent>
                    </Tooltip>

                    <div className="flex items-center gap-1">
                        <Tooltip>
                            <TooltipTrigger>
                                <div className="flex items-center space-x-1 text-xs text-purple-600 font-medium bg-purple-50 px-2 py-1 rounded">
                                    <span>{booking.gender}</span>
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Gender')}</p>
                            </TooltipContent>
                        </Tooltip>
                    </div>
                </div>

                <div className="flex items-center justify-between">
                    <div className="text-xs text-gray-500">
                        <div>{booking.time_slot}</div>
                    </div>

                    {booking.service_price && (
                        <div className="flex items-center space-x-1 text-xs text-green-600 font-medium">
                            <DollarSign className="h-3 w-3" />
                            <span>{formatCurrency(booking.service_price)}</span>
                        </div>
                    )}
                </div>
            </div>
        );
    };

    const { columns, tasks } = getKanbanData();

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('Booking Order') }
            ]}
            pageTitle={t('Manage Booking Order')}
        >
            <Head title={t('Booking Order')} />

            <TooltipProvider>
                <KanbanBoard
                    tasks={tasks}
                    columns={columns}
                    onMove={handleMove}
                    taskCard={BookingCard}
                    kanbanActions={null}
                />
            </TooltipProvider>

            <Dialog open={!!editingBooking} onOpenChange={() => setEditingBooking(null)}>
                {editingBooking && (
                    <EditBeautyBooking
                        booking={editingBooking}
                        onSuccess={() => {
                            setEditingBooking(null);
                            router.reload({ only: ['beautybookings'] });
                        }}
                    />
                )}
            </Dialog>

            <Dialog open={!!deletingBooking} onOpenChange={() => setDeletingBooking(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{t('Delete Booking')}</DialogTitle>
                        <DialogDescription>
                            {t('Are you sure you want to delete the booking for')} <strong>{deletingBooking?.name}</strong>? {t('This action cannot be undone.')}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setDeletingBooking(null)}>
                            {t('Cancel')}
                        </Button>
                        <Button variant="destructive" onClick={confirmDelete}>
                            {t('Delete')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}