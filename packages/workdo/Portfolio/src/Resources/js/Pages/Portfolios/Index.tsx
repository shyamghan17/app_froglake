import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { usePageButtons } from '@/hooks/usePageButtons';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DataTable } from '@/components/ui/data-table';
import { SearchInput } from '@/components/ui/search-input';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from '@/components/ui/pagination';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, FileImage, Briefcase, Link, Copy } from 'lucide-react';
import { toast } from 'sonner';
import { getImagePath } from '@/utils/helpers';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import NoRecordsFound from '@/components/no-records-found';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Portfolio, PortfoliosIndexProps, PortfolioFilters } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { portfolios, auth, portfoliocategories } = usePage<PortfoliosIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    const pageButtons = usePageButtons('portfolioShowButtons');

    const [filters, setFilters] = useState<PortfolioFilters>({
        title: urlParams.get('title') || '',
        category_id: urlParams.get('category_id') || 'all',
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'desc');

    const [showFilters, setShowFilters] = useState(false);
    const [copiedCode, setCopiedCode] = useState<string | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'portfolio.portfolios.destroy',
        defaultMessage: t('Are you sure you want to delete this portfolio?')
    });

    const copyPortfolioLink = async (portfolio: Portfolio) => {
        try {
            const portfolioUrl = route('portfolio.show', { slug: portfolio.slug });
            const fullUrl = portfolioUrl.startsWith('http') ? portfolioUrl : window.location.origin + portfolioUrl;
            await navigator.clipboard.writeText(fullUrl);
            setCopiedCode(portfolio.slug);
            setTimeout(() => setCopiedCode(null), 2000);
        } catch (error) {
            toast.error(t('Failed to copy link'));
        }
    };

    const handleFilter = () => {
        router.get(route('portfolio.portfolios.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            title: '',
            category_id: 'all',
        });
        router.get(route('portfolio.portfolios.index'), { per_page: perPage });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('portfolio.portfolios.index'), { ...filters, per_page: perPage, sort: field, direction }, {
            preserveState: true,
            replace: true
        });
    };

    const tableColumns = [
        {
            key: 'photo',
            header: t('Photo'),
            sortable: false,
            render: (value: string) => (
                <img src={value ? getImagePath(value) : getImagePath('avatar.png')} alt="Profile" className="w-10 h-10 rounded-full object-cover" />
            )
        },
        {
            key: 'name',
            header: t('Name'),
            sortable: true
        },
        {
            key: 'title',
            header: t('Title'),
            sortable: true
        },
        {
            key: 'client',
            header: t('Client'),
            sortable: true
        },
        {
            key: 'role',
            header: t('Role'),
            sortable: false
        },
        {
            key: 'experience_years',
            header: t('Experience'),
            sortable: false,
            render: (value: string) => value ? `${value}+ ` + t('years') : '-'
        },
        {
            key: 'category_id',
            header: t('Category'),
            sortable: false,
            render: (value: any, portfolio: Portfolio) => {
                const category = portfoliocategories.find((cat: any) => cat.id.toString() === portfolio.category_id?.toString());
                return category ? category.name : '-';
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['copy-portfolios', 'edit-portfolios', 'delete-portfolios'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, portfolio: Portfolio) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('copy-portfolios') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => copyPortfolioLink(portfolio)}
                                        className={`h-8 w-8 p-0 transition-colors ${copiedCode === portfolio.slug
                                            ? 'text-green-600 hover:text-green-700 bg-green-50'
                                            : 'text-purple-600 hover:text-purple-700'
                                            }`}
                                    >
                                        {copiedCode === portfolio.slug ? <Copy className="h-4 w-4" /> : <Link className="h-4 w-4" />}
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{copiedCode === portfolio.slug ? t('Copied!') : t('Copy Link')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-portfolios') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('portfolio.portfolios.edit', portfolio.id))} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-portfolios') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => {
                                            openDeleteDialog(portfolio.id);
                                        }}
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
                { label: t('Portfolio') },
                { label: t('Portfolios') }
            ]}
            pageTitle={t('Manage Portfolios')}
            pageActions={
                <div className="flex gap-2">
                    <TooltipProvider>
                        {pageButtons.map((button: any) => (
                            <div key={button.id}>{button.component}</div>
                        ))}
                        {auth.user?.permissions?.includes('create-portfolios') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => router.visit(route('portfolio.portfolios.create'))}>
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Create')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            }
        >
            <Head title={t('Portfolios')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.title}
                                onChange={(value) => setFilters({ ...filters, title: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search portfolios...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="portfolio.portfolios.index"
                                filters={{ ...filters }}
                            />
                            {auth.user?.permissions?.includes('manage-portfolio-categories') && (
                                <div className="relative">
                                    <FilterButton
                                        showFilters={showFilters}
                                        onToggle={() => setShowFilters(!showFilters)}
                                    />
                                    {(() => {
                                        const activeFilters = [filters.category_id !== 'all' ? filters.category_id : ''].filter(Boolean).length;
                                        return activeFilters > 0 && (
                                            <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                                {activeFilters}
                                            </span>
                                        );
                                    })()}
                                </div>
                            )}
                        </div>
                    </div>
                </CardContent>

                {/* Advanced Filters */}
                {showFilters && (
                    <CardContent className="p-6 bg-blue-50/30 border-b">
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                                <Select value={filters.category_id} onValueChange={(value) => setFilters({ ...filters, category_id: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('All Categories')} />
                                    </SelectTrigger>
                                    <SelectContent searchable={true}>
                                        <SelectItem value="all">{t('All Categories')}</SelectItem>
                                        {portfoliocategories.map((category: any) => (
                                            <SelectItem key={category.id} value={category.id.toString()}>
                                                {category.name}
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

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={portfolios.data}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Briefcase}
                                        title={t('No portfolios found')}
                                        description={t('Get started by creating your first portfolio.')}
                                        hasFilters={!!(filters.title || (auth.user?.permissions?.includes('manage-portfolio-categories') && filters.category_id !== 'all'))}
                                        onClearFilters={clearFilters}
                                        createPermission="create-portfolios"
                                        onCreateClick={() => router.visit(route('portfolio.portfolios.create'))}
                                        createButtonText={t('Create Portfolio')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={portfolios || { data: [], links: [], meta: {} }}
                        routeName="portfolio.portfolios.index"
                        filters={{ ...filters, per_page: perPage }}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Portfolio')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
