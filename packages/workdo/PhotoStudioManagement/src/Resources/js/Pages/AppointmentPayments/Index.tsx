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
import { Trash2, Eye, CreditCard as CreditCardIcon, CheckCircle } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from '@/components/ui/pagination';
import { SearchInput } from '@/components/ui/search-input';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import NoRecordsFound from '@/components/no-records-found';
import { PhotoStudioAppointmentPayment, PaymentsIndexProps, PaymentFilters } from './types';
import { formatDate, formatCurrency } from '@/utils/helpers';
import View from './View';

const paymentClass: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    cleared: 'bg-green-100 text-green-800',
};

export default function Index() {
    const { t } = useTranslation();
    const { payments, auth, services } = usePage<PaymentsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<PaymentFilters>({
        search:         urlParams.get('search') || '',
        payment_status: urlParams.get('payment_status') || '',
        service_id:     urlParams.get('service_id') || '',
        date_range:     urlParams.get('date_range') || '',
    });

    const [perPage]                         = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField]         = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode]           = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters]     = useState(false);
    const [viewingItem, setViewingItem]     = useState<PhotoStudioAppointmentPayment | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.appointment-payments.destroy',
        defaultMessage: t('Are you sure you want to delete this payment?'),
    });

    const handleFilter = () => {
        router.get(route('photo-studio-management.appointment-payments.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('photo-studio-management.appointment-payments.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setFilters({ search: '', payment_status: '', service_id: '', date_range: '' });
        router.get(route('photo-studio-management.appointment-payments.index'), { per_page: perPage, view: viewMode });
    };

    const handleMarkCleared = (paymentId: number) => {
        router.put(route('photo-studio-management.appointment-payments.update-status', paymentId), { payment_status: 'cleared' });
    };

    const tableColumns = [
        {
            key: 'appointment_number',
            header: t('Appointment No.'),
            sortable: true,
            render: (value: string, row: PhotoStudioAppointmentPayment) =>
                auth.user?.permissions?.includes('view-photo-studio-appointment-payments') ? (
                    <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => setViewingItem(row)}>{value}</span>
                ) : value,
        },
        { key: 'customer_name', header: t('Customer Name'), sortable: true },
        { key: 'service_name',  header: t('Service'),       sortable: true },
        {
            key: 'payment_date',
            header: t('Payment Date'),
            sortable: true,
            render: (value: string) => value ? formatDate(value) : '-',
        },
        {
            key: 'amount',
            header: t('Amount'),
            sortable: true,
            render: (value: number) => formatCurrency(value),
        },
        {
            key: 'payment_status',
            header: t('Payment Status'),
            sortable: true,
            render: (value: string) => (
                <span className={`px-2 py-1 rounded-full text-sm ${paymentClass[value] ?? 'bg-gray-100 text-gray-800'}`}>
                    {t(value.charAt(0).toUpperCase() + value.slice(1))}
                </span>
            ),
        },
        { key: 'payment_type', header: t('Payment Type'), sortable: true, render: (value: string) => t(value.charAt(0).toUpperCase() + value.slice(1)) },
        ...(auth.user?.permissions?.some((p: string) => ['view-photo-studio-appointment-payments', 'edit-photo-studio-appointment-payments', 'delete-photo-studio-appointment-payments'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, row: PhotoStudioAppointmentPayment) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('edit-photo-studio-appointment-payments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => handleMarkCleared(row.id)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <CheckCircle className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Mark as Cleared')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-photo-studio-appointment-payments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(row)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('delete-photo-studio-appointment-payments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(row.id)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
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
                    { label: t('Payments') },
                ]}
                pageTitle={t('Manage Payments')}
            >
                <Head title={t('Payments')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.search}
                                    onChange={value => setFilters({ ...filters, search: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search Payments...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <ListGridToggle currentView={viewMode} routeName="photo-studio-management.appointment-payments.index" filters={{ ...filters, per_page: perPage }} onViewChange={setViewMode} />
                                <PerPageSelector routeName="photo-studio-management.appointment-payments.index" filters={{ ...filters, view: viewMode }} />
                                <div className="relative">
                                    <FilterButton showFilters={showFilters} onToggle={() => setShowFilters(!showFilters)} />
                                    {[filters.payment_status, filters.service_id, filters.date_range].filter(Boolean).length > 0 && (
                                        <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            {[filters.payment_status, filters.service_id, filters.date_range].filter(Boolean).length}
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
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Status')}</label>
                                    <Select value={filters.payment_status} onValueChange={value => setFilters({ ...filters, payment_status: value })}>
                                        <SelectTrigger><SelectValue placeholder={t('Filter by Status')} /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">{t('Pending')}</SelectItem>
                                            <SelectItem value="cleared">{t('Cleared')}</SelectItem>
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
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Date Range')}</label>
                                    <DateRangePicker
                                        value={filters.date_range}
                                        onChange={value => setFilters({ ...filters, date_range: value })}
                                        placeholder={t('Select Date Range')}
                                    />
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
                                        data={payments?.data || []}
                                        columns={tableColumns}
                                        onSort={handleSort}
                                        sortKey={sortField}
                                        sortDirection={sortDirection as 'asc' | 'desc'}
                                        className="rounded-none"
                                        emptyState={
                                            <NoRecordsFound
                                                icon={CreditCardIcon}
                                                title={t('No Payments found')}
                                                description={t('payments will appear here.')}
                                                hasFilters={!!(filters.search || filters.payment_status || filters.service_id)}
                                                onClearFilters={clearFilters}
                                                createPermission={null}
                                                onCreateClick={undefined}
                                                createButtonText={undefined}
                                                className="h-auto"
                                            />
                                        }
                                    />
                                </div>
                            </div>
                        ) : (
                            <div className="overflow-auto max-h-[70vh] p-6">
                                {payments?.data?.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
                                        {payments.data.map((row) => (
                                            <Card key={row.id} className="p-0 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-row border border-slate-200 rounded-2xl bg-white">
                                                <div className="w-1 flex-shrink-0 bg-gradient-to-b from-primary via-primary/70 to-primary/30 rounded-l-2xl" />
                                                <div className="flex flex-col flex-1 min-w-0">
                                                {/* Header */}
                                                <div className="px-4 pt-4 pb-3 border-b flex items-start justify-between gap-2">
                                                    <div className="flex items-center gap-3 min-w-0">
                                                        <div className="p-2 bg-primary/10 rounded-xl flex-shrink-0">
                                                            <CreditCardIcon className="w-4 h-4 text-primary" />
                                                        </div>
                                                        <div className="min-w-0">
                                                            <h3 className="font-semibold text-sm text-gray-900 truncate">
                                                                {auth.user?.permissions?.includes('view-photo-studio-appointment-payments') ? (
                                                                    <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => setViewingItem(row)}>
                                                                        {row.appointment_number}
                                                                    </span>
                                                                ) : row.appointment_number}
                                                            </h3>
                                                            <p className="text-xs text-muted-foreground truncate mt-0.5">{row.customer_name}</p>
                                                        </div>
                                                    </div>
                                                    <span className={`text-[10px] font-semibold px-2.5 py-1 rounded-full flex-shrink-0 mt-0.5 ${paymentClass[row.payment_status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                        {t(row.payment_status.charAt(0).toUpperCase() + row.payment_status.slice(1))}
                                                    </span>
                                                </div>

                                                {/* Body */}
                                                <div className="p-4 flex-1 space-y-3">
                                                    {/* Amount */}
                                                    <div className="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100 flex items-center justify-between">
                                                        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500">{t('Amount')}</p>
                                                        <p className="text-xs font-bold text-emerald-600">{formatCurrency(row.amount)}</p>
                                                    </div>

                                                    {/* Service */}
                                                    <div className="min-w-0">
                                                        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Service')}</p>
                                                        <p className="text-xs font-semibold text-slate-700 truncate">{row.service_name}</p>
                                                    </div>

                                                    {/* Type & Date */}
                                                    <div className="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
                                                        <div className="min-w-0">
                                                            <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Type')}</p>
                                                            <p className="text-xs font-semibold text-slate-700">{row.payment_type.charAt(0).toUpperCase() + row.payment_type.slice(1)}</p>
                                                        </div>
                                                        <div className="min-w-0">
                                                            <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">{t('Payment Date')}</p>
                                                            <p className="text-xs font-semibold text-slate-700">{formatDate(row.payment_date)}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Footer Actions */}
                                                <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 mt-auto">
                                                    <TooltipProvider>
                                                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('edit-photo-studio-appointment-payments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => handleMarkCleared(row.id)} className="h-9 w-9 p-0 text-blue-600 hover:text-blue-700">
                                                                        <CheckCircle className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Mark as Cleared')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('view-photo-studio-appointment-payments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(row)} className="h-9 w-9 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {row.payment_status === 'pending' && auth.user?.permissions?.includes('delete-photo-studio-appointment-payments') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(row.id)} className="h-9 w-9 p-0 text-destructive hover:text-destructive">
                                                                        <Trash2 className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                    </TooltipProvider>
                                                </div>
                                                </div>
                                            </Card>
                                        ))}
                                    </div>
                                ) : (
                                    <NoRecordsFound
                                        icon={CreditCardIcon}
                                        title={t('No Payments found')}
                                        description={t('Payments will appear here.')}
                                        hasFilters={!!(filters.search || filters.payment_status || filters.service_id)}
                                        onClearFilters={clearFilters}
                                        createPermission={null}
                                        onCreateClick={undefined}
                                        createButtonText={undefined}
                                    />
                                )}
                            </div>
                        )}
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={payments || { data: [], links: [], meta: {} }}
                            routeName="photo-studio-management.appointment-payments.index"
                            filters={{ ...filters, per_page: perPage, view: viewMode }}
                        />
                    </CardContent>
                </Card>

                <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                    {viewingItem && <View payment={viewingItem} />}
                </Dialog>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Payment')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
