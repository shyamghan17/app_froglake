import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
// import { Dialog } from '@/components/ui/dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, ShoppingCart, Printer, FileText, Download } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import NoRecordsFound from '@/components/no-records-found';
import { EyewearOrder, EyewearOrdersIndexProps, EyewearOrderFilters } from './types';
import { formatDate, formatCurrency } from '@/utils/helpers';
// import View from './View';

export default function Index() {
    const { t } = useTranslation();
    const { orders, patients, warehouses, auth } = usePage<EyewearOrdersIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<EyewearOrderFilters>({
        patient_id: urlParams.get('patient_id') || '',
        payment_status: urlParams.get('payment_status') || '',
        search: urlParams.get('search') || '',
        date_range: urlParams.get('date_range') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters] = useState(false);
    // const [viewOrder, setViewOrder] = useState<EyewearOrder | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'optical-and-eye-care-center.eyewear-orders.destroy',
        defaultMessage: t('Are you sure you want to delete this eyewear order?')
    });

    const handleFilter = () => {
        router.get(route('optical-and-eye-care-center.eyewear-orders.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('optical-and-eye-care-center.eyewear-orders.index'), {...filters, per_page: perPage, sort: field, direction, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            patient_id: '',
            payment_status: '',
            search: '',
            date_range: '',
        });
        router.get(route('optical-and-eye-care-center.eyewear-orders.index'), {per_page: perPage, view: viewMode});
    };

    const getPaymentStatusBadge = (status: string) => {
        const variants: Record<string, string> = {
            draft: 'bg-yellow-100 text-yellow-800',
            paid: 'bg-green-100 text-green-800',
        };
        return (
            <span className={`px-3 py-1 rounded-full text-sm font-medium ${variants[status] || 'bg-gray-100 text-gray-800'}`}>
                {t(status.charAt(0).toUpperCase() + status.slice(1))}
            </span>
        );
    };

    const tableColumns = [
        {
            key: 'order_number',
            header: t('Order Number'),
            sortable: true,
            render: (value: string, order: EyewearOrder) => (
                <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.show', order.id))}>{value}</span>
            )
        },
        {
            key: 'order_date',
            header: t('Order Date'),
            sortable: true,
            render: (value: string) => formatDate(value)
        },
        {
            key: 'patient_id',
            header: t('Patient'),
            sortable: false,
            render: (_: any, order: EyewearOrder) => order.patient?.patient_name || '-'
        },
        {
            key: 'total_amount',
            header: t('Total Amount'),
            sortable: true,
            render: (value: number) => formatCurrency(value)
        },
        {
            key: 'payment_status',
            header: t('Payment Status'),
            sortable: true,
            render: (value: string) => getPaymentStatusBadge(value)
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-eyewear-orders', 'edit-eyewear-orders', 'delete-eyewear-orders', 'print-eyewear-orders'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, order: EyewearOrder) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('print-eyewear-orders') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => window.open(route('optical-and-eye-care-center.eyewear-orders.print', order.id) + '?download=pdf', '_blank')} className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700">
                                        <Download className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Download PDF')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-eyewear-orders') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route("optical-and-eye-care-center.eyewear-orders.show", order.id))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {order.payment_status === 'draft' && auth.user?.permissions?.includes('post-eyewear-orders') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.post(route('optical-and-eye-care-center.eyewear-orders.post', order.id))} className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700">
                                        <FileText className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Post Order')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-eyewear-orders') && order.payment_status === 'draft' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.edit', order.id))} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-eyewear-orders') && order.payment_status === 'draft' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(order.id)}
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
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Optical & Eye Care Center'), url: route('optical-and-eye-care-center.dashboard')},
                {label: t('Eyewear Orders')}
            ]}
            pageTitle={t('Manage Eyewear Orders')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-eyewear-orders') && (
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button size="sm" onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.create'))}>
                                    <Plus className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Create')}</p>
                            </TooltipContent>
                        </Tooltip>
                    )}
                </TooltipProvider>
            }
        >
            <Head title={t('Eyewear Orders')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Orders...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="optical-and-eye-care-center.eyewear-orders.index"
                                filters={{...filters, per_page: perPage}}
                            />
                            <PerPageSelector
                                routeName="optical-and-eye-care-center.eyewear-orders.index"
                                filters={{...filters, view: viewMode}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.patient_id, filters.payment_status].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                        <div className="flex items-center gap-4">
                            <div className="flex-1">
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Patient')}</label>
                                <Select value={filters.patient_id} onValueChange={(value) => setFilters({...filters, patient_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Patient')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {patients?.map((patient) => (
                                            <SelectItem key={patient.id} value={patient.id.toString()}>{patient.patient_name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex-1">
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Status')}</label>
                                <Select value={filters.payment_status} onValueChange={(value) => setFilters({...filters, payment_status: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Payment')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">{t('Draft')}</SelectItem>
                                        <SelectItem value="paid">{t('Paid')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex items-end gap-2 pt-7">
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
                                    data={orders?.data || []}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    className="rounded-none"
                                    emptyState={
                                        <NoRecordsFound
                                            icon={ShoppingCart}
                                            title={t('No Orders found')}
                                            description={t('Get started by creating your first order.')}
                                            hasFilters={!!(filters.search || filters.patient_id || filters.payment_status)}
                                            onClearFilters={clearFilters}
                                            createPermission="create-eyewear-orders"
                                            onCreateClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.create'))}
                                            createButtonText={t('Create Order')}
                                            className="h-auto"
                                        />
                                    }
                                />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {orders?.data && orders.data.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                    {orders.data.map((order) => (
                                        <Card key={order.id} className="p-0 hover:shadow-lg transition-all duration-200 relative overflow-hidden flex flex-col h-full min-w-0">
                                            <div className="p-4 bg-gradient-to-r from-primary/5 to-transparent border-b flex-shrink-0">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 bg-primary/10 rounded-lg">
                                                        <ShoppingCart className="h-5 w-5 text-primary" />
                                                    </div>
                                                    <div className="min-w-0 flex-1">
                                                        <h3 className="font-semibold text-sm text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.show', order.id))}>{order.order_number}</h3>
                                                        <div className="mt-1">{getPaymentStatusBadge(order.payment_status)}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="p-4 flex-1 min-h-0">
                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Patient')}</p>
                                                        <p className="font-medium text-xs truncate">{order.patient?.patient_name}</p>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Order Date')}</p>
                                                        <p className="font-medium text-xs">{formatDate(order.order_date)}</p>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Delivery Date')}</p>
                                                        <p className="font-medium text-xs">{order.delivery_date ? formatDate(order.delivery_date) : '-'}</p>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Balance Due')}</p>
                                                        <p className="font-medium text-xs text-blue-600">{formatCurrency(order.balance_amount)}</p>
                                                    </div>
                                                </div>

                                                <div className="bg-gray-50 rounded-lg p-3">
                                                        <div className="grid grid-cols-2 gap-2 text-xs">
                                                            <div className="flex justify-between">
                                                                <span className="text-gray-600">{t('Subtotal')}:</span>
                                                                <span className="font-medium">{formatCurrency(order.subtotal)}</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span className="text-gray-600">{t('Tax')}:</span>
                                                                <span className="font-medium">{formatCurrency(order.tax_amount)}</span>
                                                            </div>
                                                        </div>
                                                        <div className="border-t mt-2 pt-2">
                                                            <div className="flex justify-between items-center">
                                                                <span className="text-sm font-semibold text-gray-900">{t('Total Amount')}</span>
                                                                <span className="text-lg font-bold text-gray-900">{formatCurrency(order.total_amount)}</span>
                                                            </div>
                                                            <div className="flex justify-between items-center mt-1">
                                                                <span className="text-xs text-gray-600">{t('Balance Due')}</span>
                                                                <span className="text-sm font-semibold text-blue-600">{formatCurrency(order.balance_amount)}</span>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>

                                            <div className="flex justify-end gap-1 p-3 border-t bg-gray-50/50 flex-shrink-0 mt-auto">
                                                <TooltipProvider>
                                                    {auth.user?.permissions?.includes('print-eyewear-orders') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => window.open(route('optical-and-eye-care-center.eyewear-orders.print', order.id) + '?download=pdf', '_blank')} className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700">
                                                                    <Download className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('Download PDF')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('view-eyewear-orders') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => router.visit(route("optical-and-eye-care-center.eyewear-orders.show", order.id))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                    <Eye className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {order.payment_status === 'draft' && (
                                                        <>
                                                                {auth.user?.permissions?.includes('post-eyewear-orders') && (
                                                                    <Tooltip delayDuration={300}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => router.post(route('optical-and-eye-care-center.eyewear-orders.post', order.id))} className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700">
                                                                                <FileText className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('Post Order')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                )}
                                                                {auth.user?.permissions?.includes('edit-eyewear-orders') && (
                                                                    <Tooltip delayDuration={300}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.edit', order.id))} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                                <EditIcon className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                )}
                                                                {auth.user?.permissions?.includes('delete-eyewear-orders') && (
                                                                    <Tooltip delayDuration={300}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(order.id)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                                                                <Trash2 className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                )}
                                                            </>
                                                        )}
                                                    </TooltipProvider>
                                            </div>
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={ShoppingCart}
                                    title={t('No Orders found')}
                                    description={t('Get started by creating your first order.')}
                                    hasFilters={!!(filters.search || filters.patient_id || filters.payment_status)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-eyewear-orders"
                                    onCreateClick={() => router.get(route('optical-and-eye-care-center.eyewear-orders.create'))}
                                    createButtonText={t('Create Order')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={orders || { data: [], links: [], meta: {} }}
                        routeName="optical-and-eye-care-center.eyewear-orders.index"
                        filters={{...filters, per_page: perPage, view: viewMode}}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Eyewear Order')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

        </AuthenticatedLayout>
    );
}
