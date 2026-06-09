import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Trash2, Eye, FileText as FileTextIcon } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

import NoRecordsFound from '@/components/no-records-found';
import { formatCurrency } from '@/utils/helpers';

interface RepairInvoice {
    id: number;
    invoice_id: string;
    repair_id: number;
    repair_charge: number;
    total_amount: number;
    due_amount?: number;
    status: string;
    created_at: string;
    repair_order?: {
        product_name: string;
    };
}

interface RepairInvoicesIndexProps {
    repairinvoices: {
        data: RepairInvoice[];
        links: any[];
        meta: any;
    };
    auth: {
        user: {
            permissions: string[];
        };
    };
    repairorderrequests: any[];
}

interface RepairInvoiceFilters {
    invoice_id: string;
    status: string;
}

export default function Index() {
    const { t } = useTranslation();
    const { repairinvoices, auth, repairorderrequests } = usePage<RepairInvoicesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState<RepairInvoiceFilters>({
        invoice_id: urlParams.get('invoice_id') || '',
        status: urlParams.get('status') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');



    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'repair-management-system.repair-invoices.destroy',
        defaultMessage: t('Are you sure you want to delete this invoice?')
    });

    const handleFilter = () => {
        router.get(route('repair-management-system.repair-invoices.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('repair-management-system.repair-invoices.index'), {...filters, per_page: perPage, sort: field, direction, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            invoice_id: '',
            status: '',
        });
        router.get(route('repair-management-system.repair-invoices.index'), {per_page: perPage, view: viewMode});
    };



    const tableColumns = [
        {
            key: 'invoice_id',
            header: t('Invoice'),
            sortable: true,
            render: (value: string, repairinvoice: RepairInvoice) =>
                auth.user?.permissions?.includes('view-repair-invoices') ? (
                    <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => router.get(route('repair-management-system.repair-invoices.show', repairinvoice.id))}>{value}</span>
                ) : (
                    value || '-'
                )
        },
        {
            key: 'repair_order.product_name',
            header: t('Product Name'),
            sortable: false,
            render: (value: any, row: any) => row.repair_order?.product_name || '-'
        },
        {
            key: 'repair_charge',
            header: t('Repair Charge'),
            sortable: true,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'total_amount',
            header: t('Total Amount'),
            sortable: true,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'due_amount',
            header: t('Due Amount'),
            sortable: false,
            render: (value: number) => value ? formatCurrency(value) : formatCurrency(0)
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: any) => {
                const statusMap = {
                    '0': { label: t('Pending'), className: 'px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800' },
                    '1': { label: t('Partially Paid'), className: 'px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800' },
                    '2': { label: t('Paid'), className: 'px-2 py-1 rounded-full text-sm bg-green-100 text-green-800' }
                };
                const statusInfo = statusMap[value as keyof typeof statusMap] || { label: value, className: 'px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-800' };
                return <span className={statusInfo.className}>{statusInfo.label}</span>;
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['delete-repair-invoices'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, repairinvoice: RepairInvoice) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-repair-invoices') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('repair-management-system.repair-invoices.show', repairinvoice.id))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}

                        {auth.user?.permissions?.includes('delete-repair-invoices') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(repairinvoice.id)}
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
                {label: t('Repair')},
                {label: t('Invoices')}
            ]}
            pageTitle={t('Manage Invoices')}

        >
            <Head title={t('Invoices')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.invoice_id}
                                onChange={(value) => setFilters({...filters, invoice_id: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Invoices...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="repair-management-system.repair-invoices.index"
                                filters={{...filters, per_page: perPage}}
                            />
                            <PerPageSelector
                                routeName="repair-management-system.repair-invoices.index"
                                filters={{...filters, view: viewMode}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.status].filter(f => f !== '' && f !== null && f !== undefined).length;
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

                {/* Advanced Filters */}
                {showFilters && (
                    <CardContent className="p-6 bg-blue-50/30 border-b">
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select value={filters.status} onValueChange={(value) => setFilters({...filters, status: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Pending')}</SelectItem>
                                        <SelectItem value="1">{t('Partially Paid')}</SelectItem>
                                        <SelectItem value="2">{t('Paid')}</SelectItem>
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

                {/* Table Content */}
                <CardContent className="p-0">
                    {viewMode === 'list' ? (
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                            <DataTable
                                data={repairinvoices?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={FileTextIcon}
                                        title={t('No Invoices found')}
                                        description={t('Get started by creating your first Invoice.')}
                                        hasFilters={!!(filters.invoice_id || filters.status)}
                                        onClearFilters={clearFilters}

                                        className="h-auto"
                                    />
                                }
                            />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {repairinvoices?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                                    {repairinvoices?.data?.map((repairinvoice) => (
                                        <Card key={repairinvoice.id} className="p-6 hover:shadow-md transition-shadow flex flex-col h-full">
                                            <div className="flex items-center justify-between mb-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 bg-primary/10 rounded-lg">
                                                        <FileTextIcon className="h-5 w-5 text-primary" />
                                                    </div>
                                                    {auth.user?.permissions?.includes('view-repair-invoices') ? (
                                                        <span className="font-medium text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => router.get(route('repair-management-system.repair-invoices.show', repairinvoice.id))}>
                                                            {repairinvoice.invoice_id}
                                                        </span>
                                                    ) : (
                                                        <span className="font-medium">
                                                            {repairinvoice.invoice_id}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="space-y-4 mb-6 flex-grow">
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="text-sm">
                                                        <p className="text-muted-foreground mb-1">{t('Repair Charge')}</p>
                                                        <p className="font-medium">{repairinvoice.repair_charge ? formatCurrency(repairinvoice.repair_charge) : '-'}</p>
                                                    </div>
                                                    <div className="text-sm">
                                                        <p className="text-muted-foreground mb-1">{t('Total Amount')}</p>
                                                        <p className="font-medium">{repairinvoice.total_amount ? formatCurrency(repairinvoice.total_amount) : '-'}</p>
                                                    </div>
                                                </div>
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="text-sm">
                                                        <p className="text-muted-foreground mb-1">{t('Due Amount')}</p>
                                                        <p className="font-medium">{repairinvoice.due_amount ? formatCurrency(repairinvoice.due_amount) : formatCurrency(0)}</p>
                                                    </div>
                                                    <div className="text-sm">
                                                        <p className="text-muted-foreground mb-1">{t('Product Name')}</p>
                                                        <p className="font-medium">{repairinvoice.repair_order?.product_name || '-'}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="flex justify-between items-center pt-4 border-t mt-auto">
                                                <div className="flex items-center">
                                                    {(() => {
                                                        const statusMap = {
                                                            '0': { label: t('Pending'), className: 'px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800' },
                                                            '1': { label: t('Partially Paid'), className: 'px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800' },
                                                            '2': { label: t('Paid'), className: 'px-2 py-1 rounded-full text-sm bg-green-100 text-green-800' }
                                                        };
                                                        const statusInfo = statusMap[repairinvoice.status as keyof typeof statusMap] || { label: repairinvoice.status, className: 'px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-800' };
                                                        return <span className={statusInfo.className}>{statusInfo.label}</span>;
                                                    })()} 
                                                </div>

                                                <div className="flex gap-1">
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('view-repair-invoices') && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('repair-management-system.repair-invoices.show', repairinvoice.id))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('View')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('delete-repair-invoices') && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button
                                                                        variant="ghost"
                                                                        size="sm"
                                                                        onClick={() => openDeleteDialog(repairinvoice.id)}
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
                                            </div>
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={FileTextIcon}
                                    title={t('No Invoices found')}
                                    description={t('Get started by creating your first Invoice.')}
                                    hasFilters={!!(filters.invoice_id || filters.status)}
                                    onClearFilters={clearFilters}

                                />
                            )}
                        </div>
                    )}
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={repairinvoices || { data: [], links: [], meta: {} }}
                        routeName="repair-management-system.repair-invoices.index"
                        filters={{...filters, per_page: perPage, view: viewMode}}
                    />
                </CardContent>
            </Card>





            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Invoice')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}