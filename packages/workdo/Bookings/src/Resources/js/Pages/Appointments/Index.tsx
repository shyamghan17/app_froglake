import { useState, useMemo } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit, Trash2, CalendarDays, Calendar, Kanban, Eye, CheckCircle, CreditCard } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { FilterButton } from '@/components/ui/filter-button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import NoRecordsFound from '@/components/no-records-found';
import { formatDate, formatTime } from '@/utils/helpers';
import { Dialog } from '@/components/ui/dialog';
import { AppointmentDialog } from './components/AppointmentDialog';
import Payment from './Payment';
import { Appointment, AppointmentsIndexProps } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { appointments, items, packages, users, customers, auth } = usePage<AppointmentsIndexProps>().props;
    const urlParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [filters, setFilters] = useState({
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || '',
        payment_status: urlParams.get('payment_status') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const [showFilters, setShowFilters] = useState(false);
    const [dialogMode, setDialogMode] = useState<'create' | 'edit' | 'view' | null>(null);
    const [selectedAppointment, setSelectedAppointment] = useState<Appointment | null>(null);
    const [showPaymentModal, setShowPaymentModal] = useState(false);
    const [paymentAppointment, setPaymentAppointment] = useState<Appointment | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.appointments.destroy',
        defaultMessage: t('Are you sure you want to delete this appointment?')
    });

    const handleCompleteAppointment = (appointmentId: number) => {
        router.put(route('bookings.appointments.complete', appointmentId), {}, {
            preserveState: true,
            onSuccess: () => router.reload()
        });
    };

    const handleFilter = () => {
        router.get(route('bookings.appointments.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bookings.appointments.index'), { ...filters, per_page: perPage, sort: field, direction }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ search: '', status: '', payment_status: '' });
        router.get(route('bookings.appointments.index'), { per_page: perPage });
    };

    const getStatusBadge = (status: string) => {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-blue-100 text-blue-800',
            completed: 'bg-green-100 text-green-800',
        };
        return (
            <span className={`px-2 py-1 rounded-full text-sm font-normal ${statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800'}`}>
                {status ? status.charAt(0).toUpperCase() + status.slice(1) : '-'}
            </span>
        );
    };

    const getPaymentStatusBadge = (status: string) => {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            paid: 'bg-green-100 text-green-800',
            failed: 'bg-red-100 text-red-800',
            refunded: 'bg-purple-100 text-purple-800',
        };
        return (
            <span className={`px-2 py-1 rounded-full text-sm font-normal ${statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800'}`}>
                {status ? status.charAt(0).toUpperCase() + status.slice(1) : '-'}
            </span>
        );
    };

    const tableColumns = [
        {
            key: 'appointment_number',
            header: t('Appointment Number'),
            sortable: true,
            render: (value: string, appointment: Appointment) => (
                <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => {
                    setSelectedAppointment(appointment);
                    setDialogMode('view');
                }}>{value}</span>
            )
        },
        {
            key: 'date',
            header: t('Date'),
            sortable: true,
            render: (value: string) => formatDate(value)
        },
        {
            key: 'customer_id',
            header: t('Customer'),
            sortable: true,
            render: (value: number, appointment: Appointment) => `${appointment.customer?.first_name || ''} ${appointment.customer?.last_name || ''}`.trim() || '-'
        },

        {
            key: 'start_time',
            header: t('Time'),
            render: (value: string, appointment: Appointment) => `${formatTime(appointment.start_time)} - ${formatTime(appointment.end_time)}`
        },

        {
            key: 'payment',
            header: t('Payment Method'),
            sortable: true,
            render: (value: string) => value ? t(value).charAt(0).toUpperCase() + t(value).slice(1) : '-'
        },
        {
            key: 'created_at',
            header: t('Created At'),
            sortable: true,
            render: (value: string) => formatDate(value)
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: true,
            render: (value: string) => getStatusBadge(value)
        },
        {
            key: 'payment_status',
            header: t('Payment Status'),
            sortable: true,
            render: (value: string) => getPaymentStatusBadge(value)

        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-booking-appointments', 'delete-booking-appointments'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, appointment: Appointment) => (
                <div className="flex gap-1">
                    <TooltipProvider>

                        {auth.user?.permissions?.includes('edit-booking-appointments') && appointment.payment === 'Manually' && !appointment.has_payment_entry && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                        onClick={() => {
                                            setPaymentAppointment(appointment);
                                            setShowPaymentModal(true);
                                        }}
                                    >
                                        <CreditCard className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Process Payment')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-booking-appointments') && appointment.payment_status === 'paid' && appointment.status !== 'completed' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => handleCompleteAppointment(appointment.id)} className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700">
                                        <CheckCircle className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Completed')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button variant="ghost" size="sm" onClick={() => {
                                    setSelectedAppointment(appointment);
                                    setDialogMode('view');
                                }} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                    <Eye className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('View')}</p>
                            </TooltipContent>
                        </Tooltip>
                        {auth.user?.permissions?.includes('edit-booking-appointments') && appointment.status === 'pending' && !appointment.has_payment_entry && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => {
                                        setSelectedAppointment(appointment);
                                        setDialogMode('edit');
                                    }} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-booking-appointments') && !appointment.has_payment_entry && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(appointment.id)}
                                        className="h-8 w-8 p-0 text-destructive hover:text-destructive"
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
            )
        }] : [])
    ];

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Bookings'), url: route('bookings.dashboard') },
                    { label: t('Appointments') }
                ]}
                pageTitle={t('Manage Appointments')}
                pageActions={
                    <div className="flex gap-2">
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
                                    <p>{t('Create')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </div>
                }
            >
                <Head title={t('Appointments')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.search}
                                    onChange={(value) => setFilters({ ...filters, search: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search appointments...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">

                                <PerPageSelector
                                    routeName="bookings.appointments.index"
                                    filters={filters}
                                />
                                <div className="relative">
                                    <FilterButton
                                        showFilters={showFilters}
                                        onToggle={() => setShowFilters(!showFilters)}
                                    />
                                    {(() => {
                                        const activeFilters = [filters.status, filters.payment_status].filter(Boolean).length;
                                        return activeFilters > 0 && (
                                            <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                                {activeFilters}
                                            </span>
                                        );
                                    })()}
                                </div>
                            </div>
                        </div>
                    </CardContent>

                    {showFilters && (
                        <CardContent className="p-6 bg-blue-50/30 border-b">
                            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                    <Select value={filters.status} onValueChange={(value) => setFilters({ ...filters, status: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by status')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                                            <SelectItem value="confirmed">{t('Confirmed')}</SelectItem>
                                            <SelectItem value="completed">{t('Completed')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Status')}</label>
                                    <Select value={filters.payment_status} onValueChange={(value) => setFilters({ ...filters, payment_status: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by payment status')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                                            <SelectItem value="paid">{t('Paid')}</SelectItem>
                                            <SelectItem value="failed">{t('Failed')}</SelectItem>
                                            <SelectItem value="refunded">{t('Refunded')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="flex items-end gap-2">
                                    <Button onClick={handleFilter} size="sm">{t('Apply')}</Button>
                                    <Button variant="outline" onClick={clearFilters} size="sm">{t('Clear')}</Button>
                                </div>
                            </div>
                        </CardContent>
                    )}

                    <CardContent className="p-0">
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                                <DataTable
                                    key={`appointments-table-${appointments.data.length}`}
                                    data={appointments.data}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    className="rounded-none"
                                    emptyState={
                                        <NoRecordsFound
                                            icon={CalendarDays}
                                            title={t('No appointments found')}
                                            description={t('Get started by scheduling your first appointment.')}
                                            hasFilters={!!(filters.search || filters.status || filters.payment_status)}
                                            onClearFilters={clearFilters}
                                            createPermission="create-booking-appointments"
                                            onCreateClick={() => setDialogMode('create')}
                                            createButtonText={t('Create Appointment')}
                                            className="h-auto"
                                        />
                                    }
                                />
                            </div>
                        </div>
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={appointments}
                            routeName="bookings.appointments.index"
                            filters={{ ...filters, per_page: perPage, sort: sortField, direction: sortDirection }}
                        />
                    </CardContent>
                </Card>

                <AppointmentDialog
                    mode={dialogMode || 'create'}
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

                <Dialog open={showPaymentModal} onOpenChange={setShowPaymentModal}>
                    {paymentAppointment && (
                        <Payment
                            appointment={paymentAppointment}
                            onSuccess={() => {
                                setShowPaymentModal(false);
                                setPaymentAppointment(null);
                                router.reload();
                            }}
                        />
                    )}
                </Dialog>

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