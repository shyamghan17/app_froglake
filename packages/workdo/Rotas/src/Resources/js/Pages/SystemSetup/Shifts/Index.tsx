import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit, Trash2, Clock } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import CreateShift from './Create';
import EditShift from './Edit';
import NoRecordsFound from '@/components/no-records-found';
import { Shift, ShiftsIndexProps, ShiftModalState } from './types';
import SystemSetupSidebar from "../SystemSetupSidebar";
import { formatTime } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { shifts, auth } = usePage<ShiftsIndexProps>().props;

    const { pageProps } = usePage().props as any;

    const [modalState, setModalState] = useState<ShiftModalState>({
        isOpen: false,
        mode: '',
        data: null
    });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'rotas.shifts.destroy',
        defaultMessage: t('Are you sure you want to delete this Shift?')
    });

    const openModal = (mode: 'add' | 'edit', data: Shift | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'shift_name',
            header: t('Shift Name'),
            sortable: false
        },
        {
            key: 'start_time',
            header: t('Start Time'),
            sortable: false,
            render: (value: string) => formatTime(value, pageProps)
        },
        {
            key: 'end_time',
            header: t('End Time'),
            sortable: false,
            render: (value: string) => formatTime(value, pageProps)
        },
        {
            key: 'break_start_time',
            header: t('Break Start'),
            sortable: false,
            render: (value: string) => formatTime(value, pageProps)
        },
        {
            key: 'break_end_time',
            header: t('Break End'),
            sortable: false,
            render: (value: string) => formatTime(value, pageProps)
        },
        {
            key: 'is_night_shift',
            header: t('Night Shift'),
            sortable: false,
            render: (value: boolean) => (
                <span className={`px-2 py-1 rounded-full text-sm ${
                    value ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                }`}>
                    {value ? t('Yes') : t('No')}
                </span>
            )
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-rotas-shifts', 'delete-rotas-shifts'].includes(p)) ? [{
            key: 'actions',
            header: t('Action'),
            render: (_: any, shift: Shift) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-rotas-shifts') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', shift)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-rotas-shifts') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(shift.id)}
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
                    { label: t('Rotas'), url: route('rotas.dashboard.index') },
                    {label: t('System Setup')},
                    {label: t('Shifts')}
                ]}
                pageTitle={t('System Setup')}
            >
                <Head title={t('Shifts')} />

                <div className="flex flex-col md:flex-row gap-8">
                    <div className="md:w-64 flex-shrink-0">
                        <SystemSetupSidebar activeItem="shifts" />
                    </div>

                    <div className="flex-1">
                        <Card className="shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-medium">{t('Shifts')}</h3>
                                    {auth.user?.permissions?.includes('create-rotas-shifts') && (
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
                                    <div className="min-w-[800px]">
                                        <DataTable
                                            key={shifts.length}
                                            data={shifts.data || shifts}
                                            columns={tableColumns}
                                            className="rounded-none"
                                            emptyState={
                                                <NoRecordsFound
                                                    icon={Clock}
                                                    title={t('No Shifts found')}
                                                    description={t('Get started by creating your first Shift.')}
                                                    createPermission="create-rotas-shifts"
                                                    onCreateClick={() => openModal('add')}
                                                    createButtonText={t('Create Shift')}
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
                        <CreateShift onSuccess={closeModal} />
                    )}
                    {modalState.mode === 'edit' && modalState.data && (
                        <EditShift
                            shift={modalState.data}
                            onSuccess={closeModal}
                        />
                    )}
                </Dialog>

                <ConfirmationDialog
                    open={deleteState.isOpen}
                    onOpenChange={closeDeleteDialog}
                    title={t('Delete Shift')}
                    message={deleteState.message}
                    confirmText={t('Delete')}
                    onConfirm={confirmDelete}
                    variant="destructive"
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}