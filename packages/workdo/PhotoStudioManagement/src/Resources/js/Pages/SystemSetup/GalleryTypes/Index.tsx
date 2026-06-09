import { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Dialog } from '@/components/ui/dialog';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit, Trash2, Images } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import Create from './Create';
import EditGalleryType from './Edit';
import NoRecordsFound from '@/components/no-records-found';
import { PhotoStudioGalleryType, GalleryTypesIndexProps } from './types';
import SystemSetupSidebar from '../SystemSetupSidebar';

interface ModalState {
    isOpen: boolean;
    mode: string;
    data: PhotoStudioGalleryType | null;
}

export default function Index() {
    const { t } = useTranslation();
    const { galleryTypes, auth } = usePage<GalleryTypesIndexProps>().props;

    const [modalState, setModalState] = useState<ModalState>({ isOpen: false, mode: '', data: null });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.gallery-types.destroy',
        defaultMessage: t('Are you sure you want to delete this gallery type?'),
    });

    const openModal = (mode: 'add' | 'edit', data: PhotoStudioGalleryType | null = null) =>
        setModalState({ isOpen: true, mode, data });

    const closeModal = () => setModalState({ isOpen: false, mode: '', data: null });

    const tableColumns = [
        { key: 'name', header: t('Name'), sortable: false },
        {
            key: 'description',
            header: t('Description'),
            sortable: false,
            render: (_: any, row: PhotoStudioGalleryType) => row.description || '-',
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: boolean) => (
                <span className={`px-2 py-1 rounded-full text-xs ${value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {value ? t('Active') : t('Inactive')}
                </span>
            ),
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-photo-studio-gallery-type', 'delete-photo-studio-gallery-type'].includes(p)) ? [{
            key: 'actions',
            header: t('Action'),
            render: (_: any, row: PhotoStudioGalleryType) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-photo-studio-gallery-type') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', row)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-photo-studio-gallery-type') && (
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
                    { label: t('System Setup') },
                    { label: t('Gallery Types') },
                ]}
                pageTitle={t('System Setup')}
            >
                <Head title={t('Gallery Types')} />

                <div className="flex flex-col md:flex-row gap-8">
                    <div className="md:w-64 flex-shrink-0">
                        <SystemSetupSidebar activeItem="gallery-types" />
                    </div>

                    <div className="flex-1">
                        <Card className="shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Gallery Types')}</h3>
                                    {auth.user?.permissions?.includes('create-photo-studio-gallery-type') && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button size="sm" onClick={() => openModal('add')}>
                                                    <Plus className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Create')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                </div>
                                <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[75vh] rounded-none w-full">
                                    <div className="min-w-[600px]">
                                        <DataTable
                                            data={galleryTypes?.data || []}
                                            columns={tableColumns}
                                            className="rounded-none"
                                            emptyState={
                                                <NoRecordsFound
                                                    icon={Images}
                                                    title={t('No Gallery Types found')}
                                                    description={t('Get started by creating your first Gallery Type.')}
                                                    createPermission="create-photo-studio-gallery-type"
                                                    onCreateClick={() => openModal('add')}
                                                    createButtonText={t('Create Gallery Type')}
                                                    className="h-auto"
                                                />
                                            }
                                        />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                    {modalState.mode === 'add' && <Create onClose={closeModal} />}
                    {modalState.mode === 'edit' && modalState.data && (
                        <EditGalleryType galleryType={modalState.data} onClose={closeModal} />
                    )}
                </Dialog>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Gallery Type')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
