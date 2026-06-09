import { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit, Trash2, ExternalLink, FileText } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import NoRecordsFound from '@/components/no-records-found';
import SystemSetupSidebar from '../SystemSetupSidebar';
import CreateModal from './Create';
import EditModal from './Edit';

interface CustomPage {
    id: number;
    title: string;
    slug: string;
    page_header?: string;
    page_header_description?: string;
    content: string;
    is_active: boolean;
}

interface CustomPagesProps {
    pages: {
        data: CustomPage[];
    };
    auth: any;
}

interface ModalState {
    isOpen: boolean;
    mode: 'add' | 'edit' | '';
    data: CustomPage | null;
}

export default function CustomPages() {
    const { t } = useTranslation();
    const { pages, auth } = usePage<CustomPagesProps>().props;

    const [modalState, setModalState] = useState<ModalState>({
        isOpen: false,
        mode: '',
        data: null
    });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.custom-pages.destroy',
        defaultMessage: t('Are you sure you want to delete this custom page?')
    });

    const openModal = (mode: 'add' | 'edit', data: CustomPage | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const handleEdit = (page: CustomPage) => {
        openModal('edit', page);
    };

    const tableColumns = [
        {
            key: 'title',
            header: t('Title'),
            sortable: false,
            render: (_: any, page: CustomPage) => (
                <div className="font-medium">{page.title}</div>
            )
        },
        {
            key: 'slug',
            header: t('Slug'),
            sortable: false,
            render: (_: any, page: CustomPage) => (
                <code className="text-sm bg-gray-100 px-2 py-1 rounded">{page.slug}</code>
            )
        },
        {
            key: 'is_active',
            header: t('Status'),
            sortable: false,
            render: (_: any, page: CustomPage) => (
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    page.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                }`}>
                    {page.is_active ? t('Active') : t('Inactive')}
                </span>
            )
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-booking-custom-pages', 'delete-booking-custom-pages'].includes(p)) ? [{
            key: 'actions',
            header: t('Action'),
            render: (_: any, page: CustomPage) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-booking-custom-pages') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => handleEdit(page)}
                                        className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                    >
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-booking-custom-pages') && !['privacy-policy', 'terms-conditions'].includes(page.slug) && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(page.id)}
                                        className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
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
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Bookings'), url: route('bookings.dashboard') },
                    { label: t('System Setup'), url: route('bookings.brand-settings.index') },
                    { label: t('Custom Pages') }
                ]}
                pageTitle={t('System Setup')}
            >
                <Head title={t('Custom Pages')} />

                <div className="flex flex-col md:flex-row gap-8">
                    <div className="md:w-64 flex-shrink-0">
                        <SystemSetupSidebar activeItem="custom-pages" />
                    </div>

                    <div className="flex-1">
                        <Card className="shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Custom Pages')}</h3>
                                    {auth.user?.permissions?.includes('create-booking-custom-pages') && (
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
                                <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[75vh] rounded-none w-full">
                                    <div className="min-w-[600px]">
                                        <DataTable
                                            data={pages.data}
                                            columns={tableColumns}
                                            className="rounded-none"
                                            emptyState={
                                                <NoRecordsFound
                                                    icon={FileText}
                                                    title={t('No Custom Pages found')}
                                                    description={t('Get started by creating your first Custom Page.')}
                                                    createPermission="create-booking-custom-pages"
                                                    onCreateClick={() => openModal('add')}
                                                    createButtonText={t('Create Custom Page')}
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
                    {modalState.mode === 'add' && (
                        <CreateModal onSuccess={closeModal} />
                    )}
                    {modalState.mode === 'edit' && modalState.data && (
                        <EditModal
                            customPage={modalState.data}
                            onSuccess={closeModal}
                        />
                    )}
                </Dialog>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Custom Page')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
