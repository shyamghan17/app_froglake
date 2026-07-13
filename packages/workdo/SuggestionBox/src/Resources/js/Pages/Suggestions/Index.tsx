import { useState, useEffect } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog } from '@/components/ui/dialog';
import { FilterButton } from '@/components/ui/filter-button';
import { Plus, ThumbsUp, Eye, MessageSquare } from 'lucide-react';
import { SearchInput } from '@/components/ui/search-input';
import { Pagination } from "@/components/ui/pagination";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import NoRecordsFound from '@/components/no-records-found';
import Create from './Create';
import { formatDate } from '@/utils/helpers';
import { Suggestion, SuggestionsIndexProps, SuggestionModalState, SuggestionFilters } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { suggestions, categories, auth } = usePage<SuggestionsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    useFlashMessages();
    
    const [filters, setFilters] = useState<SuggestionFilters>({
        name: urlParams.get('name') || '',
        category_id: urlParams.get('category_id') || 'all',
        status: urlParams.get('status') || 'all',
        date_range: urlParams.get('date_range') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<SuggestionModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [selectedSuggestion, setSelectedSuggestion] = useState<any>(null);
    const [hasVoted, setHasVoted] = useState<Record<number, boolean>>({});

    useEffect(() => {
        if (suggestions?.data) {
            const votedSuggestions: Record<number, boolean> = {};
            suggestions.data.forEach(suggestion => {
                votedSuggestions[suggestion.id] = suggestion.has_voted || false;
            });
            setHasVoted(votedSuggestions);
        }
    }, [suggestions?.data]);

    const handleFilter = () => {
        const params = {
            ...(filters.name && { name: filters.name }),
            ...(filters.category_id !== 'all' && { category_id: filters.category_id }),
            ...(filters.status !== 'all' && { status: filters.status })
        };
        
        router.get(route('suggestions.index'), { ...params, per_page: perPage }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            name: '',
            category_id: 'all',
            status: 'all',
            date_range: ''
        });
        router.get(route('suggestions.index'), { per_page: perPage });
    };

    const openModal = (mode: 'add' | 'view', data?: any) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
        setSelectedSuggestion(null);
    };

    const handleViewSuggestion = (suggestion: Suggestion) => {
        router.get(route('suggestions.show', suggestion.id));
    };

    const handleVote = async (suggestionId: number) => {
        router.post(route('suggestions.vote', suggestionId), {}, {
            preserveScroll: true,
            onSuccess: () => {
            }
        });
    };

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            'new': { label: 'New', color: 'bg-blue-100 text-blue-800'},
            'under_review': { label: 'Under Review', color: 'bg-yellow-100 text-yellow-800'},
            'accepted': { label: 'Accepted', color: 'bg-purple-100 text-purple-800'},
            'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-800'},
            'complete': { label: 'Complete', color: 'bg-green-100 text-green-800'},
        };
        
        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig['new'];
        
        return (
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}>
                {t(config?.label || status || '-')}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Suggestion Box')},
                {label: t('Suggestions')}
            ]}
            pageTitle={t('Suggestions')}
            pageActions={
                <TooltipProvider>
                    <div className="flex items-center gap-2">
                        {auth.user?.permissions?.includes('manage-own-suggestions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => router.get(route('suggestions.my-suggestions'))}>
                                        <MessageSquare className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('My Suggestions')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('create-suggestions') && (
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
                </TooltipProvider>
            }
        >
            <Head title={t('Suggestions')} />

            <div className="space-y-6">
                {/* Search and Filter Bar */}
                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.name}
                                    onChange={(value) => setFilters({...filters, name: value})}
                                    onSearch={handleFilter}
                                    placeholder={t('Search suggestions...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <PerPageSelector
                                    routeName="suggestions.index"
                                    filters={filters}
                                />
                                <div className="relative">
                                    <FilterButton
                                        showFilters={showFilters}
                                        onToggle={() => setShowFilters(!showFilters)}
                                    />
                                    {(() => {
                                        const activeFilters = [filters.category_id !== 'all' ? filters.category_id : '', filters.status !== 'all' ? filters.status : '', filters.date_range].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                                    <Select value={filters.category_id} onValueChange={(value) => setFilters({...filters, category_id: value})}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('All Categories')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">{t('All Categories')}</SelectItem>
                                            {categories?.map(category => (
                                                <SelectItem key={category.id} value={category.id.toString()}>
                                                    {category.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                    <Select value={filters.status} onValueChange={(value) => setFilters({...filters, status: value})}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('All Status')} />
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
                                <div className="flex items-end gap-2">
                                    <Button onClick={handleFilter} size="sm">{t('Apply')}</Button>
                                    <Button variant="outline" onClick={clearFilters} size="sm">{t('Clear')}</Button>
                                </div>
                            </div>
                        </CardContent>
                    )}
                </Card>

                {/* Suggestions Grid */}
                <Card className="shadow-sm">
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {(!suggestions?.data || suggestions.data.length === 0) ? (
                                <div className="col-span-full">
                                    <NoRecordsFound
                                        icon={MessageSquare}
                                        title={t('No Suggestions found')}
                                        description={t('Get started by creating your first suggestion.')}
                                        hasFilters={!!(filters.name || filters.category_id !== 'all' || filters.status !== 'all' || filters.date_range)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-suggestions"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Suggestion')}
                                        className="h-auto"
                                    />
                                </div>
                            ) : (
                                suggestions.data.map((suggestion) => (
                        <Card
                            key={suggestion.id}
                            className="group hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden bg-white border-l-4"
                            style={{ borderLeftColor: suggestion.category?.color || '#6B7280' }}
                        >
                            <CardContent className="p-6 h-full flex flex-col">
                                {/* Header: Status + Date */}
                                <div className="flex justify-between items-start mb-3">
                                    {getStatusBadge(suggestion.status)}
                                    <span className="text-sm text-gray-500 font-medium">
                                        {formatDate(suggestion.created_at)}
                                    </span>
                                </div>

                                {/* Title */}
                                <h3 
                                    className="font-bold text-[17px] leading-tight mb-3 text-gray-900 transition-colors line-clamp-2 cursor-pointer hover:text-blue-600"
                                    onClick={() => handleViewSuggestion(suggestion)}
                                >
                                    {suggestion.title}
                                </h3>

                                {/* Description - Flex grow to take available space */}
                                <div className="flex-1 overflow-hidden mb-2">
                                    <p className="text-gray-600 text-sm line-clamp-4 leading-relaxed">
                                        {suggestion.description}
                                    </p>
                                </div>

                                {/* Footer: Stats + Actions - Always at bottom */}
                                <div className="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <div className="flex items-center gap-4 text-sm text-gray-500">
                                        <div className="flex items-center gap-1.5">
                                            <span className="font-medium">
                                                {suggestion.is_anonymous ? t('Anonymous') : suggestion.user?.name}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-6 text-sm text-gray-500">
                                        <div className="flex items-center gap-1.5 cursor-pointer" onClick={(e) => {
                                            e.stopPropagation();
                                            handleVote(suggestion.id);
                                        }}>
                                            <ThumbsUp className={`h-4 w-4 ${hasVoted[suggestion.id] ? 'text-blue-600 fill-blue-600' : 'text-blue-600'}`} />
                                            <span className="font-semibold text-gray-700">{suggestion.votes_count}</span>
                                        </div>
                                        <div className="flex items-center gap-1.5">
                                            <Eye className="h-4 w-4 text-green-600" />
                                            <span>{suggestion.views_count}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                                ))
                            )}
                        </div>
                    </CardContent>

                    {/* Pagination Footer */}
                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={suggestions || { data: [], links: [], meta: {} }}
                            routeName="suggestions.index"
                            filters={{ ...filters, per_page: perPage }}
                        />
                    </CardContent>
                </Card>
            </div>

            {/* Create/View Suggestion Modal */}
            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
            </Dialog>
        </AuthenticatedLayout>
    );
}