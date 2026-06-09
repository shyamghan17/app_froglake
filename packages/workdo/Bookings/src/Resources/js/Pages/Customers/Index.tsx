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
import { Plus, Edit, Trash2, UserCheck } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { FilterButton } from '@/components/ui/filter-button';
import NoRecordsFound from '@/components/no-records-found';
import { Dialog, DialogTrigger } from '@/components/ui/dialog';
import { DatePicker } from '@/components/ui/date-picker';
import { CustomersIndexProps, Customer, CustomerModalState } from './types';
import Create from './Create';
import EditCustomer from './Edit';

export default function Index() {
    const { t } = useTranslation();
    const { customers, auth } = usePage<CustomerIndexProps>().props;
    const urlParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [filters, setFilters] = useState({
        search: urlParams.get('search') || '',
        date_from: urlParams.get('date_from') || '',
        date_to: urlParams.get('date_to') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<CustomerModalState>({
        isOpen: false,
        mode: '',
        data: null
    });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.customers.destroy',
        defaultMessage: t('Are you sure you want to delete this customer?')
    });

    const openModal = (mode: 'add' | 'edit', data: Customer | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const handleFilter = () => {
        router.get(route('bookings.customers.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bookings.customers.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ search: '', date_from: '', date_to: '' });
        router.get(route('bookings.customers.index'), {per_page: perPage});
    };

    const tableColumns = [
        {
            key: 'first_name',
            header: t('Name'),
            sortable: true,
            render: (value: string, customer: Customer) => `${customer.first_name} ${customer.last_name}`
        },
        {
            key: 'email',
            header: t('Email'),
            sortable: true,
            render: (value: string) => value
        },
        {
            key: 'mobile_number',
            header: t('Mobile Number'),
            render: (value: string) => value || '-'
        },
        {
            key: 'description',
            header: t('Description'),
            render: (value: string) => {
                if (!value) return '-';
                const maxLength = 50;
                const isTruncated = value.length > maxLength;
                const displayText = isTruncated ? value.substring(0, maxLength) + '...' : value;
                
                return isTruncated ? (
                    <Tooltip delayDuration={0}>
                        <TooltipTrigger asChild>
                            <span className="cursor-help">{displayText}</span>
                        </TooltipTrigger>
                        <TooltipContent className="max-w-xs">
                            <p>{value}</p>
                        </TooltipContent>
                    </Tooltip>
                ) : displayText;
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-booking-customers', 'delete-booking-customers'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, customer: Customer) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-booking-customers') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', customer)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-booking-customers') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(customer.id)}
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
                    {label: t('Customers')}
                ]}
                pageTitle={t('Manage Customers')}
                pageActions={
                    <div className="flex gap-2">
                        {auth.user?.permissions?.includes('create-booking-customers') && (
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
                    </div>
                }
            >
            <Head title={t('Customers')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search customers...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="bookings.customers.index"
                                filters={filters}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.date_from, filters.date_to].filter(Boolean).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Date From')}</label>
                                <DatePicker
                                    value={filters.date_from}
                                    onChange={(value) => setFilters({...filters, date_from: value})}
                                    placeholder={t('Select start date')}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Date To')}</label>
                                <DatePicker
                                    value={filters.date_to}
                                    onChange={(value) => setFilters({...filters, date_to: value})}
                                    placeholder={t('Select end date')}
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
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                key={`customers-table-${customers.data.length}`}
                                data={customers.data}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={UserCheck}
                                        title={t('No customers found')}
                                        description={t('Get started by adding your first customer.')}
                                        hasFilters={!!(filters.search || filters.date_from || filters.date_to)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-booking-customers"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Add Customer')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={customers}
                        routeName="bookings.customers.index"
                        filters={{...filters, per_page: perPage, sort: sortField, direction: sortDirection}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditCustomer
                        customer={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Customer')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            </AuthenticatedLayout>
        </TooltipProvider>
    );
}