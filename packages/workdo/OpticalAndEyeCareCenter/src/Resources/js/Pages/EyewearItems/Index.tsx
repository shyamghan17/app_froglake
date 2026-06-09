import { useState, useMemo } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Package, Edit, Trash2, Image, Eye } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import NoRecordsFound from '@/components/no-records-found';
import { formatCurrency, getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { eyewearitems, categories, auth } = usePage<any>().props;
    const urlParams = useMemo(() => new URLSearchParams(window.location.search), []);

    const [filters, setFilters] = useState({
        name: urlParams.get('name') || '',
        category_id: urlParams.get('category_id') || ''
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');

    const [showFilters, setShowFilters] = useState(false);
    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'optical-and-eye-care-center.eyewear-items.destroy',
        defaultMessage: t('Are you sure you want to delete this eyewear item?')
    });

    const handleFilter = () => {
        router.get(route('optical-and-eye-care-center.eyewear-items.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('optical-and-eye-care-center.eyewear-items.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ name: '', category_id: '' });
        router.get(route('optical-and-eye-care-center.eyewear-items.index'), { per_page: perPage, view: viewMode });
    };

    const tableColumns = [
        {
            key: 'image',
            header: t('Image'),
            render: (value: string) => {
                if (!value) {
                    return (
                        <div className="w-12 h-12 bg-gray-100 rounded-md border flex items-center justify-center">
                            <Image className="w-6 h-6 text-gray-400" />
                        </div>
                    );
                }
                const isImage = /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(value);
                let imageUrl = getImagePath(value);
                return isImage ? (
                    <div className="relative w-12 h-12">
                        <img
                            src={imageUrl}
                            alt="Image"
                            className="w-12 h-12 object-cover rounded-md border hover:scale-110 transition-transform cursor-pointer"
                            onClick={() => window.open(imageUrl, '_blank')}
                            onError={(e) => {
                                const target = e.target as HTMLImageElement;
                                target.style.display = 'none';
                                const fallback = target.nextElementSibling as HTMLElement;
                                if (fallback) fallback.classList.remove('hidden');
                            }}
                        />
                        <div className="hidden w-12 h-12 bg-gray-100 rounded-md border flex items-center justify-center">
                            <Image className="w-6 h-6 text-gray-400" />
                        </div>
                    </div>
                ) : (
                    <div className="w-12 h-12 bg-gray-100 rounded-md border flex items-center justify-center">
                        <Image className="w-6 h-6 text-gray-400" />
                    </div>
                );
            }
        },
        {
            key: 'name',
            header: t('Name'),
            sortable: true
        },
        {
            key: 'sku',
            header: t('SKU'),
            sortable: false
        },
        {
            key: 'sale_price',
            header: t('Sale Price'),
            sortable: true,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'quantity',
            header: t('Quantity'),
            sortable: false,
            render: (value: string) => value || '0'
        },
        {
            key: 'category_id',
            header: t('Category'),
            render: (value: number, item: any) => item.category?.name || '-'
        },
        {
            key: 'actions',
            header: t('Actions'),
            render: (_: any, item: any) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-eyewear-items') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.show', { eyewearitem: item.id }))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-eyewear-items') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.edit', { eyewearitem: item.id }))} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-eyewear-items') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(item.id)}
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
        }
    ];

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    {
                        label: t('Optical & Eye Care Center'), url: route('optical-and-eye-care-center.dashboard'),
                    }, { label: t('Eyewear Items') }
                ]}
                pageTitle={t('Manage Eyewear Items')}
                pageActions={
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('create-eyewear-items') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.create'))}>
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
                <Head title={t('Eyewear Items')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.name}
                                    onChange={(value) => setFilters({ ...filters, name: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search items...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <ListGridToggle
                                    currentView={viewMode}
                                    routeName="optical-and-eye-care-center.eyewear-items.index"
                                    filters={{ ...filters, per_page: perPage, sort: sortField, direction: sortDirection }}
                                />
                                <PerPageSelector
                                    routeName="optical-and-eye-care-center.eyewear-items.index"
                                    filters={{ ...filters, sort: sortField, direction: sortDirection, view: viewMode }}
                                />
                                <div className="relative">
                                    <FilterButton
                                        showFilters={showFilters}
                                        onToggle={() => setShowFilters(!showFilters)}
                                    />
                                    {(() => {
                                        const activeFilters = [filters.category_id].filter(Boolean).length;
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
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                                    <Select value={filters.category_id} onValueChange={(value) => setFilters({ ...filters, category_id: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by category')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((category) => (
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
                        {viewMode === 'list' ? (
                            <div className="overflow-y-auto max-h-[70vh] w-full">
                                <DataTable
                                    data={eyewearitems.data}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    emptyState={
                                        <NoRecordsFound
                                            icon={Package}
                                            title={t('No items found')}
                                            description={t('Get started by creating your first item.')}
                                            hasFilters={!!(filters.name || filters.category_id)}
                                            onClearFilters={clearFilters}
                                            onCreateClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.create'))}
                                            createButtonText={t('Create Item')}
                                        />
                                    }
                                />
                            </div>
                        ) : (
                            <div className="overflow-auto max-h-[70vh] p-6">
                                {eyewearitems.data.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                        {eyewearitems.data.map((item: any) => {
                                            const imageUrl = item.image ? getImagePath(item.image) : null;
                                            return (
                                            <Card key={item.id} className="p-0 hover:shadow-lg transition-all duration-200 relative overflow-hidden flex flex-col h-full min-w-0">
                                                <div className="p-4 bg-gradient-to-r from-primary/5 to-transparent border-b flex-shrink-0">
                                                    <div className="flex items-center gap-3">
                                                        {imageUrl ? (
                                                            <div className="relative w-10 h-10">
                                                                <img
                                                                    src={imageUrl}
                                                                    alt={item.name}
                                                                    className="w-10 h-10 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                                                                    onClick={() => window.open(imageUrl, '_blank')}
                                                                    onError={(e) => {
                                                                        const target = e.target as HTMLImageElement;
                                                                        target.style.display = 'none';
                                                                        const fallback = target.nextElementSibling as HTMLElement;
                                                                        if (fallback) fallback.classList.remove('hidden');
                                                                    }}
                                                                />
                                                                <div className="hidden w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                                    <Package className="h-5 w-5 text-primary" />
                                                                </div>
                                                            </div>
                                                        ) : (
                                                            <div className="p-2 bg-primary/10 rounded-lg">
                                                                <Package className="h-5 w-5 text-primary" />
                                                            </div>
                                                        )}
                                                        <div className="min-w-0 flex-1">
                                                            <h3 className="font-semibold text-sm text-gray-900">{item.name}</h3>
                                                            <p className="text-xs font-medium text-primary">{t('Eyewear Item')}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="p-4 flex-1 min-h-0">
                                                    <div className="grid grid-cols-2 gap-4 mb-4">
                                                        <div className="text-xs min-w-0">
                                                            <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('SKU')}</p>
                                                            <p className="font-medium text-xs">{item.sku || '-'}</p>
                                                        </div>
                                                        <div className="text-xs min-w-0">
                                                            <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Sale Price')}</p>
                                                            <p className="font-medium text-xs">{item.sale_price ? formatCurrency(item.sale_price) : '-'}</p>
                                                        </div>
                                                    </div>

                                                    <div className="grid grid-cols-2 gap-4">
                                                        <div className="text-xs min-w-0">
                                                            <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Quantity')}</p>
                                                            <p className="font-medium text-xs">{item.quantity || '0'}</p>
                                                        </div>
                                                        <div className="text-xs min-w-0">
                                                            <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Category')}</p>
                                                            <p className="font-medium text-xs">{item.category?.name || '-'}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="flex justify-end gap-1 p-3 border-t bg-gray-50/50 flex-shrink-0 mt-auto">
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('view-eyewear-items') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.show', { eyewearitem: item.id }))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('View')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('edit-eyewear-items') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.edit', { eyewearitem: item.id }))} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                        <Edit className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('Edit')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('delete-eyewear-items') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button
                                                                        variant="ghost"
                                                                        size="sm"
                                                                        onClick={() => openDeleteDialog(item.id)}
                                                                        className="h-8 w-8 p-0 text-red-600 hover:text-red-700 "
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
                                            </Card>
                                        );
                                        })}
                                    </div>
                                ) : (
                                    <NoRecordsFound
                                        icon={Package}
                                        title={t('No items found')}
                                        description={t('Get started by creating your first item.')}
                                        hasFilters={!!(filters.name || filters.category_id)}
                                        onClearFilters={clearFilters}
                                        onCreateClick={() => router.visit(route('optical-and-eye-care-center.eyewear-items.create'))}
                                        createButtonText={t('Create Item')}
                                    />
                                )}
                            </div>
                        )}
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={eyewearitems}
                            routeName="optical-and-eye-care-center.eyewear-items.index"
                            filters={{ ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }}
                        />
                    </CardContent>
                </Card>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Eyewear Item')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />

            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
