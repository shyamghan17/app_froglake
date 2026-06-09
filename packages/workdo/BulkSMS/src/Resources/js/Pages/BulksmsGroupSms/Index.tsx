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
import { Plus, Trash2, Eye, MessageSquare } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import Create from './Create';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';

interface BulksmsGroupSms {
    id: number;
    name: string;
    group_id: number;
    mobile_no: string;
    sms: string;
    status: string;
    created_at: string;
}

interface BulksmsGroupSmsFilters {
    search: string;
}

interface BulksmsGroupSmsModalState {
    isOpen: boolean;
    mode: string;
    data: BulksmsGroupSms | null;
}

export default function Index() {
    const { t } = useTranslation();
    const { bulksmsgroupsms, auth, bulksmsgroups } = usePage<any>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState<BulksmsGroupSmsFilters>({
        search: urlParams.get('search') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [modalState, setModalState] = useState<BulksmsGroupSmsModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<BulksmsGroupSms | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bulk-s-m-s.bulksms-group-sms.destroy',
        defaultMessage: t('Are you sure you want to delete this bulk SMS?')
    });

    const handleFilter = () => {
        router.get(route('bulk-s-m-s.bulksms-group-sms.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bulk-s-m-s.bulksms-group-sms.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            search: '',
        });
        router.get(route('bulk-s-m-s.bulksms-group-sms.index'), {per_page: perPage});
    };

    const openModal = (mode: 'add', data: BulksmsGroupSms | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'group_id',
            header: t('Group Name'),
            sortable: false,
            render: (value: number) => {
                const group = bulksmsgroups?.find((item: any) => item.id === value);
                return group?.name || '-';
            }
        },
        {
            key: 'sms',
            header: t('SMS Message'),
            sortable: false,
            render: (value: string) => {
                return value ;
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-bulk-sms-groups-send', 'delete-bulk-sms-groups-send'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, bulksmsgroupsms: BulksmsGroupSms) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-bulk-sms-groups-send') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button 
                                        variant="ghost" 
                                        size="sm" 
                                        onClick={() => router.get(route('bulk-s-m-s.bulksms-group-sms.show', bulksmsgroupsms.id))} 
                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                    >
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}

                        {auth.user?.permissions?.includes('delete-bulk-sms-groups-send') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(bulksmsgroupsms.id)}
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
                {label: t('Bulk SMS')},
                {label: t('Send Bulk SMS')}
            ]}
            pageTitle={t('Manage Bulk SMS')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-bulk-sms-groups-send') && (
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
            <Head title={t('Bulk SMS')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search groups and SMS messages...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="bulk-s-m-s.bulksms-group-sms.index"
                                filters={{...filters}}
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                        <DataTable
                            data={bulksmsgroupsms?.data || []}
                            columns={tableColumns}
                            onSort={handleSort}
                            sortKey={sortField}
                            sortDirection={sortDirection as 'asc' | 'desc'}
                            className="rounded-none"
                            emptyState={
                                <NoRecordsFound
                                    icon={MessageSquare}
                                    title={t('No Bulk SMS found')}
                                    description={t('Get started by creating your first Bulk SMS.')}
                                    hasFilters={!!(filters.search)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-bulk-sms-groups-send"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Bulk SMS')}
                                    className="h-auto"
                                />
                            }
                        />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={bulksmsgroupsms || { data: [], links: [], meta: {} }}
                        routeName="bulk-s-m-s.bulksms-group-sms.index"
                        filters={{...filters, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Bulk SMS')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}