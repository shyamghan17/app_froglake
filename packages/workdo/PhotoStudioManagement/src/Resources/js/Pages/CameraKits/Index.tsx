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
import { Plus, Edit as EditIcon, Trash2, Eye, Camera, Tag } from 'lucide-react';
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
import { PhotoStudioCameraKit, CameraKitsIndexProps, CameraKitModalState, CameraKitFilters } from './types';
import { getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { cameraKits, auth, equipmentTags, equipmentTypes } = usePage<CameraKitsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<CameraKitFilters>({
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || '',
        equipment_type_id: urlParams.get('equipment_type_id') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<CameraKitModalState>({ isOpen: false, mode: '', data: null });
    const [viewingItem, setViewingItem] = useState<PhotoStudioCameraKit | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.camera-kits.destroy',
        defaultMessage: t('Are you sure you want to delete this camera kit?'),
    });

    const openModal = (mode: 'add' | 'edit', data: PhotoStudioCameraKit | null = null) =>
        setModalState({ isOpen: true, mode, data });

    const closeModal = () => setModalState({ isOpen: false, mode: '', data: null });

    const handleFilter = () => {
        router.get(route('photo-studio-management.camera-kits.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('photo-studio-management.camera-kits.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setFilters({ search: '', status: '', equipment_type_id: '' });
        router.get(route('photo-studio-management.camera-kits.index'), { per_page: perPage, view: viewMode });
    };

    const tableColumns = [
        {
            key: 'image',
            header: t('Image'),
            sortable: false,
            render: (_: any, row: PhotoStudioCameraKit) =>
                row.image ? (
                    <img src={getImagePath(row.image)} alt={row.name} className="h-10 w-10 object-cover rounded" />
                ) : (
                    <div className="h-10 w-10 bg-gray-100 rounded flex items-center justify-center">
                        <Camera className="h-5 w-5 text-gray-400" />
                    </div>
                ),
        },
        { key: 'name', header: t('Name'), sortable: true },
        {
            key: 'equipment_type',
            header: t('Equipment Type'),
            sortable: false,
            render: (_: any, row: PhotoStudioCameraKit) => row.equipment_type?.name || '-',
        },
        {
            key: 'tags',
            header: t('Tags'),
            sortable: false,
            render: (_: any, row: PhotoStudioCameraKit) =>
                row.tags && row.tags.length > 0 ? (
                    <div className="flex flex-wrap gap-1">
                        {row.tags.slice(0, 2).map((tag, i) => {
                            const tagObj = equipmentTags.find((t) => t.id.toString() === tag);
                            return tagObj ? <Badge key={i} variant="secondary" className="text-xs">{tagObj.name}</Badge> : null;
                        })}
                        {row.tags.length > 2 && <Badge variant="outline" className="text-xs">+{row.tags.length - 2}</Badge>}
                    </div>
                ) : '-',
        },
        {
            key: 'specifications',
            header: t('Specifications'),
            sortable: false,
            render: (_: any, row: PhotoStudioCameraKit) =>
                row.specifications && row.specifications.length > 0 ? (
                    <div className="flex flex-col gap-0.5">
                        {row.specifications.slice(0, 2).map((spec, i) => (
                            <span key={i} className="text-xs text-gray-600">
                                <span className="font-medium text-gray-800">{spec.field_name}:</span> {spec.description}
                            </span>
                        ))}
                        {row.specifications.length > 2 && (
                            <span className="text-xs text-muted-foreground">+{row.specifications.length - 2} {t('more')}</span>
                        )}
                    </div>
                ) : '-',
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (_: any, row: PhotoStudioCameraKit) => (
                <span className={`px-2 py-1 rounded-full text-sm ${row.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {row.status === 'available' ? t('Available') : t('Unavailable')}
                </span>
            ),
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-photo-studio-camera-kit', 'edit-photo-studio-camera-kit', 'delete-photo-studio-camera-kit'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, row: PhotoStudioCameraKit) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-photo-studio-camera-kit') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(row)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-photo-studio-camera-kit') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', row)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-photo-studio-camera-kit') && (
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
                    { label: t('Camera Kits') },
                ]}
                pageTitle={t('Manage Camera Kits')}
                pageActions={
                    <div className="flex gap-2">
                        <TooltipProvider>
                            {auth.user?.permissions?.includes('create-photo-studio-camera-kit') && (
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
                <Head title={t('Camera Kits')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex-1 max-w-md">
                                <SearchInput
                                    value={filters.search}
                                    onChange={(value) => setFilters({ ...filters, search: value })}
                                    onSearch={handleFilter}
                                    placeholder={t('Search Camera Kits...')}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <ListGridToggle currentView={viewMode} routeName="photo-studio-management.camera-kits.index" filters={{ ...filters, per_page: perPage }} onViewChange={setViewMode} />
                                <PerPageSelector routeName="photo-studio-management.camera-kits.index" filters={{ ...filters, view: viewMode }} />
                                <div className="relative">
                                    <FilterButton showFilters={showFilters} onToggle={() => setShowFilters(!showFilters)} />
                                    {(filters.status !== '' || filters.equipment_type_id !== '') && (
                                        <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            {[filters.status, filters.equipment_type_id].filter(Boolean).length}
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
                                            <SelectItem value="available">{t('Available')}</SelectItem>
                                            <SelectItem value="unavailable">{t('Unavailable')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">{t('Equipment Type')}</label>
                                    <Select value={filters.equipment_type_id} onValueChange={(value) => setFilters({ ...filters, equipment_type_id: value })}>
                                        <SelectTrigger>
                                            <SelectValue placeholder={t('Filter by Type')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {equipmentTypes.map((type) => (
                                                <SelectItem key={type.id} value={type.id.toString()}>{type.name}</SelectItem>
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
                                        data={cameraKits?.data || []}
                                        columns={tableColumns}
                                        onSort={handleSort}
                                        sortKey={sortField}
                                        sortDirection={sortDirection as 'asc' | 'desc'}
                                        className="rounded-none"
                                        emptyState={
                                            <NoRecordsFound
                                                icon={Camera}
                                                title={t('No Camera Kits found')}
                                                description={t('Get started by creating your first Camera Kit.')}
                                                hasFilters={!!(filters.search || filters.status || filters.equipment_type_id)}
                                                onClearFilters={clearFilters}
                                                createPermission="create-photo-studio-camera-kit"
                                                onCreateClick={() => openModal('add')}
                                                createButtonText={t('Create Camera Kit')}
                                                className="h-auto"
                                            />
                                        }
                                    />
                                </div>
                            </div>
                        ) : (
                            <div className="overflow-auto max-h-[70vh] p-6">
                                {cameraKits?.data?.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                        {cameraKits.data.map((kit) => (
                                            <Card key={kit.id} className="p-0 hover:shadow-xl transition-all duration-300 overflow-hidden rounded-xl border border-gray-200 group" style={{ display: 'grid', gridTemplateRows: 'auto auto 1fr auto' }}>

                                                {/* Image with overlay */}
                                                <div className="relative h-36 bg-gray-100 overflow-hidden">
                                                    {kit.image ? (
                                                        <img src={getImagePath(kit.image)} alt={kit.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                                    ) : (
                                                        <div className="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 gap-2">
                                                            <Camera className="w-10 h-10 text-gray-300" />
                                                        </div>
                                                    )}
                                                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                                                    <div className="absolute bottom-0 left-0 right-0 px-3 pb-2">
                                                        <h3 className="font-semibold text-sm text-white truncate drop-shadow">{kit.name}</h3>
                                                        <p className="text-[11px] text-white/70 truncate">{kit.equipment_type?.name || '-'}</p>
                                                    </div>
                                                    <span className={`absolute top-2 right-2 text-[10px] px-2 py-1 rounded-full ${kit.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                        {kit.status === 'available' ? t('Available') : t('Unavailable')}
                                                    </span>
                                                </div>

                                                {/* Tags row — always rendered */}
                                                <div className="px-3 pt-2.5 pb-2 flex items-start gap-2 text-xs min-w-0">
                                                    <Tag className="w-3.5 h-3.5 text-primary/60 shrink-0 mt-0.5" />
                                                    <div className="flex flex-wrap gap-1 min-w-0">
                                                        {kit.tags && kit.tags.length > 0 ? (
                                                            <>
                                                                {kit.tags.slice(0, 2).map((tagId, i) => {
                                                                    const tag = equipmentTags.find((t) => t.id.toString() === tagId);
                                                                    return tag ? <Badge key={i} variant="secondary" className="text-xs">{tag.name}</Badge> : null;
                                                                })}
                                                                {kit.tags.length > 2 && <Badge variant="outline" className="text-xs">+{kit.tags.length - 2}</Badge>}
                                                            </>
                                                        ) : <span className="text-gray-400">—</span>}
                                                    </div>
                                                </div>

                                                {/* Specifications row — always rendered, fills remaining space */}
                                                <div className="px-3 pb-2 border-t border-gray-100 pt-2 space-y-1">
                                                    {kit.specifications && kit.specifications.length > 0
                                                        ? kit.specifications.map((spec, i) => (
                                                            <div key={i} className="flex items-start gap-1 text-xs text-gray-600 min-w-0">
                                                                <span className="font-semibold text-gray-700 shrink-0">{spec.field_name}:</span>
                                                                <span className="truncate">{spec.description}</span>
                                                            </div>
                                                        ))
                                                        : <span className="text-gray-400 text-xs">—</span>
                                                    }
                                                </div>

                                                <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 mt-auto">
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('view-photo-studio-camera-kit') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(kit)} className="h-9 w-9 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('edit-photo-studio-camera-kit') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', kit)} className="h-9 w-9 p-0 text-blue-600 hover:text-blue-700">
                                                                        <EditIcon className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('delete-photo-studio-camera-kit') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(kit.id)} className="h-9 w-9 p-0 text-red-600 hover:text-red-700">
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
                                        icon={Camera}
                                        title={t('No Camera Kits found')}
                                        description={t('Get started by creating your first Camera Kit.')}
                                        hasFilters={!!(filters.search || filters.status || filters.equipment_type_id)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-photo-studio-camera-kit"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Camera Kit')}
                                    />
                                )}
                            </div>
                        )}
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                        <Pagination
                            data={cameraKits || { data: [], links: [], meta: {} }}
                            routeName="photo-studio-management.camera-kits.index"
                            filters={{ ...filters, per_page: perPage, view: viewMode }}
                        />
                    </CardContent>
                </Card>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Camera Kit')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />

                <Dialog open={modalState.isOpen && modalState.mode === 'add'} onOpenChange={closeModal}>
                    <Create key={modalState.isOpen ? 'create' : 'closed'} onClose={closeModal} equipmentTags={equipmentTags} equipmentTypes={equipmentTypes} />
                </Dialog>

                <Dialog open={modalState.isOpen && modalState.mode === 'edit'} onOpenChange={closeModal}>
                    {modalState.data && (
                        <Edit cameraKit={modalState.data} onClose={closeModal} equipmentTags={equipmentTags} equipmentTypes={equipmentTypes} />
                    )}
                </Dialog>

                <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                    {viewingItem && (
                        <View cameraKit={viewingItem} equipmentTags={equipmentTags} onClose={() => setViewingItem(null)} />
                    )}
                </Dialog>
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
