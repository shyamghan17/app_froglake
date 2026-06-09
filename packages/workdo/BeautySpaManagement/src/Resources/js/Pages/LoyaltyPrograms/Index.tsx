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
import { Plus, Edit as EditIcon, Trash2, Eye, Award as AwardIcon, Download, FileImage } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";

import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import Create from './Create';
import EditBeautyLoyaltyProgram from './Edit';

import NoRecordsFound from '@/components/no-records-found';
import { BeautyLoyaltyProgram, BeautyLoyaltyProgramsIndexProps, BeautyLoyaltyProgramFilters, BeautyLoyaltyProgramModalState } from './types';
import { formatDate, formatTime, formatDateTime, formatCurrency, getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { beautyloyaltyprograms, auth } = usePage<BeautyLoyaltyProgramsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState<BeautyLoyaltyProgramFilters>({
        customer_name: urlParams.get('customer_name') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const [modalState, setModalState] = useState<BeautyLoyaltyProgramModalState>({
        isOpen: false,
        mode: '',
        data: null
    });


    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'beauty-spa-management.beauty-loyalty-programs.destroy',
        defaultMessage: t('Are you sure you want to delete this loyalty program?')
    });

    const handleFilter = () => {
        router.get(route('beauty-spa-management.beauty-loyalty-programs.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('beauty-spa-management.beauty-loyalty-programs.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            customer_name: '',
        });
        router.get(route('beauty-spa-management.beauty-loyalty-programs.index'), {per_page: perPage});
    };

    const openModal = (mode: 'add' | 'edit', data: BeautyLoyaltyProgram | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'customer_name',
            header: t('Customer Name'),
            sortable: true
        },
        {
            key: 'points_earned',
            header: t('Points Earned'),
            sortable: false,
            render: (value: number) => value || '-'
        },
        {
            key: 'points_redeemed',
            header: t('Points Redeemed'),
            sortable: false,
            render: (value: number) => value || '-'
        },
        {
            key: 'last_updated',
            header: t('Last Updated'),
            sortable: true,
            render: (value: string) => value ? formatDate(value) : '-'
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-beauty-loyalty-programs', 'delete-beauty-loyalty-programs'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, beautyloyaltyprogram: BeautyLoyaltyProgram) => (
                <div className="flex gap-1">
                    <TooltipProvider>

                        {auth.user?.permissions?.includes('edit-beauty-loyalty-programs') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', beautyloyaltyprogram)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-beauty-loyalty-programs') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(beautyloyaltyprogram.id)}
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
                {label: t('Beauty Spa Management'), url: route('beauty-spa-management.index')},
                {label: t('Loyalty Programs')}
            ]}
            pageTitle={t('Manage Loyalty Programs')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-beauty-loyalty-programs') && (
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
            <Head title={t('Loyalty Programs')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.customer_name}
                                onChange={(value) => setFilters({...filters, customer_name: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Loyalty Programs...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="beauty-spa-management.beauty-loyalty-programs.index"
                                filters={{...filters}}
                            />
                            
                        </div>
                    </div>
                </CardContent>



                {/* Table Content */}
                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                        <DataTable
                            data={beautyloyaltyprograms?.data || []}
                            columns={tableColumns}
                            onSort={handleSort}
                            sortKey={sortField}
                            sortDirection={sortDirection as 'asc' | 'desc'}
                            className="rounded-none"
                            emptyState={
                                <NoRecordsFound
                                    icon={AwardIcon}
                                    title={t('No Loyalty Programs found')}
                                    description={t('Get started by creating your first Loyalty Program.')}
                                    hasFilters={!!(filters.customer_name)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-beauty-loyalty-programs"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Loyalty Program')}
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
                        data={beautyloyaltyprograms || { data: [], links: [], meta: {} }}
                        routeName="beauty-spa-management.beauty-loyalty-programs.index"
                        filters={{...filters, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditBeautyLoyaltyProgram
                        beautyloyaltyprogram={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>



            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Loyalty Programs')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}