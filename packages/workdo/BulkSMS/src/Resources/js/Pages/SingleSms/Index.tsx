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
import { Plus, Edit as EditIcon, Trash2, Eye, MessageSquare as MessageSquareIcon, Download, FileImage } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import Create from './Create';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { SingleSms, SingleSmsIndexProps, SingleSmsFilters, SingleSmsModalState } from './types';
import { formatDate, formatTime, formatDateTime, formatCurrency, getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { singlesms, auth, bulksmscontacts } = usePage<SingleSmsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState<SingleSmsFilters>({
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || '',
        contact_id: urlParams.get('contact_id') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode] = useState<'list'>('list');
    const [modalState, setModalState] = useState<SingleSmsModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<SingleSms | null>(null);

    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bulk-s-m-s.single-sms.destroy',
        defaultMessage: t('Are you sure you want to delete this single sms?')
    });

    const handleFilter = () => {
        router.get(route('bulk-s-m-s.single-sms.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bulk-s-m-s.single-sms.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            search: '',
            status: '',
            contact_id: '',
        });
        router.get(route('bulk-s-m-s.single-sms.index'), {per_page: perPage});
    };

    const openModal = (mode: 'add', data: SingleSms | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'contact_id',
            header: t('Contact'),
            sortable: false,
            render: (value: string, row: any) => {
                const modelData = bulksmscontacts?.find(item => item.id.toString() === value?.toString());
                return modelData?.name || value || '-';
            }
        },
        {
            key: 'mobile_no',
            header: t('Mobile Number'),
            sortable: false
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: string) => {
                const options: any = {"0":"pending","1":"sent","2":"failed"};
                const displayValue = options[value] || value || '-';
                const statusColors = {
                    delivered: 'bg-green-100 text-green-800',
                    failed: 'bg-red-100 text-red-800'
                };
                const colorClass = statusColors[displayValue as keyof typeof statusColors] || 'bg-gray-100 text-gray-800';
                return (
                    <span className={`px-2 py-1 rounded-full text-sm ${colorClass}`}>
                        {t(displayValue)}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-single-sms', 'delete-single-sms'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, singlesms: SingleSms) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-single-sms') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(singlesms)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}

                        {auth.user?.permissions?.includes('delete-single-sms') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(singlesms.id)}
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
                {label: t('Single SMS')}
            ]}
            pageTitle={t('Manage Single SMS')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-single-sms') && (
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
            <Head title={t('Single SMS')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search contacts and mobile numbers...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="bulk-s-m-s.single-sms.index"
                                filters={{...filters}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [
                                        filters.contact_id,
                                        filters.status
                                    ].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Contact')}</label>
                                <Select value={filters.contact_id} onValueChange={(value) => setFilters({ ...filters, contact_id: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Contact')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {bulksmscontacts?.map((contact: any) => (
                                            <SelectItem key={contact.id} value={contact.id.toString()}>
                                                {contact.name}
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



                {/* Table Content */}
                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                        <DataTable
                            data={singlesms?.data || []}
                            columns={tableColumns}
                            onSort={handleSort}
                            sortKey={sortField}
                            sortDirection={sortDirection as 'asc' | 'desc'}
                            className="rounded-none"
                            emptyState={
                                <NoRecordsFound
                                    icon={MessageSquareIcon}
                                    title={t('No Single SMS found')}
                                    description={t('Get started by creating your first Single SMS.')}
                                    hasFilters={!!(filters.search || filters.status || filters.contact_id)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-single-sms"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Single SMS')}
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
                        data={singlesms || { data: [], links: [], meta: {} }}
                        routeName="bulk-s-m-s.single-sms.index"
                        filters={{...filters, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}

            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View singlesms={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Single SMS')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}