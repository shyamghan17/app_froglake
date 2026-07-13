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
import { Plus, Edit as EditIcon, Trash2, Eye, Bell as BellIcon, Pin, PinOff, CheckCircle, XCircle, MessageSquare } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import EditNotice from './Edit';
import Create from './Create';
import NoRecordsFound from '@/components/no-records-found';
import { Notice, NoticesIndexProps, NoticeFilters, NoticeModalState } from './types';
import { formatDate } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { notices, auth } = usePage<NoticesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<NoticeFilters>({
        title: urlParams.get('title') || '',
        priority: urlParams.get('priority') || '',
        target_type: urlParams.get('target_type') || '',
        status: urlParams.get('status') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [modalState, setModalState] = useState<NoticeModalState>({ isOpen: false, mode: '', data: null });
    const [showFilters, setShowFilters] = useState(false);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'notice-board.notices.destroy',
        defaultMessage: t('Are you sure you want to delete this notice?')
    });

    const [publishState, setPublishState] = useState<{ isOpen: boolean; id: number | null }>(({ isOpen: false, id: null }));
    const [deactivateState, setDeactivateState] = useState<{ isOpen: boolean; id: number | null }>(({ isOpen: false, id: null }));

    const handleFilter = () => {
        router.get(route('notice-board.notices.index'), {
            ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode
        }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('notice-board.notices.index'), {
            ...filters, per_page: perPage, sort: field, direction, view: viewMode
        }, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setFilters({ title: '', priority: '', target_type: '', status: '' });
        router.get(route('notice-board.notices.index'), { per_page: perPage, view: viewMode });
    };

    const openModal = (mode: 'add' | 'edit', data: Notice | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const priorityBadge = (value: string) => {
        const colorMap: Record<string, string> = {
            normal: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
            urgent: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
            critical: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        };

        return (
            <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${colorMap[value] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'}`}>
                {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
            </span>
        );
    };

    const expiryDateBadge = (expiryDate?: string | Date | null) => {
        if (!expiryDate) {
            return <span className="font-medium">-</span>;
        }

        const date = new Date(expiryDate);
        const formattedDate = formatDate(expiryDate);
        const isExpired = date <= new Date();

        return (
            <span className={`${isExpired ? 'text-red-600 dark:text-red-400' : ''}`}>
                {formattedDate}
            </span>
        );
    };

    const targetTypeLabel = (value: string) => {
        const labelMap: Record<string, string> = {
            all: t('All'),
            department: t('Department'),
            role: t('Role'),
            specific_users: t('Specific Users'),
        };

        return labelMap[value] || '-';
    };

    const statusBadge = (value: string) => {
        const colorMap: Record<string, string> = {
            draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            published: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            deactivated: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        };

        return (
            <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${colorMap[value] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'}`}>
                {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
            </span>
        );
    };

    const tableColumns = [
        {
            key: 'title',
            header: t('Title'),
            sortable: true,
        },
        {
            key: 'target_type',
            header: t('Target Audience'),
            sortable: false,
            render: (value: string) => targetTypeLabel(value),
        },
        {
            key: 'start_date',
            header: t('Start Date'),
            sortable: false,
            render: (value: string) => value ? formatDate(value) : '-',
        },
        {
            key: 'expiry_date',
            header: t('Expiry Date'),
            sortable: false,
            render: (value: string) => value ? expiryDateBadge(value) : '-',
        },
        {
            key: 'allow_comments',
            header: t('Allow Comments'),
            sortable: false,
            render: (value: boolean) => (
                <span className={`px-2 py-1 rounded-full text-xs font-medium ${value ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'}`}>
                    {value ? t('Yes') : t('No')}
                </span>
            ),
        },
        {
            key: 'priority',
            header: t('Priority'),
            sortable: false,
            render: (value: string) => priorityBadge(value),
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: string) => statusBadge(value),
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-notices', 'edit-notices', 'delete-notices', 'pin-unpin-notices', 'manage-notice-status'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, notice: Notice) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {/* Publish — only when draft */}
                        {auth.user?.permissions?.includes('manage-notice-status') && notice.status === 'draft' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setPublishState({ isOpen: true, id: notice.id })} className="h-8 w-8 p-0 text-violet-600 hover:text-violet-700">
                                        <CheckCircle className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Publish')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {/* Deactivate — only when published */}
                        {auth.user?.permissions?.includes('manage-notice-status') && notice.status === 'published' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setDeactivateState({ isOpen: true, id: notice.id })} className="h-8 w-8 p-0 text-red-500 hover:text-red-600">
                                        <XCircle className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Deactivate')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {/* Pin/Unpin */}
                        {auth.user?.permissions?.includes('pin-unpin-notices') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.patch(route('notice-board.notices.toggle-pin', notice.id))} className={`h-8 w-8 p-0 ${notice.is_pinned ? 'text-orange-500 hover:text-orange-600' : 'text-gray-400 hover:text-gray-600'}`}>
                                        {notice.is_pinned ? <PinOff className="h-4 w-4" /> : <Pin className="h-4 w-4" />}
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{notice.is_pinned ? t('Unpin') : t('Pin')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {/* View */}
                        {auth.user?.permissions?.includes('view-notices') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.get(route('notice-board.notices.show', notice.id), { from: 'notices' })} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {/* Edit — hidden when published */}
                        {auth.user?.permissions?.includes('edit-notices') && notice.status === 'draft' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', notice)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {/* Delete — hidden when published */}
                        {auth.user?.permissions?.includes('delete-notices') && notice.status === 'draft' && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(notice.id)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            )
        }] : [])
    ];

    const activeFilterCount = [filters.priority, filters.target_type, filters.status].filter(Boolean).length;

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Notice Board'), url: route('notice-board.board') },
                { label: t('Manage Notices') },
            ]}
            pageTitle={t('Manage Notices')}
            pageActions={
                <div className="flex gap-2">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('create-notices') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => openModal('add')}>
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Create')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            }
        >
            <Head title={t('Notices')} />

            <Card className="shadow-sm">

                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.title}
                                onChange={(value) => setFilters({ ...filters, title: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search Notices...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="notice-board.notices.index"
                                filters={{ ...filters, per_page: perPage }}
                            />
                            <PerPageSelector
                                routeName="notice-board.notices.index"
                                filters={{ ...filters, view: viewMode }}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {activeFilterCount > 0 && (
                                    <span className="absolute -top-2 -right-2 rtl:-left-2 rtl:right-auto bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                        {activeFilterCount}
                                    </span>
                                )}
                            </div>
                        </div>
                    </div>
                </CardContent>

                {/* Advanced Filters */}
                {showFilters && (
                    <CardContent className="p-6 bg-blue-50/30 dark:bg-slate-800/80 border-b dark:border-slate-700">
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{t('Priority')}</label>
                                <Select value={filters.priority} onValueChange={(value) => setFilters({ ...filters, priority: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('All Priorities')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="normal">{t('Normal')}</SelectItem>
                                        <SelectItem value="urgent">{t('Urgent')}</SelectItem>
                                        <SelectItem value="critical">{t('Critical')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{t('Target Audience')}</label>
                                <Select value={filters.target_type} onValueChange={(value) => setFilters({ ...filters, target_type: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('All Audiences')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All')}</SelectItem>
                                        <SelectItem value="department">{t('Department')}</SelectItem>
                                        <SelectItem value="role">{t('Role')}</SelectItem>
                                        <SelectItem value="specific_users">{t('Specific Users')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">{t('Status')}</label>
                                <Select value={filters.status} onValueChange={(value) => setFilters({ ...filters, status: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('All Statuses')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">{t('Draft')}</SelectItem>
                                        <SelectItem value="published">{t('Publish')}</SelectItem>
                                        <SelectItem value="deactivated">{t('Deactivated')}</SelectItem>
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

                {/* Table / Grid Content */}
                <CardContent className="p-0">
                    {viewMode === 'list' ? (
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-slate-600 scrollbar-track-gray-100 dark:scrollbar-track-slate-800 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                                <DataTable
                                    data={notices?.data || []}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    className="rounded-none"
                                    emptyState={
                                        <NoRecordsFound
                                            icon={BellIcon}
                                            title={t('No notices found')}
                                            description={t('Get started by creating your first notice.')}
                                            hasFilters={!!(filters.title || filters.priority || filters.target_type)}
                                            onClearFilters={clearFilters}
                                            createPermission="create-notices"
                                            onCreateClick={() => openModal('add')}
                                            createButtonText={t('Create Notice')}
                                            className="h-auto"
                                        />
                                    }
                                />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {notices?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
                                    {notices.data.map((notice: Notice) => (
                                        <Card key={notice.id} className="hover:shadow-md transition-shadow relative flex flex-col">
                                            {/* Header */}
                                            <div className="p-3 border-b dark:border-slate-700 min-h-[62px] flex flex-col justify-between">
                                                <div className="flex items-start justify-between gap-2">
                                                    <div className="flex items-center gap-1.5 min-w-0 order-1 rtl:order-2">
                                                        {notice.is_pinned && (
                                                            <Pin className="h-3.5 w-3.5 text-orange-500 shrink-0" />
                                                        )}
                                                        <h3 className="font-semibold text-sm leading-snug line-clamp-2">
                                                            {notice.title}
                                                        </h3>
                                                    </div>
                                                    <div className="shrink-0 order-2 rtl:order-1">{priorityBadge(notice.priority)}</div>
                                                </div>
                                            </div>

                                            {/* Body */}
                                            <div className="px-4 py-3 space-y-2 text-xs flex-1">
                                                {/* Description preview */}
                                                <p className="text-xs text-muted-foreground line-clamp-2"
                                                    dangerouslySetInnerHTML={{
                                                        __html: notice.description?.replace(/<[^>]*>/g, '') || '-'
                                                    }}
                                                />
                                                <div className="flex items-center justify-between gap-2">
                                                    <span className="text-muted-foreground">{t('Target')}</span>
                                                    <span className="font-medium">
                                                        {targetTypeLabel(notice.target_type)}
                                                    </span>
                                                </div>
                                                <div className="flex items-center justify-between gap-2">
                                                    <span className="text-muted-foreground">{t('Start Date')}</span>
                                                    <span className="font-medium">
                                                        {notice.start_date ? formatDate(notice.start_date) : '-'}
                                                    </span>
                                                </div>
                                                <div className="flex items-center justify-between gap-2">
                                                    <span className="text-muted-foreground">{t('Expiry')}</span>
                                                    <span className="font-medium">
                                                        {expiryDateBadge(notice.expiry_date)}
                                                    </span>
                                                </div>
                                                <div className="flex items-center justify-between gap-2">
                                                    <span className="text-muted-foreground">{t('Status')}</span>
                                                    {statusBadge(notice.status)}
                                                </div>
                                            </div>

                                            {/* Footer */}
                                            <div className="flex flex-wrap items-center justify-between gap-1 px-3 py-2 border-t dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">

                                                {/* Left — count */}
                                                <div className="flex items-center gap-2 order-1 rtl:order-2">
                                                    <span className="text-xs text-muted-foreground flex items-center gap-1">
                                                        <Eye className="h-3 w-3" />
                                                        {notice.reads_count ?? 0}
                                                    </span>
                                                    {auth.user?.permissions?.includes('manage-any-notices-comments') && notice.allow_comments && (
                                                        <span className="text-xs text-muted-foreground flex items-center gap-1">
                                                            <MessageSquare className="h-3 w-3" />
                                                            {notice.comments_count ?? 0}
                                                        </span>
                                                    )}
                                                </div>

                                                {/* Right — actions */}
                                                <div className="flex items-center flex-wrap gap-0.5 order-2 rtl:order-1">
                                                    <TooltipProvider>
                                                        {/* Publish — only draft */}
                                                        {auth.user?.permissions?.includes('manage-notice-status') && notice.status === 'draft' && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => setPublishState({ isOpen: true, id: notice.id })}
                                                                        className="h-8 w-8 p-0 text-violet-600 hover:text-violet-700">
                                                                        <CheckCircle className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Publish')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {/* Deactivate — only published */}
                                                        {auth.user?.permissions?.includes('manage-notice-status') && notice.status === 'published' && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => setDeactivateState({ isOpen: true, id: notice.id })}
                                                                        className="h-8 w-8 p-0 text-red-500 hover:text-red-600">
                                                                        <XCircle className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Deactivate')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {/* Pin/Unpin */}
                                                        {auth.user?.permissions?.includes('pin-unpin-notices') && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => router.patch(route('notice-board.notices.toggle-pin', notice.id))}
                                                                        className={`h-8 w-8 p-0 ${notice.is_pinned ? 'text-orange-500 hover:text-orange-600' : 'text-gray-400 hover:text-gray-600'}`}>
                                                                        {notice.is_pinned ? <PinOff className="h-4 w-4" /> : <Pin className="h-4 w-4" />}
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{notice.is_pinned ? t('Unpin') : t('Pin')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {/* View */}
                                                        {auth.user?.permissions?.includes('view-notices') && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => router.get(route('notice-board.notices.show', notice.id), { from: 'notices' })}
                                                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {/* Edit — only draft */}
                                                        {auth.user?.permissions?.includes('edit-notices') && notice.status === 'draft' && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => openModal('edit', notice)}
                                                                        className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                        <EditIcon className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {/* Delete — only draft */}
                                                        {auth.user?.permissions?.includes('delete-notices') && notice.status === 'draft' && (
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm"
                                                                        onClick={() => openDeleteDialog(notice.id)}
                                                                        className="h-8 w-8 p-0 text-destructive hover:text-destructive">
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
                                    icon={BellIcon}
                                    title={t('No notices found')}
                                    description={t('Get started by creating your first notice.')}
                                    hasFilters={!!(filters.title || filters.priority || filters.target_type)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-notices"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Notice')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t dark:border-slate-700 bg-gray-50/30 dark:bg-slate-800/30">
                    <Pagination
                        data={notices}
                        routeName="notice-board.notices.index"
                        filters={{ ...filters, per_page: perPage, view: viewMode }}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditNotice notice={modalState.data} onSuccess={closeModal} />
                )}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Notice')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            <ConfirmationDialog
                open={publishState.isOpen}
                onOpenChange={(open) => setPublishState({ isOpen: open, id: null })}
                title={t('Publish Notice')}
                message={t('Are you sure you want to publish this notice?')}
                confirmText={t('Publish')}
                onConfirm={() => { router.patch(route('notice-board.notices.publish', publishState.id)); setPublishState({ isOpen: false, id: null }); }}
                variant="default"
            />

            <ConfirmationDialog
                open={deactivateState.isOpen}
                onOpenChange={(open) => setDeactivateState({ isOpen: open, id: null })}
                title={t('Deactivate Notice')}
                message={t('Are you sure you want to deactivate this notice?')}
                confirmText={t('Deactivate')}
                onConfirm={() => { router.patch(route('notice-board.notices.deactivate', deactivateState.id)); setDeactivateState({ isOpen: false, id: null }); }}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
