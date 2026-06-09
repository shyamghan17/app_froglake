import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, Shield as ShieldIcon } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import EditRepairWarranty from './Edit';
import View from './View';

import NoRecordsFound from '@/components/no-records-found';
import { RepairWarranty, RepairWarrantiesIndexProps, RepairWarrantyFilters, RepairWarrantyModalState } from './types';
import { formatDate } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { repairwarranties, auth, repairorderrequests, repairparts } = usePage<RepairWarrantiesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<RepairWarrantyFilters>({
        warranty_number: urlParams.get('warranty_number') || '',
        warranty_terms: urlParams.get('warranty_terms') || '',
        repair_order_id: urlParams.get('repair_order_id') || '',
        part_id: urlParams.get('part_id') || '',
        claim_status: urlParams.get('claim_status') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [modalState, setModalState] = useState<RepairWarrantyModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<RepairWarranty | null>(null);

    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'repair-management-system.repair-warranties.destroy',
        defaultMessage: t('Are you sure you want to delete this warranty?')
    });

    const handleFilter = () => {
        router.get(route('repair-management-system.repair-warranties.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('repair-management-system.repair-warranties.index'), {...filters, per_page: perPage, sort: field, direction, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            warranty_number: '',
            warranty_terms: '',
            repair_order_id: '',
            part_id: '',
            claim_status: '',
        });
        router.get(route('repair-management-system.repair-warranties.index'), {per_page: perPage, view: viewMode});
    };

    const openModal = (mode: 'add' | 'edit', data: RepairWarranty | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'warranty_number',
            header: t('Warranty Number'),
            sortable: true
        },
        {
            key: 'repair_order.product_name',
            header: t('Repair Order'),
            sortable: false,
            render: (value: any, row: any) => row.repair_order?.product_name || '-'
        },
        {
            key: 'part.name',
            header: t('Part'),
            sortable: false,
            render: (value: any, row: any) => row.part?.name || '-'
        },
        {
            key: 'warranty_period',
            header: t('Warranty Period'),
            sortable: false,
            render: (value: string) => {
                if (!value) return '-';
                const dates = value.split(' - ');
                if (dates.length === 2) {
                    const endDate = new Date(dates[1]);
                    const isExpired = !isNaN(endDate.getTime()) && endDate <= new Date();
                    const startDate = formatDate(dates[0]);
                    const formattedEndDate = formatDate(dates[1]);
                    return (
                        <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                            {startDate} - {formattedEndDate}
                        </span>
                    );
                }
                const date = new Date(value);
                const isValidDate = !isNaN(date.getTime());
                const isExpired = isValidDate && date <= new Date();
                return (
                    <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                        {isValidDate ? formatDate(value) : value}
                    </span>
                );
            }
        },
        {
            key: 'claim_status',
            header: t('Claim Status'),
            sortable: false,
            render: (value: any) => {
                const options: any = {"0":"Active","1":"Pending","2":"Claimed","3":"Expired"};
                const colors: any = {
                    "0":"bg-green-100 text-green-800",   // Active - Green
                    "1":"bg-yellow-100 text-yellow-800", // Pending - Yellow
                    "2":"bg-blue-100 text-blue-800",     // Claimed - Blue
                    "3":"bg-red-100 text-red-800"      // Expired - Gray
                };
                return (
                    <span className={`px-2 py-1 rounded-full text-sm ${colors[value] || 'bg-gray-100 text-gray-800'}`}>
                        {options[value] || value}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-repair-warranties', 'delete-repair-warranties'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, repairwarranty: RepairWarranty) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-repair-warranties') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(repairwarranty)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-repair-warranties') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', repairwarranty)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-repair-warranties') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(repairwarranty.id)}
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
                {label: t('Warranties')}
            ]}
            pageTitle={t('Manage Warranties')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-repair-warranties') && (
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button size="sm" onClick={() => openModal('add')}>
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
            <Head title={t('Warranties')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.warranty_number}
                                onChange={(value) => setFilters({...filters, warranty_number: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Warranties...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="repair-management-system.repair-warranties.index"
                                filters={{...filters, per_page: perPage}}
                            />
                            <PerPageSelector
                                routeName="repair-management-system.repair-warranties.index"
                                filters={{...filters, view: viewMode}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.repair_order_id, filters.part_id, filters.claim_status].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Repair Order')}</label>
                                <Select value={filters.repair_order_id} onValueChange={(value) => setFilters({...filters, repair_order_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Repair Order')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {repairorderrequests?.map((item: any) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Part')}</label>
                                <Select value={filters.part_id} onValueChange={(value) => setFilters({...filters, part_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Part')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {repairparts?.map((item: any) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Claim Status')}</label>
                                <Select value={filters.claim_status} onValueChange={(value) => setFilters({...filters, claim_status: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Claim Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Active')}</SelectItem>
                                        <SelectItem value="1">{t('Pending')}</SelectItem>
                                        <SelectItem value="2">{t('Claimed')}</SelectItem>
                                        <SelectItem value="3">{t('Expired')}</SelectItem>
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
                                data={repairwarranties?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={ShieldIcon}
                                        title={t('No Warranties found')}
                                        description={t('Get started by creating your first Warranty.')}
                                        hasFilters={!!(filters.warranty_number || filters.warranty_terms || filters.repair_order_id || filters.part_id || filters.claim_status)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-repair-warranties"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Warranty')}
                                        className="h-auto"
                                    />
                                }
                            />
                            </div>
                        </div>
                    ) : (
                        <div className="p-6">
                            {repairwarranties?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    {repairwarranties?.data?.map((repairwarranty) => {
                                        const statusInfo = (() => {
                                            const options: any = {"0":"Active","1":"Pending","2":"Claimed","3":"Expired"};
                                            const colors: any = {
                                                "0":"bg-green-100 text-green-800",
                                                "1":"bg-yellow-100 text-yellow-800",
                                                "2":"bg-blue-100 text-blue-800",
                                                "3":"bg-red-100 text-red-800"
                                            };
                                            return {
                                                name: options[repairwarranty.claim_status] || '-',
                                                colorClass: colors[repairwarranty.claim_status] || 'bg-gray-100 text-gray-800'
                                            };
                                        })();

                                        return (
                                        <Card key={repairwarranty.id} className="p-4 hover:shadow-md transition-shadow flex flex-col h-full">
                                            <div className="flex items-center gap-3 mb-3">
                                                <div className="p-2 bg-primary/10 rounded-lg">
                                                    <ShieldIcon className="h-4 w-4 text-primary" />
                                                </div>
                                                <h3 className="font-semibold text-base truncate">{repairwarranty.warranty_number}</h3>
                                            </div>

                                            <div className="grid grid-cols-2 gap-3 mb-4 flex-grow">
                                                <div className="text-sm">
                                                    <p className="text-muted-foreground text-xs">{t('Repair Order')}</p>
                                                    <p className="font-medium text-sm">{repairwarranty.repair_order?.product_name || '-'}</p>
                                                </div>
                                                <div className="text-sm">
                                                    <p className="text-muted-foreground text-xs">{t('Part')}</p>
                                                    <p className="font-medium text-sm truncate">{repairwarranty.part?.name || '-'}</p>
                                                </div>
                                                <div className="text-sm col-span-2">
                                                    <p className="text-muted-foreground text-xs">{t('Warranty Period')}</p>
                                                    <p className={`font-medium text-sm ${repairwarranty.warranty_period && (() => {
                                                        const dates = repairwarranty.warranty_period.split(' - ');
                                                        if (dates.length === 2) {
                                                            const endDate = new Date(dates[1]);
                                                            return !isNaN(endDate.getTime()) && endDate <= new Date();
                                                        }
                                                        const date = new Date(repairwarranty.warranty_period);
                                                        return !isNaN(date.getTime()) && date <= new Date();
                                                    })() ? 'text-red-600' : ''}`}>
                                                        {repairwarranty.warranty_period ? (() => {
                                                            const dates = repairwarranty.warranty_period.split(' - ');
                                                            if (dates.length === 2) {
                                                                const startDate = formatDate(dates[0]);
                                                                const endDate = formatDate(dates[1]);
                                                                return `${startDate} - ${endDate}`;
                                                            }
                                                            const date = new Date(repairwarranty.warranty_period);
                                                            const isValidDate = !isNaN(date.getTime());
                                                            return isValidDate ? formatDate(repairwarranty.warranty_period) : repairwarranty.warranty_period;
                                                        })() : '-'}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="flex justify-between items-center pt-3 border-t mt-auto">
                                                <span className={`px-2 py-1 rounded-full text-xs ${statusInfo.colorClass}`}>
                                                    {statusInfo.name}
                                                </span>
                                                <div className="flex gap-1">
                                                <TooltipProvider>
                        {auth.user?.permissions?.includes('view-repair-warranties') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(repairwarranty)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-repair-warranties') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', repairwarranty)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                                                    {auth.user?.permissions?.includes('delete-repair-warranties') && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    onClick={() => openDeleteDialog(repairwarranty.id)}
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
                                        );
                                    })}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={ShieldIcon}
                                    title={t('No Warranties found')}
                                    description={t('Get started by creating your first Warranty.')}
                                    hasFilters={!!(filters.warranty_number || filters.warranty_terms || filters.repair_order_id || filters.part_id || filters.claim_status)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-repair-warranties"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Warranty')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={repairwarranties || { data: [], links: [], meta: {} }}
                        routeName="repair-management-system.repair-warranties.index"
                        filters={{...filters, per_page: perPage, view: viewMode}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditRepairWarranty
                        repairwarranty={modalState.data}
                        repairorderrequests={repairorderrequests}
                        repairparts={repairparts}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View repairwarranty={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Warranty')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}