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
import { Plus, Edit as EditIcon, Trash2, Box } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { FilterButton } from '@/components/ui/filter-button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import NoRecordsFound from '@/components/no-records-found';
import { formatCurrency, formatDate } from '@/utils/helpers';
import Create from './Create';
import Edit from './Edit';
import { PackagesIndexProps, Package, PackageFilters, PackageModalState } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { packages, items, extraServices, auth } = usePage<PackageIndexProps>().props;
    const urlParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [filters, setFilters] = useState<PackageFilters>({
        name: urlParams.get('name') || '',
        item_id: urlParams.get('item_id') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [showFilters, setShowFilters] = useState(false);

    const [modalState, setModalState] = useState<PackageModalState>({
        isOpen: false,
        mode: 'create',
        data: null
    });

    const openModal = (mode: 'create' | 'edit', data: Package | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: 'create', data: null });
    };


    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.packages.destroy',
        defaultMessage: t('Are you sure you want to delete this package?')
    });

    const handleFilter = () => {
        router.get(route('bookings.packages.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bookings.packages.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ name: '', item_id: '' });
        router.get(route('bookings.packages.index'), {per_page: perPage});
    };

    const tableColumns = [
        {
            key: 'name',
            header: t('Package Name'),
            sortable: true,
            render: (value: string) => value
        },
        {
            key: 'item_name',
            header: t('Item'),
            sortable: true,
            render: (value: number, pkg: Package) => pkg.item?.name || '-'
        },
        {
            key: 'delivery_time',
            header: t('Delivery Time'),
            sortable: true,
            render: (value: string, item: Package) => `${value} ${item.delivery_period}`
        },
        {
            key: 'price',
            header: t('Price'),
            sortable: true,
            render: (value: number) => formatCurrency(Number(value))
        },
        {
            key: 'created_at',
            header: t('Created At'),
            sortable: true,
            render: (value: string) => formatDate(value)
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-booking-packages', 'delete-booking-packages'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, item: Package) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-booking-packages') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', item)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-booking-packages') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(item.id)}
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
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('Packages')}
                ]}
                pageTitle={t('Manage Packages')}
                pageActions={
                    <div className="flex gap-2">
                        {auth.user?.permissions?.includes('create-booking-packages') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => openModal('create')}>
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
            <Head title={t('Packages')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.name}
                                onChange={(value) => setFilters({...filters, name: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search packages...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">

                            <PerPageSelector
                                routeName="bookings.packages.index"
                                filters={filters}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.item_id].filter(Boolean).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Item')}</label>
                                <Select value={filters.item_id} onValueChange={(value) => setFilters({...filters, item_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by item')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {items.map((item) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
                                            </SelectItem>
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
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                key={`packages-table-${packages.data.length}`}
                                data={packages.data}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Box}
                                        title={t('No packages found')}
                                        description={t('Get started by creating your first booking package.')}
                                        hasFilters={!!(filters.name || filters.item_id)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-booking-packages"
                                        onCreateClick={() => openModal('create')}
                                        createButtonText={t('Create Package')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={packages}
                        routeName="bookings.packages.index"
                        filters={{...filters, per_page: perPage, sort: sortField, direction: sortDirection}}
                    />
                </CardContent>
            </Card>

            <Create
                open={modalState.isOpen && modalState.mode === 'create'}
                onOpenChange={(open) => !open && closeModal()}
                items={items}
                extraServices={extraServices}
            />

            <Edit
                open={modalState.isOpen && modalState.mode === 'edit'}
                onOpenChange={(open) => !open && closeModal()}
                packageData={modalState.data}
                items={items}
                extraServices={extraServices}
            />

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Package')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            </AuthenticatedLayout>
        </TooltipProvider>
    );
}