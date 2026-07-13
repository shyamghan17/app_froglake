import { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog } from '@/components/ui/dialog';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, ThumbsUp, Eye, Trash2, MessageSquare, EyeOff, EditIcon, ArrowLeft } from 'lucide-react';
import { SearchInput } from '@/components/ui/search-input';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import NoRecordsFound from '@/components/no-records-found';
import Create from './Create';
import Edit from './Edit';
import { formatDate } from '@/utils/helpers';
import { Suggestion, SuggestionFilters, SuggestionModalState } from './types';

interface MySuggestionsProps {
    suggestions: {
        data: Suggestion[];
        links: any[];
    };
    categories: any[];
    stats: {
        total: number;
        new: number;
        under_review: number;
        accepted: number;
        rejected: number;
        complete: number;
    };
    auth: {
        user: {
            id: number;
            name: string;
            permissions: string[];
        };
    };
}

export default function MySuggestions() {
    const { t } = useTranslation();
    const { suggestions, categories, stats, auth } = usePage<MySuggestionsProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<SuggestionFilters>({
        name: urlParams.get('search') || '',
        category_id: 'all',
        status: 'all',
        date_range: ''
    });
    const [modalState, setModalState] = useState<SuggestionModalState>({
        isOpen: false,
        mode: '',
        data: null
    });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'suggestions.destroy',
        defaultMessage: t('Are you sure you want to delete this suggestion?')
    });

    const handleFilter = () => {
        const params = {
            ...(filters.name && { search: filters.name })
        };

        router.get(route('suggestions.my-suggestions'), params, {
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
        router.get(route('suggestions.my-suggestions'));
    };

    const openModal = (mode: 'add' | 'edit', data?: any) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
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
            'new': { label: 'New', color: 'bg-blue-100 text-blue-800' },
            'under_review': { label: 'Under Review', color: 'bg-yellow-100 text-yellow-800' },
            'accepted': { label: 'Accepted', color: 'bg-purple-100 text-purple-800' },
            'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-800' },
            'complete': { label: 'Complete', color: 'bg-green-100 text-green-800' },
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
                { label: t('Suggestion Box') },
                { label: t('Suggestions'), url: route('suggestions.index') },
                { label: t('My Suggestions') }
            ]}
            pageTitle={t('My Suggestions')}
            pageActions={
                <TooltipProvider>
                    <div className="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => router.visit(route('suggestions.index'))}
                        >
                            <ArrowLeft className="h-4 w-4" />
                            {t('Back')}
                        </Button>
                        {auth.user?.permissions?.includes('manage-own-suggestions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => router.get(route('suggestions.index'))}>
                                        <MessageSquare className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Suggestions')}</p>
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
            <Head title={t('My Suggestions')} />

            <div className="space-y-6">
                {/* Search Bar Only */}
                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.name}
                                    onChange={(value) => setFilters({ ...filters, name: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search my suggestions...')}
                                />
                            </div>
                        </div>
                    </CardContent>
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
                                        hasFilters={!!(filters.name)}
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
                                                <div className="flex items-center gap-6 text-sm text-gray-500">
                                                    <div className="flex items-center gap-1.5 cursor-pointer" onClick={(e) => {
                                                        e.stopPropagation();
                                                        handleVote(suggestion.id);
                                                    }}>
                                                        <ThumbsUp className="h-4 w-4 text-blue-600" />
                                                        <span className="font-semibold text-gray-700">{suggestion.votes_count}</span>
                                                    </div>
                                                    <div className="flex items-center gap-1.5">
                                                        <Eye className="h-4 w-4 text-green-600" />
                                                        <span>{suggestion.views_count}</span>
                                                    </div>
                                                </div>

                                                <div className="flex items-center gap-2">
                                                    <TooltipProvider>
                                                        {suggestion.is_anonymous && (
                                                            <Tooltip>
                                                                <TooltipTrigger asChild>
                                                                    <Button
                                                                        variant="ghost"
                                                                        size="sm"
                                                                        className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                                                    >
                                                                        <EyeOff className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('Anonymous')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {suggestion.status === 'new' && (
                                                            <>
                                                                <Tooltip>
                                                                    <TooltipTrigger asChild>
                                                                        <Button
                                                                            variant="ghost"
                                                                            size="sm"
                                                                            onClick={(e) => {
                                                                                e.stopPropagation();
                                                                                openModal('edit', suggestion);
                                                                            }}
                                                                            className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                                                        >
                                                                            <EditIcon className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Edit')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                                <Tooltip>
                                                                    <TooltipTrigger asChild>
                                                                        <Button
                                                                            variant="ghost"
                                                                            size="sm"
                                                                            onClick={(e) => {
                                                                                e.stopPropagation();
                                                                                openDeleteDialog(suggestion.id);
                                                                            }}
                                                                            className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                                                        >
                                                                            <Trash2 className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Delete')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                            </>
                                                        )}
                                                    </TooltipProvider>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Create/Edit Modal */}
            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <Edit onSuccess={closeModal} suggestion={modalState.data} />
                )}
            </Dialog>

            {/* Delete Confirmation */}
            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Suggestion')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}