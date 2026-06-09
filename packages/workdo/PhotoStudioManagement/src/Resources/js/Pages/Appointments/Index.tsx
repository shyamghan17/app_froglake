import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Dialog } from '@/components/ui/dialog';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, CalendarCheck, ChevronDown, CreditCard, Users, User as UserIcon } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from '@/components/ui/pagination';
import { SearchInput } from '@/components/ui/search-input';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import NoRecordsFound from '@/components/no-records-found';
import { formatDateTime, formatCurrency, getImagePath } from '@/utils/helpers';
import { PhotoStudioAppointment, AppointmentsIndexProps, AppointmentFilters, AppointmentModalState } from './types';
import Create from './Create';
import Edit from './Edit';
import View from './View';
import AssignTeamMember from './AssignTeamMember';
import CreatePayment from '../AppointmentPayments/Create';

const statusClass: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    scheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const paymentClass: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
};

export default function Index() {
    const { t } = useTranslation();
    const { appointments, auth, services, teamMembers } = usePage<AppointmentsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<AppointmentFilters>({
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || '',
        payment_status: urlParams.get('payment_status') || '',
        service_id: urlParams.get('service_id') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<AppointmentModalState>({ isOpen: false, mode: '', data: null });
    const [paymentModalOpen, setPaymentModalOpen] = useState(false);
    const [selectedForPayment, setSelectedForPayment] = useState<PhotoStudioAppointment | null>(null);
    const [assignModalOpen, setAssignModalOpen] = useState(false);
    const [selectedForAssign, setSelectedForAssign] = useState<PhotoStudioAppointment | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.appointments.destroy',
        defaultMessage: t('Are you sure you want to delete this appointment?'),
    });

    const openModal = (mode: 'add' | 'edit' | 'view', data: PhotoStudioAppointment | null = null) =>
        setModalState({ isOpen: true, mode, data });

    const closeModal = () => setModalState({ isOpen: false, mode: '', data: null });

    const openPaymentModal = (appointment: PhotoStudioAppointment) => {
        setSelectedForPayment(appointment);
        setPaymentModalOpen(true);
    };

    const closePaymentModal = () => {
        setSelectedForPayment(null);
        setPaymentModalOpen(false);
    };

    const openAssignModal = (appointment: PhotoStudioAppointment) => {
        setSelectedForAssign(appointment);
        setAssignModalOpen(true);
    };

    const closeAssignModal = () => {
        setSelectedForAssign(null);
        setAssignModalOpen(false);
    };

    const handleFilter = () => {
        router.get(route('photo-studio-management.appointments.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('photo-studio-management.appointments.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, { preserveState: true, replace: true });
    };

    const updateStatus = (id: number, status: string) => {
        router.patch(route('photo-studio-management.appointments.status', id), { status }, { preserveScroll: true });
    };

    const clearFilters = () => {
        setFilters({ search: '', status: '', payment_status: '', service_id: '' });
        router.get(route('photo-studio-management.appointments.index'), { per_page: perPage, view: viewMode });
    };

    const tableColumns = [
        {
            key: 'appointment_number',
            header: t('Appointment No.'),
            sortable: true,
            render: (value: string, row: PhotoStudioAppointment) =>
                auth.user?.permissions?.includes('view-photo-studio-appointments') ? (
                    <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => openModal('view', row)}>{value}</span>
                ) : value,
        },
        { key: 'name', header: t('Name'), sortable: true },
        { key: 'email', header: t('Email'), sortable: true },
        {
            key: 'service',
            header: t('Service'),
            render: (_: any, row: PhotoStudioAppointment) => row.service?.name || '-',
        },
        {
            key: 'booking_start_date',
            header: t('Start Date'),
            render: (_: any, row: PhotoStudioAppointment) => (
                <span className="whitespace-nowrap">{formatDateTime(row.booking_start_date)}</span>
            ),
        },
        {
            key: 'team_member_ids',
            header: t('Team Members'),
            sortable: false,
            render: (_: any, row: PhotoStudioAppointment) => {
                if (!row.team_member_ids || row.team_member_ids.length === 0) return '-';
                return (
                    <div className="flex -space-x-2">
                        <TooltipProvider>
                            {row.team_member_ids.slice(0, 3).map((id) => {
                                const member = teamMembers.find((m) => m.id.toString() === id);
                                if (!member) return null;
                                return (
                                    <Tooltip key={member.id} delayDuration={0}>
                                        <TooltipTrigger>
                                            <div className="w-8 h-8 rounded-full border-2 border-background overflow-hidden bg-gray-100 flex items-center justify-center">
                                                {member.user?.avatar ? (
                                                    <img src={getImagePath(member.user.avatar)} alt={member.user?.name} className="w-full h-full object-cover" />
                                                ) : (
                                                    <UserIcon className="w-4 h-4 text-gray-400" />
                                                )}
                                            </div>
                                        </TooltipTrigger>
                                        <TooltipContent><p>{member.user?.name || '-'}</p></TooltipContent>
                                    </Tooltip>
                                );
                            })}
                            {row.team_member_ids.length > 3 && (
                                <div className="w-8 h-8 rounded-full border-2 border-background bg-gray-100 flex items-center justify-center text-xs font-medium">
                                    +{row.team_member_ids.length - 3}
                                </div>
                            )}
                        </TooltipProvider>
                    </div>
                );
            },
        },
        {
            key: 'price',
            header: t('Price'),
            render: (_: any, row: PhotoStudioAppointment) => formatCurrency(row.price),
        },
        {
            key: 'status',
            header: t('Status'),
            render: (_: any, row: PhotoStudioAppointment) => {
                if (auth.user?.permissions?.includes('edit-photo-studio-appointments')) {
                    return (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" className={`px-2 py-1 rounded-full text-sm h-auto font-medium ${statusClass[row.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                    {row.status.charAt(0).toUpperCase() + row.status.slice(1)} <ChevronDown className="h-3 w-3 ml-1" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                {['pending', 'scheduled', 'completed', 'cancelled'].map(s => (
                                    <DropdownMenuItem key={s} onClick={() => updateStatus(row.id, s)}>{t(s.charAt(0).toUpperCase() + s.slice(1))}</DropdownMenuItem>
                                ))}
                            </DropdownMenuContent>
                        </DropdownMenu>
                    );
                }
                return <span className={`px-2 py-1 rounded-full text-sm font-medium ${statusClass[row.status] ?? 'bg-gray-100 text-gray-800'}`}>{row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span>;
            },
        },
        {
            key: 'payment_status',
            header: t('Payment Status'),
            render: (_: any, row: PhotoStudioAppointment) => (
                <span className={`px-2 py-1 rounded-full text-sm ${paymentClass[row.payment_status] ?? 'bg-gray-100 text-gray-800'}`}>
                    {row.payment_status.charAt(0).toUpperCase() + row.payment_status.slice(1)}
                </span>
            ),
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-photo-studio-appointments', 'edit-photo-studio-appointments', 'delete-photo-studio-appointments', 'create-photo-studio-appointment-payments'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, row: PhotoStudioAppointment) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-photo-studio-team-members') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openAssignModal(row)} className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700">
                                        <Users className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Assign Team Members')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('create-photo-studio-appointment-payments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openPaymentModal(row)} className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700">
                                        <CreditCard className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Payment')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-photo-studio-appointments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('view', row)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('edit-photo-studio-appointments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', row)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('delete-photo-studio-appointments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(row.id)} className="h-8 w-8 p-0 text-red-600 hover:text-red-700">
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            ),
        }] : []),
    ];

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                    { label: t('Appointments') },
                ]}
                pageTitle={t('Manage Appointments')}
                pageActions={
                    <div className="flex gap-2">
                        <TooltipProvider>
                            {auth.user?.permissions?.includes('create-photo-studio-appointments') && (
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button size="sm" onClick={() => openModal('add')}><Plus className="h-4 w-4" /></Button>
                                    </TooltipTrigger>
                                    <TooltipContent><p>{t('Create')}</p></TooltipContent>
                                </Tooltip>
                            )}
                        </TooltipProvider>
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
                                    onChange={value => setFilters({ ...filters, search: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search Appointments...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <ListGridToggle currentView={viewMode} routeName="photo-studio-management.appointments.index" filters={{ ...filters, per_page: perPage }} onViewChange={setViewMode} />
                                <PerPageSelector routeName="photo-studio-management.appointments.index" filters={{ ...filters, view: viewMode }} />
                                <div className="relative">
                                    <FilterButton showFilters={showFilters} onToggle={() => setShowFilters(!showFilters)} />
                                    {[filters.status, filters.payment_status, filters.service_id].filter(Boolean).length > 0 && (
                                        <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            {[filters.status, filters.payment_status, filters.service_id].filter(Boolean).length}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </CardContent>

                    {showFilters && (
                        <CardContent className="p-6 bg-blue-50/30 border-b">
                            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                    <Select value={filters.status} onValueChange={value => setFilters({ ...filters, status: value })}>
                                        <SelectTrigger><SelectValue placeholder={t('Filter by Status')} /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                                            <SelectItem value="scheduled">{t('Scheduled')}</SelectItem>
                                            <SelectItem value="completed">{t('Completed')}</SelectItem>
                                            <SelectItem value="cancelled">{t('Cancelled')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Status')}</label>
                                    <Select value={filters.payment_status} onValueChange={value => setFilters({ ...filters, payment_status: value })}>
                                        <SelectTrigger><SelectValue placeholder={t('Filter by Payment')} /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                                            <SelectItem value="confirmed">{t('Confirmed')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Service')}</label>
                                    <Select value={filters.service_id} onValueChange={value => setFilters({ ...filters, service_id: value })}>
                                        <SelectTrigger><SelectValue placeholder={t('Filter by Service')} /></SelectTrigger>
                                        <SelectContent>
                                            {services?.map(s => (
                                                <SelectItem key={s.id} value={s.id.toString()}>{s.name}</SelectItem>
                                            ))}
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
                        {viewMode === 'list' ? (
                            <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                                <div className="min-w-[800px]">
                                    <DataTable
                                        data={appointments?.data || []}
                                        columns={tableColumns}
                                        onSort={handleSort}
                                        sortKey={sortField}
                                        sortDirection={sortDirection as 'asc' | 'desc'}
                                        className="rounded-none"
                                        emptyState={
                                            <NoRecordsFound
                                                icon={CalendarCheck}
                                                title={t('No Appointments found')}
                                                description={t('Get started by creating your first Appointment.')}
                                                hasFilters={!!(filters.search || filters.status || filters.payment_status || filters.service_id)}
                                                onClearFilters={clearFilters}
                                                createPermission="create-photo-studio-appointments"
                                                onCreateClick={() => openModal('add')}
                                                createButtonText={t('Create Appointment')}
                                                className="h-auto"
                                            />
                                        }
                                    />
                                </div>
                            </div>
                        ) : (
                            <div className="overflow-auto max-h-[70vh] p-6">
                                {appointments?.data?.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
                                        {appointments.data.map((row) => (
                                            <Card key={row.id} className="p-0 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-slate-200 rounded-2xl bg-white">
                                                {/* Header */}
                                                <div className="px-4 pt-4 pb-3 border-b flex items-start justify-between gap-2">
                                                    <div className="min-w-0">
                                                        <h3 className="font-semibold text-sm text-gray-900 truncate">
                                                            {auth.user?.permissions?.includes('view-photo-studio-appointments') ? (
                                                                <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => openModal('view', row)}>
                                                                    {row.appointment_number}
                                                                </span>
                                                            ) : row.appointment_number}
                                                        </h3>
                                                        <p className="text-xs text-muted-foreground truncate mt-0.5">{row.name}</p>
                                                    </div>
                                                    <span className="text-xs text-muted-foreground truncate mt-0.5 ">
                                                        {row.mobile_no || <span className="text-slate-400">—</span>}
                                                    </span>
                                                </div>

                                                {/* Body */}
                                                <div className="p-4 flex-1 space-y-3">
                                                    {/* Email */}
                                                    <div className="flex items-start gap-2 text-xs">
                                                        <span className="text-slate-500 shrink-0 mt-0.5">✉</span>
                                                        <span className="text-slate-500 truncate">{row.email}</span>
                                                    </div>

                                                    <div className="h-px bg-slate-100" />

                                                    {/* Service */}
                                                    <div className="min-w-0">
                                                        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Service')}</p>
                                                        <p className="text-xs font-semibold text-slate-700 truncate">{row.service?.name || '—'}</p>
                                                    </div>

                                                    {/* Start Date */}
                                                    <div className="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                                        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-0.5">{t('Start Date')}</p>
                                                        <p className="text-xs font-semibold text-slate-700">{formatDateTime(row.booking_start_date)}</p>
                                                    </div>

                                                    {/* Price & Team Members */}
                                                    <div className="grid grid-cols-2 gap-3">
                                                        <div className="min-w-0">
                                                            <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Price')}</p>
                                                            <p className="text-xs font-bold text-emerald-600">{formatCurrency(row.price)}</p>
                                                        </div>
                                                        <div className="min-w-0">
                                                        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1.5">{t('Team Members')}</p>
                                                        {row.team_member_ids?.length > 0 ? (
                                                            <div className="flex -space-x-2">
                                                                <TooltipProvider>
                                                                    {row.team_member_ids.slice(0, 4).map((id) => {
                                                                        const member = teamMembers.find((m) => m.id.toString() === id);
                                                                        if (!member) return null;
                                                                        return (
                                                                            <Tooltip key={member.id} delayDuration={0}>
                                                                                <TooltipTrigger>
                                                                                    <div className="w-6 h-6 rounded-full border-2 border-white overflow-hidden bg-slate-200 flex items-center justify-center shadow-sm">
                                                                                        {member.user?.avatar ? (
                                                                                            <img src={getImagePath(member.user.avatar)} alt={member.user?.name} className="w-full h-full object-cover" />
                                                                                        ) : (
                                                                                            <UserIcon className="w-3 h-3 text-slate-400" />
                                                                                        )}
                                                                                    </div>
                                                                                </TooltipTrigger>
                                                                                <TooltipContent><p>{member.user?.name || '-'}</p></TooltipContent>
                                                                            </Tooltip>
                                                                        );
                                                                    })}
                                                                    {row.team_member_ids.length > 4 && (
                                                                        <div className="w-6 h-6 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-semibold text-slate-500 shadow-sm">
                                                                            +{row.team_member_ids.length - 4}
                                                                        </div>
                                                                    )}
                                                                </TooltipProvider>
                                                            </div>
                                                        ) : <span className="text-xs text-slate-400">—</span>}
                                                        </div>
                                                    </div>

                                                    {/* Payment & Status */}
                                                    <div className="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
                                                        <div className="min-w-0">
                                                            <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Payment')}</p>
                                                            <span className={`text-[12px]  px-2 py-1 rounded-full ${paymentClass[row.payment_status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                                {row.payment_status.charAt(0).toUpperCase() + row.payment_status.slice(1)}
                                                            </span>
                                                        </div>
                                                        <div className="min-w-0">
                                                            <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Status')}</p>
                                                            {auth.user?.permissions?.includes('edit-photo-studio-appointments') ? (
                                                                <DropdownMenu>
                                                                    <DropdownMenuTrigger asChild>
                                                                        <Button variant="ghost" className={`px-2 py-1 rounded-full text-[12px] h-auto  ${statusClass[row.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                                            {row.status.charAt(0).toUpperCase() + row.status.slice(1)} <ChevronDown className="h-3 w-3 ml-1" />
                                                                        </Button>
                                                                    </DropdownMenuTrigger>
                                                                    <DropdownMenuContent>
                                                                        {['pending', 'scheduled', 'completed', 'cancelled'].map(s => (
                                                                            <DropdownMenuItem key={s} onClick={() => updateStatus(row.id, s)}>{t(s.charAt(0).toUpperCase() + s.slice(1))}</DropdownMenuItem>
                                                                        ))}
                                                                    </DropdownMenuContent>
                                                                </DropdownMenu>
                                                            ) : (
                                                                <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full ${statusClass[row.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                                    {row.status.charAt(0).toUpperCase() + row.status.slice(1)}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Footer Actions */}
                                                <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 mt-auto">
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('edit-photo-studio-team-members') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openAssignModal(row)} className="h-9 w-9 p-0 text-gray-600 hover:text-gray-700">
                                                                        <Users className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Assign Team Members')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('create-photo-studio-appointment-payments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openPaymentModal(row)} className="h-9 w-9 p-0 text-purple-600 hover:text-purple-700">
                                                                        <CreditCard className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Payment')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('view-photo-studio-appointments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openModal('view', row)} className="h-9 w-9 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('edit-photo-studio-appointments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', row)} className="h-9 w-9 p-0 text-blue-600 hover:text-blue-700">
                                                                        <EditIcon className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('delete-photo-studio-appointments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(row.id)} className="h-9 w-9 p-0 text-red-600 hover:text-red-700">
                                                                        <Trash2 className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                    </TooltipProvider>
                                                </div>
                                            </Card>
                                        ))}
                                    </div>
                                ) : (
                                    <NoRecordsFound
                                        icon={CalendarCheck}
                                        title={t('No Appointments found')}
                                        description={t('Get started by creating your first Appointment.')}
                                        hasFilters={!!(filters.search || filters.status || filters.payment_status || filters.service_id)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-photo-studio-appointments"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Appointment')}
                                    />
                                )}
                            </div>
                        )}
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={appointments || { data: [], links: [], meta: {} }}
                            routeName="photo-studio-management.appointments.index"
                            filters={{ ...filters, per_page: perPage, view: viewMode }}
                        />
                    </CardContent>
                </Card>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Appointment')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />

                <Dialog open={modalState.isOpen && modalState.mode === 'add'} onOpenChange={closeModal}>
                    <Create key={modalState.isOpen ? 'create' : 'closed'} onClose={closeModal} services={services} />
                </Dialog>

                <Dialog open={modalState.isOpen && modalState.mode === 'edit'} onOpenChange={closeModal}>
                    {modalState.data && (
                        <Edit appointment={modalState.data} onClose={closeModal} services={services} />
                    )}
                </Dialog>

                <Dialog open={modalState.isOpen && modalState.mode === 'view'} onOpenChange={closeModal}>
                    {modalState.data && (
                        <View appointment={modalState.data} teamMembers={teamMembers} onClose={closeModal} />
                    )}
                </Dialog>

                <Dialog open={paymentModalOpen} onOpenChange={closePaymentModal}>
                    {selectedForPayment && (
                        <CreatePayment appointment={selectedForPayment} onSuccess={closePaymentModal} />
                    )}
                </Dialog>

                <Dialog open={assignModalOpen} onOpenChange={closeAssignModal}>
                    {selectedForAssign && (
                        <AssignTeamMember appointment={selectedForAssign} onClose={closeAssignModal} />
                    )}
                </Dialog>
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
