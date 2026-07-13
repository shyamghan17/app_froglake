import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { Eye, MessageSquare, Lightbulb, Clock, CheckCircle, XCircle, Sparkles, ThumbsUp } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import NoRecordsFound from '@/components/no-records-found';
import { Suggestion, SuggestionsIndexProps } from './types';
import ResponseModal from './ResponseModal';
import { formatDate } from '@/utils/helpers';
import ExportButton from '../../Components/ExportButton';

export default function AdminDashboard() {
    const { t } = useTranslation();
    const { suggestions, categories, stats, auth } = usePage<SuggestionsIndexProps & { stats: any, auth: any }>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [searchTerm, setSearchTerm] = useState(urlParams.get('search') || '');
    const [filters, setFilters] = useState({
        status: urlParams.get('status') || 'all',
        category_id: urlParams.get('category_id') || 'all',
        date_range: urlParams.get('date_range') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<{
        isOpen: boolean;
        mode: 'response' | null;
        suggestion: Suggestion | null;
    }>({
        isOpen: false,
        mode: null,
        suggestion: null
    });

    useFlashMessages();

    const exportColumns = [
        { key: 'title', header: 'Title' },
        { key: 'description', header: 'Description', render: (value: string) => value?.replace(/<[^>]*>/g, '') || '-' },
        {
            key: 'user', header: 'Submitted By', render: (value: any, suggestion: any) =>
                suggestion.user?.name || '-'
        },
        { key: 'is_anonymous', header: 'Is Anonymous', render: (value: boolean) => value ? 'Yes' : 'No' },
        { key: 'category', header: 'Category', render: (value: any, suggestion: any) => suggestion.category?.name || '-' },
        {
            key: 'status', header: 'Status', render: (value: string) => {
                const statusLabels: Record<string, string> = {
                    'new': 'New',
                    'under_review': 'Under Review',
                    'accepted': 'Accepted',
                    'rejected': 'Rejected',
                    'complete': 'Complete'
                };
                return statusLabels[value] || value;
            }
        },
        { key: 'created_at', header: 'Date', render: (value: string) => formatDate(value) },
        { key: 'admin_response', header: 'Admin Response', render: (value: string) => value?.replace(/<[^>]*>/g, '') || '-' },
        { key: 'responded_by', header: 'Responded By', render: (value: any, suggestion: any) => suggestion.responded_by?.name || '-' },
        { key: 'responded_at', header: 'Responded At', render: (value: string) => value ? formatDate(value) : '-' },
        { key: 'views_count', header: 'Views' },
        { key: 'votes_count', header: 'Votes' },
        {
            key: 'voters', header: 'Voter Names', render: (value: any, suggestion: any) => {
                if (!suggestion.votes || suggestion.votes.length === 0) return '-';
                return suggestion.votes.map((vote: any) => vote.user?.name || 'Unknown').join(', ');
            }
        }
    ];

    const handleFilter = () => {
        const params: Record<string, any> = {
            search: searchTerm,
            per_page: perPage,
            sort: sortField,
            direction: sortDirection
        };
        if (filters.status !== 'all') params.status = filters.status;
        if (filters.category_id !== 'all') params.category_id = filters.category_id;
        if (filters.date_range) params.date_range = filters.date_range;

        router.get(route('suggestion-admin.index'), params, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        const params: Record<string, any> = {
            search: searchTerm,
            per_page: perPage,
            sort: field,
            direction
        };
        if (filters.status !== 'all') params.status = filters.status;
        if (filters.category_id !== 'all') params.category_id = filters.category_id;
        if (filters.date_range) params.date_range = filters.date_range;

        router.get(route('suggestion-admin.index'), params, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setSearchTerm('');
        setFilters({ status: 'all', category_id: 'all', date_range: '' });
        router.get(route('suggestion-admin.index'));
    };

    const openModal = (mode: 'response', suggestion: Suggestion) => {
        setModalState({
            isOpen: true,
            mode,
            suggestion
        });
    };

    const closeModal = () => {
        setModalState({
            isOpen: false,
            mode: null,
            suggestion: null
        });

        if (modalState.mode === 'response') {
            router.reload({ only: ['suggestions', 'stats'] });
        }
    };

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            'new': {
                label: 'New',
                color: 'bg-blue-100 text-blue-800'
            },
            'under_review': {
                label: 'Under Review',
                color: 'bg-yellow-100 text-yellow-800'
            },
            'accepted': {
                label: 'Accepted',
                color: 'bg-purple-100 text-purple-800'
            },
            'rejected': {
                label: 'Rejected',
                color: 'bg-red-100 text-red-800'
            },
            'complete': {
                label: 'Complete',
                color: 'bg-green-100 text-green-800'
            }
        };

        const config = statusConfig[status as keyof typeof statusConfig];

        return (
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}>
                {t(config?.label || status)}
            </span>
        );
    };

    const getCategoryBadge = (category: any) => {
        return (
            <span
                className="px-2 py-1 rounded-full text-sm font-medium"
                style={{
                    backgroundColor: `${category?.color || '#273247'}20`,
                    color: category?.color || '#384b69'
                }}
            >
                {category?.name}
            </span>
        );
    };

    const tableColumns = [
        {
            key: 'title',
            header: t('Title'),
            sortable: true,
            render: (value: string, suggestion: Suggestion) => (
                <span>{value}</span>
            )
        },
        {
            key: 'user',
            header: t('Submitted By'),
            sortable: false,
            render: (value: any, suggestion: Suggestion) => (
                <span>
                    {suggestion.is_anonymous
                        ? t('Anonymous')
                        : suggestion.user?.name
                    }
                </span>
            )
        },
        {
            key: 'votes_count',
            header: t('Votes'),
            sortable: true,
            render: (value: number) => (
                <span>{value}</span>
            )
        },
        {
            key: 'views_count',
            header: t('Views'),
            sortable: true,
            render: (value: number) => (
                <span>{value}</span>
            )
        },
        {
            key: 'created_at',
            header: t('Date'),
            sortable: true,
            render: (_value: string, suggestion: Suggestion) => formatDate(suggestion.created_at)
        },
        {
            key: 'category',
            header: t('Category'),
            sortable: false,
            render: (value: any, suggestion: Suggestion) => getCategoryBadge(suggestion.category)
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: true,
            render: (value: string) => getStatusBadge(value)
        },
        {
            key: 'actions',
            header: t('Actions'),
            render: (_value: any, suggestion: Suggestion) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => openModal('response', suggestion)}
                                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                >
                                    <MessageSquare className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Respond')}</p>
                            </TooltipContent>
                        </Tooltip>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => router.get(route('suggestions.show', suggestion.id))}
                                    className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                >
                                    <Eye className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('View')}</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            )
        }
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Suggestion Box') },
                { label: t('Admin Dashboard') }
            ]}
            pageTitle={t('Admin Dashboard')}
        >
            <Head title={t('Admin Dashboard')} />

            {/* Statistics Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 sm:gap-3 mb-6">
                {/* Total Suggestions - Teal */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-teal-50 to-teal-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-teal-700 mb-1">{t('Total Suggestions')}</h3>
                                <p className="text-2xl font-bold text-teal-900">{stats?.total || 0}</p>
                            </div>
                            <div className="p-3 bg-teal-200 rounded-xl">
                                <Lightbulb className="h-6 w-6 text-teal-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* New - Blue */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-blue-50 to-blue-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-blue-700 mb-1">{t('New')}</h3>
                                <p className="text-2xl font-bold text-blue-900">{stats?.new || 0}</p>
                            </div>
                            <div className="p-3 bg-blue-200 rounded-xl">
                                <Sparkles className="h-6 w-6 text-blue-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Accept - Purple */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-purple-50 to-purple-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-purple-700 mb-1">{t('Accepted')}</h3>
                                <p className="text-2xl font-bold text-purple-900">{stats?.accepted || 0}</p>
                            </div>
                            <div className="p-3 bg-purple-200 rounded-xl">
                                <ThumbsUp className="h-6 w-6 text-purple-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Under Review - Yellow */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-yellow-50 to-yellow-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-yellow-700 mb-1">{t('Under Review')}</h3>
                                <p className="text-2xl font-bold text-yellow-900">{stats?.under_review || 0}</p>
                            </div>
                            <div className="p-3 bg-yellow-200 rounded-xl">
                                <Clock className="h-6 w-6 text-yellow-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Complete - Green */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-green-50 to-green-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-green-700 mb-1">{t('Complete')}</h3>
                                <p className="text-2xl font-bold text-green-900">{stats?.complete || 0}</p>
                            </div>
                            <div className="p-3 bg-green-200 rounded-xl">
                                <CheckCircle className="h-6 w-6 text-green-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Reject - Red */}
                <Card className="border-0 shadow-lg bg-gradient-to-br from-red-50 to-red-100">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-sm font-medium text-red-700 mb-1">{t('Rejected')}</h3>
                                <p className="text-2xl font-bold text-red-900">{stats?.rejected || 0}</p>
                            </div>
                            <div className="p-3 bg-red-200 rounded-xl">
                                <XCircle className="h-6 w-6 text-red-700" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={searchTerm}
                                onChange={setSearchTerm}
                                onSearch={handleFilter}
                                placeholder={t('Search suggestions...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">

                                <PerPageSelector
                                    routeName="suggestion-admin.index"
                                    filters={{
                                        search: searchTerm,
                                        ...(filters.status !== 'all' && { status: filters.status }),
                                        ...(filters.category_id !== 'all' && { category_id: filters.category_id }),
                                        ...(filters.date_range && { date_range: filters.date_range }),
                                        sort: sortField,
                                        direction: sortDirection
                                    }}
                                />
                            <ExportButton
                                data={suggestions?.data || []}
                                columns={exportColumns}
                                filename="suggestions-export"
                                title="All Suggestions"
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.status, filters.category_id, filters.date_range].filter(f => f !== 'all' && f !== null && f !== undefined && f !== '').length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select
                                    value={filters.status}
                                    onValueChange={(value: string) => setFilters(prev => ({ ...prev, status: value }))}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All Status')}</SelectItem>
                                        <SelectItem value="new">{t('New')}</SelectItem>
                                        <SelectItem value="accepted">{t('Accepted')}</SelectItem>
                                        <SelectItem value="rejected">{t('Rejected')}</SelectItem>
                                        <SelectItem value="under_review">{t('Under Review')}</SelectItem>
                                        <SelectItem value="complete">{t('Complete')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                                <Select
                                    value={filters.category_id}
                                    onValueChange={(value: string) => setFilters(prev => ({ ...prev, category_id: value }))}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by category')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All Categories')}</SelectItem>
                                        {categories?.map((category: any) => (
                                            <SelectItem key={category.id} value={String(category.id)}>
                                                {category.icon} {category.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Date Range')}</label>
                                <DateRangePicker
                                    value={filters.date_range}
                                    onChange={(value) => setFilters(prev => ({ ...prev, date_range: value }))}
                                    placeholder={t('Select date range')}
                                />
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
                                data={suggestions?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Lightbulb}
                                        title={t('No suggestions found')}
                                        description={t('No suggestions match your current filters')}
                                        hasFilters={!!(searchTerm || filters.status !== 'all' || filters.category_id !== 'all' || filters.date_range)}
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
                        data={suggestions || { data: [], links: [], meta: {} }}
                        routeName="suggestion-admin.index"
                        filters={{
                            search: searchTerm,
                            ...(filters.status !== 'all' && { status: filters.status }),
                            ...(filters.category_id !== 'all' && { category_id: filters.category_id }),
                            ...(filters.date_range && { date_range: filters.date_range }),
                            sort: sortField,
                            direction: sortDirection,
                            per_page: perPage
                        }}
                    />
                </CardContent>
            </Card>

            {/* Response Modal */}
            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'response' && modalState.suggestion && (
                    <ResponseModal suggestion={modalState.suggestion} onSuccess={closeModal} />
                )}
            </Dialog>
        </AuthenticatedLayout>
    );
}