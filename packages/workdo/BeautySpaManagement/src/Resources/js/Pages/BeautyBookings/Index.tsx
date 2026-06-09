import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Calendar as CalendarIcon, Download, FileImage, CreditCard, Eye } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import EditBeautyBooking from './Edit';
import PaymentModal from './PaymentModal';
import ViewModal from './View';

import NoRecordsFound from '@/components/no-records-found';
import { BeautyBooking, BeautyBookingsIndexProps, BeautyBookingFilters, BeautyBookingModalState } from './types';
import { formatDate, formatTime, formatCurrency } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { beautybookings, auth, beautyservices } = usePage<BeautyBookingsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<BeautyBookingFilters>({
        name: urlParams.get('name') || '',
        email: urlParams.get('email') || '',
        phone_number: urlParams.get('phone_number') || '',
        service: urlParams.get('service') || '',
        gender: urlParams.get('gender') || '',
        reference: urlParams.get('reference') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const [modalState, setModalState] = useState<BeautyBookingModalState>({
        isOpen: false,
        mode: '',
        data: null
    });


    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'beauty-spa-management.beauty-bookings.destroy',
        defaultMessage: t('Are you sure you want to delete this bookings?')
    });

    const handleFilter = () => {
        router.get(route('beauty-spa-management.beauty-bookings.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('beauty-spa-management.beauty-bookings.index'), { ...filters, per_page: perPage, sort: field, direction }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            name: '',
            email: '',
            phone_number: '',
            service: '',
            gender: '',
            reference: '',
        });
        router.get(route('beauty-spa-management.beauty-bookings.index'), { per_page: perPage });
    };

    const openModal = (mode: 'add' | 'edit' | 'payment' | 'view', data: BeautyBooking | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
        router.reload();
    };

    const tableColumns = [
        {
            key: 'name',
            header: t('Name'),
            sortable: true
        },
        {
            key: 'service',
            header: t('Service'),
            sortable: false,
            render: (value: string, row: any) => {
                const modelData = beautyservices?.find(item => item.id.toString() === value?.toString());
                return modelData?.name || value || '-';
            }
        },
        {
            key: 'price',
            header: t('Price'),
            sortable: true,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'gender',
            header: t('Gender'),
            sortable: false,
            render: (value: string) => {
                const genderMap: { [key: string]: string } = {
                    'male': t('Male'),
                    'female': t('Female'),
                    'other': t('Other')
                };
                return genderMap[value] || value || '-';
            }
        },
        {
            key: 'date',
            header: t('Date'),
            sortable: true,
            render: (value: string) => value ? formatDate(value) : '-'
        },
        {
            key: 'start_time',
            header: t('Start Time'),
            sortable: false,
            render: (value: string) => value ? formatTime(value) : '-'
        },
        {
            key: 'end_time',
            header: t('End Time'),
            sortable: false,
            render: (value: string) => value ? formatTime(value) : '-'
        },
         {
            key: 'payment_option',
            header: t('Payment Method'),
            sortable: false,
            render: (value: string) => (
                <span className={`px-2 py-1 rounded-full text-xs ${
                    value === 'Offline' ? 'bg-gray-100 text-gray-800' :
                    'bg-blue-100 text-blue-800'
                }`}>
                    {t(value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()))}
                </span>
            )
        },
        {
            key: 'payment_status',
            header: t('Payment status'),
            sortable: false,
            render: (value: string) => (
                <span className={`px-2 py-1 rounded-full text-xs ${
                    value === 'paid' ? 'bg-green-100 text-green-800' :
                    value === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                }`}>
                    {t(value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()))}
                </span>
            )
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-beauty-bookings', 'delete-beauty-bookings'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, beautybooking: BeautyBooking) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {(beautybooking.payment_status === 'pending') && !beautybooking.has_payment  && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('payment',beautybooking)} className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700">
                                        <CreditCard className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Payment')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-beauty-bookings') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('view', beautybooking)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {(beautybooking.payment_status != 'paid') && !beautybooking.has_payment && auth.user?.permissions?.includes('edit-beauty-bookings') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', beautybooking)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {(beautybooking.payment_status != 'paid') && !beautybooking.has_payment && auth.user?.permissions?.includes('delete-beauty-bookings') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(beautybooking.id)}
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
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('Bookings') }
            ]}
            pageTitle={t('Manage Bookings')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-beauty-bookings') && (
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
            <Head title={t('Bookings')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.name}
                                onChange={(value) => setFilters({ ...filters, name: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search Beauty Bookings...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">

                            <PerPageSelector
                                routeName="beauty-spa-management.beauty-bookings.index"
                                filters={{ ...filters }}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.service, filters.gender, filters.reference].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Service')}</label>
                                <Select value={filters.service} onValueChange={(value) => setFilters({ ...filters, service: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Service')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {beautyservices?.map((item: any) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Gender')}</label>
                                <Select value={filters.gender} onValueChange={(value) => setFilters({ ...filters, gender: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Gender')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Male')}</SelectItem>
                                        <SelectItem value="1">{t('Female')}</SelectItem>
                                        <SelectItem value="2">{t('Other')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Reference')}</label>
                                <Select value={filters.reference} onValueChange={(value) => setFilters({ ...filters, reference: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Reference')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Google')}</SelectItem>
                                        <SelectItem value="1">{t('Friend')}</SelectItem>
                                        <SelectItem value="2">{t('Social Media')}</SelectItem>
                                        <SelectItem value="3">{t('Other')}</SelectItem>
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
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={beautybookings?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={CalendarIcon}
                                        title={t('No Bookings found')}
                                        description={t('Get started by creating your first Booking.')}
                                        hasFilters={!!(filters.name || filters.email || filters.phone_number || filters.service || filters.gender || filters.reference)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-beauty-bookings"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Booking')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={beautybookings || { data: [], links: [], meta: {} }}
                        routeName="beauty-spa-management.beauty-bookings.index"
                        filters={{ ...filters, per_page: perPage }}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditBeautyBooking
                        booking={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
                {modalState.mode === 'view' && modalState.data && (
                    <ViewModal
                        booking={modalState.data}
                        beautyservices={beautyservices || []}
                    />
                )}
                {modalState.mode === 'payment' && modalState.data && (
                    <PaymentModal
                        isOpen={true}
                        booking={modalState.data}
                        serviceName={beautyservices?.find(s => s.id.toString() === modalState.data?.service?.toString())?.name || ''}
                        onClose={closeModal}
                    />
                )}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Bookings')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}