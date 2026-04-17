import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Button } from '@/components/ui/button';
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { Activity, Trash2 } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import NoRecordsFound from '@/components/no-records-found';
import { getPackageAlias } from '@/utils/helpers';

interface ActivityLog {
    id: number;
    module: string;
    sub_module: string | null;
    description: string;
    url: string | null;
    user: { name: string } | null;
    created_at: string;
}

interface ActivityLogsIndexProps {
    activityLogs: {
        data: ActivityLog[];
        links: any[];
        meta: any;
    };
    modules: string[];
    staffs: { id: number; name: string }[];
    auth: {
        user: {
            permissions: string[];
        };
    };
}

interface ActivityLogFilters {
    module: string;
    description: string;
    user_id: string;
    search: string;
}

export default function Index() {
    const { t } = useTranslation();
    const { activityLogs, modules, staffs, auth } = usePage<ActivityLogsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<ActivityLogFilters>({
        module: urlParams.get('module') || '',
        description: urlParams.get('description') || '',
        user_id: urlParams.get('user_id') || '',
        search: urlParams.get('search') || ''
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || 'created_at');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'desc');
    const [showFilters, setShowFilters] = useState(false);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'activity-logs.destroy',
        defaultMessage: t('Are you sure you want to delete this activity log?')
    });

    const handleFilter = () => {
        router.get(route('activity-logs.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('activity-logs.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ module: '', description: '', user_id: '', search: '' });
        router.get(route('activity-logs.index'), {per_page: perPage});
    };

    const formatDuration = (createdAt: string) => {
        const now = new Date();
        const created = new Date(createdAt);
        const diffMs = now.getTime() - created.getTime();
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffDays > 0) return `${diffDays}d ago`;
        if (diffHours > 0) return `${diffHours}h ago`;
        if (diffMins > 0) return `${diffMins}m ago`;
        return 'Just now';
    };

    const tableColumns = [
        {
            key: 'module',
            header: t('Module'),
            sortable: true,
            render: (value: string) => getPackageAlias(value)
        },
        {
            key: 'sub_module',
            header: t('Sub Module'),
            sortable: false,
            render: (value: string | null) => value || '-'
        },
        {
            key: 'description',
            header: t('Description'),
            sortable: false,
            render: (_: any, log: ActivityLog) => log.description + (log.user?.name ?? '') + '.'
        },
        {
            key: 'user.name',
            header: t('Staff'),
            render: (_: any, log: ActivityLog) => log.user?.name || '-'
        },
        {
            key: 'created_at',
            header: t('Activity Duration'),
            sortable: true,
            render: (value: string) => formatDuration(value)
        },
        ...(auth.user?.permissions?.includes('delete-activity-log') ? [{
            key: 'actions',
            header: t('Action'),
            render: (_: any, log: ActivityLog) => (
                <div className="flex gap-1">
                    <TooltipProvider>                        
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => openDeleteDialog(log.id)}
                                    className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                >
                                    <Trash2 className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Delete')}</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            )
        }] : [])
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[{label: t('Activity Log')}]}
            pageTitle={t('Manage Activity Log')}
        >
            <Head title={t('Activity Log')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search activities...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="activity-logs.index"
                                filters={filters}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.module, filters.user_id].filter(Boolean).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Add-On')}</label>
                                <Select value={filters.module} onValueChange={(value) => setFilters({...filters, module: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select Add-On')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {modules.map((module) => (
                                            <SelectItem key={module} value={module}>{getPackageAlias(module)}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Staff')}</label>
                                <Select value={filters.user_id} onValueChange={(value) => setFilters({...filters, user_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select staff')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {staffs.map((staff) => (
                                            <SelectItem key={staff.id} value={staff.id.toString()}>{staff.name}</SelectItem>
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
                                data={activityLogs.data}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Activity}
                                        title={t('No activity logs found')}
                                        description={t('No activities have been recorded yet.')}
                                        hasFilters={!!(filters.module || filters.description || filters.user_id || filters.search)}
                                        onClearFilters={clearFilters}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={activityLogs}
                        routeName="activity-logs.index"
                        filters={{...filters, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Activity Log')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}