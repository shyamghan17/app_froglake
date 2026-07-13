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
import { Trash2, Eye, History as HistoryIcon } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { SuggestionStatusHistory, SuggestionStatusHistoriesIndexProps, SuggestionStatusHistoryFilters } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { suggestionstatushistories, auth, suggestionboxsuggestions, users } = usePage<SuggestionStatusHistoriesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<SuggestionStatusHistoryFilters>({
        comment: urlParams.get('comment') || '',
        suggestion_id: urlParams.get('suggestion_id') || 'all',
        changed_by: urlParams.get('changed_by') || 'all',
        old_status: urlParams.get('old_status') || 'all',
        new_status: urlParams.get('new_status') || 'all',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const [viewingItem, setViewingItem] = useState<SuggestionStatusHistory | null>(null);
    const [showFilters, setShowFilters] = useState(false);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'status-histories.destroy',
        defaultMessage: t('Are you sure you want to delete this status history?')
    });

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            'new': { label: 'New', color: 'bg-blue-100 text-blue-800' },
            'under_review': { label: 'Under Review', color: 'bg-yellow-100 text-yellow-800' },
            'accepted': { label: 'Accepted', color: 'bg-purple-100 text-purple-800' },
            'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-800' },
            'complete': { label: 'Complete', color: 'bg-green-100 text-green-800' }
        };

        const config = statusConfig[status as keyof typeof statusConfig];
        return (
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}>
                {t(config?.label || status || '-')}
            </span>
        );
    };

    const handleFilter = () => {
        router.get(route('status-histories.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('status-histories.index'), { ...filters, per_page: perPage, sort: field, direction }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            comment: '',
            suggestion_id: 'all',
            changed_by: 'all',
            old_status: 'all',
            new_status: 'all',
        });
        router.get(route('status-histories.index'), { per_page: perPage });
    };

    const tableColumns = [
        {
            key: 'suggestion.title',
            header: t('Suggestion'),
            sortable: false,
            render: (value: any, row: any) => row.suggestion?.title || '-'
        },
        {
            key: 'changedBy.name',
            header: t('Changed By'),
            sortable: false,
            render: (value: any, SuggestionStatusHistory: SuggestionStatusHistory) => SuggestionStatusHistory.user?.name || '-'
        },
        {
            key: 'old_status',
            header: t('Old Status'),
            sortable: false,
            render: (value: string) => {
                if (!value) {
                    return <span className="text-gray-500 text-sm">{t('Initial Status')}</span>;
                }
                return getStatusBadge(value);
            }
        },
        {
            key: 'new_status',
            header: t('New Status'),
            sortable: false,
            render: (value: string) => getStatusBadge(value)
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-suggestion-status-histories', 'delete-suggestion-status-histories'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, suggestionstatushistory: SuggestionStatusHistory) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-suggestion-status-histories') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(suggestionstatushistory)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-suggestion-status-histories') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(suggestionstatushistory.id)}
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
                { label: t('Suggestion Box') },
                { label: t('Status Histories') }
            ]}
            pageTitle={t('Manage Status Histories')}
        >
            <Head title={t('Status Histories')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.comment}
                                onChange={(value) => setFilters({ ...filters, comment: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search Status Histories...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="status-histories.index"
                                filters={{ ...filters }}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [
                                        filters.suggestion_id !== 'all' ? filters.suggestion_id : '',
                                        filters.old_status !== 'all' ? filters.old_status : '',
                                        filters.new_status !== 'all' ? filters.new_status : ''
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
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Suggestion')}</label>
                                <Select value={filters.suggestion_id} onValueChange={(value) => setFilters({ ...filters, suggestion_id: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Suggestions')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All Suggestions')}</SelectItem>
                                        {suggestionboxsuggestions?.map((suggestion: any) => (
                                            <SelectItem key={suggestion.id} value={suggestion.id.toString()}>
                                                {suggestion.title}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Old Status')}</label>
                                <Select value={filters.old_status} onValueChange={(value) => setFilters({ ...filters, old_status: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Old Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All Status')}</SelectItem>
                                        <SelectItem value="new">{t('New')}</SelectItem>
                                        <SelectItem value="under_review">{t('Under Review')}</SelectItem>
                                        <SelectItem value="accepted">{t('Accepted')}</SelectItem>
                                        <SelectItem value="rejected">{t('Rejected')}</SelectItem>
                                        <SelectItem value="complete">{t('Complete')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('New Status')}</label>
                                <Select value={filters.new_status} onValueChange={(value) => setFilters({ ...filters, new_status: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('New Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All Status')}</SelectItem>
                                        <SelectItem value="new">{t('New')}</SelectItem>
                                        <SelectItem value="under_review">{t('Under Review')}</SelectItem>
                                        <SelectItem value="accepted">{t('Accepted')}</SelectItem>
                                        <SelectItem value="rejected">{t('Rejected')}</SelectItem>
                                        <SelectItem value="complete">{t('Complete')}</SelectItem>
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
                                data={suggestionstatushistories?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={HistoryIcon}
                                        title={t('No Status Histories found')}
                                        description={t('No status histories match your current filters.')}
                                        hasFilters={!!(filters.comment || (filters.suggestion_id !== 'all' && filters.suggestion_id) || (filters.old_status !== 'all' && filters.old_status) || (filters.new_status !== 'all' && filters.new_status))}
                                        onClearFilters={clearFilters}
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
                        data={suggestionstatushistories || { data: [], links: [], meta: {} }}
                        routeName="status-histories.index"
                        filters={{ ...filters, per_page: perPage }}
                    />
                </CardContent>
            </Card>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View suggestionstatushistory={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Status History')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}