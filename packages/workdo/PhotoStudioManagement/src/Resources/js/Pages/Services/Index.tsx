import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Dialog } from '@/components/ui/dialog';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, Briefcase, DollarSign, Tag, Camera } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { FilterButton } from '@/components/ui/filter-button';
import { SearchInput } from '@/components/ui/search-input';
import { Pagination } from '@/components/ui/pagination';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import Edit from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { PhotoStudioService, ServicesIndexProps, ServiceModalState, ServiceFilters } from './types';
import { getImagePath, formatCurrency } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { services, auth, serviceCategories, cameraKits } = usePage<ServicesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<ServiceFilters>({
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || '',
        category_id: urlParams.get('category_id') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<ServiceModalState>({ isOpen: false, mode: '', data: null });
    const [viewingItem, setViewingItem] = useState<PhotoStudioService | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.services.destroy',
        defaultMessage: t('Are you sure you want to delete this service?'),
    });

    const openModal = (mode: 'add' | 'edit', data: PhotoStudioService | null = null) =>
        setModalState({ isOpen: true, mode, data });

    const closeModal = () => setModalState({ isOpen: false, mode: '', data: null });

    const handleFilter = () => {
        router.get(route('photo-studio-management.services.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('photo-studio-management.services.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setFilters({ search: '', status: '', category_id: '' });
        router.get(route('photo-studio-management.services.index'), { per_page: perPage, view: viewMode });
    };

    const resolveCategories = (ids: string[]) =>
        ids?.slice(0, 2).map((id) => serviceCategories.find((c) => c.id.toString() === id)?.name).filter(Boolean) || [];

    const tableColumns = [
        {
            key: 'image',
            header: t('Image'),
            sortable: false,
            render: (_: any, row: PhotoStudioService) =>
                row.image ? (
                    <img src={getImagePath(row.image)} alt={row.name} className="h-10 w-10 object-cover rounded" />
                ) : (
                    <div className="h-10 w-10 bg-gray-100 rounded flex items-center justify-center">
                        <Briefcase className="h-5 w-5 text-gray-400" />
                    </div>
                ),
        },
        { key: 'name', header: t('Service Name'), sortable: true },
        {
            key: 'service_category_ids',
            header: t('Categories'),
            sortable: false,
            render: (_: any, row: PhotoStudioService) => {
                const names = resolveCategories(row.service_category_ids);
                return names.length > 0 ? (
                    <div className="flex flex-wrap gap-1">
                        {names.map((name, i) => <Badge key={i} variant="secondary" className="text-xs">{name}</Badge>)}
                        {row.service_category_ids?.length > 2 && <Badge variant="outline" className="text-xs">+{row.service_category_ids.length - 2}</Badge>}
                    </div>
                ) : '-';
            },
        },
        
        {
            key: 'camera_kit_ids',
            header: t('Camera Kits'),
            sortable: false,
            render: (_: any, row: PhotoStudioService) => {
                if (!row.camera_kit_ids || row.camera_kit_ids.length === 0) return '-';
                return (
                    <div className="flex flex-wrap gap-1">
                        {row.camera_kit_ids.slice(0, 2).map((id) => {
                            const kit = cameraKits.find((k) => k.id.toString() === id);
                            return kit ? <Badge key={id} variant="secondary" className="text-xs">{kit.name}</Badge> : null;
                        })}
                        {row.camera_kit_ids.length > 2 && (
                            <Badge variant="outline" className="text-xs">+{row.camera_kit_ids.length - 2}</Badge>
                        )}
                    </div>
                );
            },
        },
        {
            key: 'price',
            header: t('Price'),
            sortable: true,
            render: (_: any, row: PhotoStudioService) => formatCurrency(row.price),
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (_: any, row: PhotoStudioService) => (
                <span className={`px-2 py-1 rounded-full text-sm ${row.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {row.status ? t('Active') : t('Inactive')}
                </span>
            ),
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-photo-studio-service', 'edit-photo-studio-service', 'delete-photo-studio-service'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, row: PhotoStudioService) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-photo-studio-service') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(row)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-photo-studio-service') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', row)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-photo-studio-service') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(row.id)} className="h-8 w-8 p-0 text-red-600 hover:text-red-700">
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            ),
        }] : []),
    ];

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                    { label: t('Services') },
                ]}
                pageTitle={t('Manage Services')}
                pageActions={
                    <div className="flex gap-2">
                        <TooltipProvider>
                            {auth.user?.permissions?.includes('create-photo-studio-service') && (
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button size="sm" onClick={() => openModal('add')}><Plus className="h-4 w-4" /></Button>
                                    </TooltipTrigger>
                                    <TooltipContent><p>{t('Create')}</p></TooltipContent>
                                </Tooltip>
                            )}
                        </TooltipProvider>
                    </div>
                }
            >
                <Head title={t('Services')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.search}
                                    onChange={(value) => setFilters({ ...filters, search: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search Services...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <ListGridToggle currentView={viewMode} routeName="photo-studio-management.services.index" filters={{ ...filters, per_page: perPage }} onViewChange={setViewMode} />
                                <PerPageSelector routeName="photo-studio-management.services.index" filters={{ ...filters, view: viewMode }} />
                                <div className="relative">
                                    <FilterButton showFilters={showFilters} onToggle={() => setShowFilters(!showFilters)} />
                                    {(filters.status !== '' || filters.category_id !== '') && (
                                        <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            {[filters.status, filters.category_id].filter(Boolean).length}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </CardContent>

                    {showFilters && (
                        <CardContent className="p-6 bg-blue-50/30 border-b">
                            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                    <Select value={filters.status} onValueChange={(value) => setFilters({ ...filters, status: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by Status')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="1">{t('Active')}</SelectItem>
                                            <SelectItem value="0">{t('Inactive')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                                    <Select value={filters.category_id} onValueChange={(value) => setFilters({ ...filters, category_id: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by Category')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {serviceCategories.map((cat) => (
                                                <SelectItem key={cat.id} value={cat.id.toString()}>{cat.name}</SelectItem>
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
                            <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                                <div className="min-w-[700px]">
                                    <DataTable
                                        data={services?.data || []}
                                        columns={tableColumns}
                                        onSort={handleSort}
                                        sortKey={sortField}
                                        sortDirection={sortDirection as 'asc' | 'desc'}
                                        className="rounded-none"
                                        emptyState={
                                            <NoRecordsFound
                                                icon={Briefcase}
                                                title={t('No Services found')}
                                                description={t('Get started by creating your first service.')}
                                                hasFilters={!!(filters.search || filters.status || filters.category_id)}
                                                onClearFilters={clearFilters}
                                                createPermission="create-photo-studio-service"
                                                onCreateClick={() => openModal('add')}
                                                createButtonText={t('Create Service')}
                                                className="h-auto"
                                            />
                                        }
                                    />
                                </div>
                            </div>
                        ) : (
                            <div className="overflow-auto max-h-[70vh] p-6">
                                {services?.data?.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                        {services.data.map((service) => (
                                            <Card key={service.id} className="p-0 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-gray-200 rounded-xl group">

                                                {/* Image banner */}
                                                <div className="relative h-32 bg-gray-100 overflow-hidden shrink-0">
                                                    {service.image ? (
                                                        <img src={getImagePath(service.image)} alt={service.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                            <Briefcase className="w-10 h-10 text-gray-300" />
                                                        </div>
                                                    )}
                                                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                                                    <div className="absolute bottom-0 left-0 right-0 px-3 pb-2">
                                                        <h3 className="font-semibold text-sm text-white truncate drop-shadow">{service.name}</h3>
                                                    </div>
                                                    <span className={`absolute top-2 left-2 text-[10px]  px-2 py-1 rounded-full ${service.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                        {service.status ? t('Active') : t('Inactive')}
                                                    </span>
                                                </div>

                                                {/* Info rows */}
                                                <div className="px-4 py-3 flex-1 grid grid-rows-[auto_auto_auto] gap-2">

                                                    {/* Price */}
                                                    <div className="flex items-center gap-2 text-xs text-gray-600">
                                                        <DollarSign className="w-3.5 h-3.5 text-primary/60 shrink-0" />
                                                        <span className="font-semibold text-gray-900">{formatCurrency(service.price)}</span>
                                                    </div>

                                                    {/* Categories */}
                                                    <div className="flex items-start gap-2 text-xs text-gray-600 min-w-0">
                                                        <Tag className="w-3.5 h-3.5 text-primary/60 shrink-0 mt-0.5" />
                                                        <div className="flex flex-wrap gap-1 min-w-0">
                                                            {resolveCategories(service.service_category_ids).length > 0
                                                                ? resolveCategories(service.service_category_ids).map((name, i) => (
                                                                    <Badge key={i} variant="secondary" className="text-xs">{name}</Badge>
                                                                ))
                                                                : <span className="text-gray-400">—</span>
                                                            }
                                                            {service.service_category_ids?.length > 2 && (
                                                                <Badge variant="outline" className="text-xs">+{service.service_category_ids.length - 2}</Badge>
                                                            )}
                                                        </div>
                                                    </div>

                                                    {/* Camera kits */}<div className="flex items-start gap-2 text-xs text-gray-600 min-w-0">
                                                        <Camera className="w-3.5 h-3.5 text-primary/60 shrink-0 mt-0.5" />
                                                        <div className="flex flex-wrap gap-1 min-w-0">
                                                            {service.camera_kit_ids?.length > 0
                                                                ? service.camera_kit_ids.slice(0, 2).map((id) => {
                                                                    const kit = cameraKits.find((k) => k.id.toString() === id);
                                                                    return kit ? <Badge key={id} variant="secondary" className="text-xs">{kit.name}</Badge> : null;
                                                                })
                                                                : <span className="text-gray-400">—</span>
                                                            }
                                                            {service.camera_kit_ids?.length > 2 && (
                                                                <Badge variant="outline" className="text-xs">+{service.camera_kit_ids.length - 2}</Badge>
                                                            )}
                                                        </div>
                                                    </div>

                                                </div>

                                                <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 mt-auto">
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('view-photo-studio-service') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(service)} className="h-9 w-9 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('edit-photo-studio-service') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', service)} className="h-9 w-9 p-0 text-blue-600 hover:text-blue-700">
                                                                        <EditIcon className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('delete-photo-studio-service') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(service.id)} className="h-9 w-9 p-0 text-red-600 hover:text-red-700">
                                                                        <Trash2 className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                    </TooltipProvider>
                                                </div>
                                            </Card>
                                        ))}
                                    </div>
                                ) : (
                                    <NoRecordsFound
                                        icon={Briefcase}
                                        title={t('No Services found')}
                                        description={t('Get started by creating your first service.')}
                                        hasFilters={!!(filters.search || filters.status || filters.category_id)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-photo-studio-service"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Service')}
                                    />
                                )}
                            </div>
                        )}
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={services || { data: [], links: [], meta: {} }}
                            routeName="photo-studio-management.services.index"
                            filters={{ ...filters, per_page: perPage, view: viewMode }}
                        />
                    </CardContent>
                </Card>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Service')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />

                <Dialog open={modalState.isOpen && modalState.mode === 'add'} onOpenChange={closeModal}>
                    <Create key={modalState.isOpen ? 'create' : 'closed'} onClose={closeModal} serviceCategories={serviceCategories} cameraKits={cameraKits} />
                </Dialog>

                <Dialog open={modalState.isOpen && modalState.mode === 'edit'} onOpenChange={closeModal}>
                    {modalState.data && (
                        <Edit service={modalState.data} onClose={closeModal} serviceCategories={serviceCategories} cameraKits={cameraKits} />
                    )}
                </Dialog>

                <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                    {viewingItem && (
                        <View service={viewingItem} serviceCategories={serviceCategories} cameraKits={cameraKits} onClose={() => setViewingItem(null)} />
                    )}
                </Dialog>
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
