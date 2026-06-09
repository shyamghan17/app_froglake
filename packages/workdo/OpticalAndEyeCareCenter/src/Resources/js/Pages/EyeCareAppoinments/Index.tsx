import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, Calendar as CalendarIcon, Download, FileImage } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import EditEyeCareAppoinment from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { EyeCareAppoinment, EyeCareAppoinmentsIndexProps, EyeCareAppoinmentFilters, EyeCareAppoinmentModalState } from './types';
import { formatDate, formatTime, formatDateTime, formatCurrency, getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { eyecareappoinments, auth, eyepatients } = usePage<EyeCareAppoinmentsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<EyeCareAppoinmentFilters>({
        doctor_name: urlParams.get('doctor_name') || '',
        status: urlParams.get('status') || '',
        appointment_type: urlParams.get('appointment_type') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [modalState, setModalState] = useState<EyeCareAppoinmentModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<EyeCareAppoinment | null>(null);

    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'optical-and-eye-care-center.eye-care-appoinments.destroy',
        defaultMessage: t('Are you sure you want to delete this eye care appoinment?')
    });

    const handleFilter = () => {
        router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'), {...filters, per_page: perPage, sort: field, direction, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            doctor_name: '',
            status: '',
            appointment_type: '',
        });
        router.get(route('optical-and-eye-care-center.eye-care-appoinments.index'), {per_page: perPage, view: viewMode});
    };

    const openModal = (mode: 'add' | 'edit', data: EyeCareAppoinment | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'patient.patient_name',
            header: t('Patient Patient_Name'),
            sortable: false,
            render: (value: any, row: any) => row.patient?.patient_name || '-'
        },
        {
            key: 'doctor_name',
            header: t('Doctor Name'),
            sortable: true,
            render: (_: any, row: any) => row.doctor?.name || '-'
        },
        {
            key: 'appointment_datetime',
            header: t('Appointment Date & time'),
            sortable: false,
            render: (value: string) => value ? formatDateTime(value) : '-'
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: any) => {
                const statuses: any = {"0":"Scheduled","1":"Confirmed","2":"Completed","3":"Cancelled"};
                const colors: any = {
                    "0": "bg-blue-100 text-blue-800",
                    "1": "bg-green-100 text-green-800",
                    "2": "bg-gray-100 text-gray-800",
                    "3": "bg-red-100 text-red-800"
                };
                return (
                    <span className={`px-2 py-1 rounded-full text-sm font-medium ${colors[value] || 'bg-gray-100 text-gray-800'}`}>
                        {statuses[value] || value}
                    </span>
                );
            }
        },
        {
            key: 'appointment_type',
            header: t('Appointment Type'),
            sortable: false,
            render: (value: any) => {
                const types: any = {"0":"Consultation","1":"Follow-up","2":"Emergency"};
                const colors: any = {
                    "0": "bg-purple-100 text-purple-800",
                    "1": "bg-yellow-100 text-yellow-800",
                    "2": "bg-orange-100 text-orange-800"
                };
                return (
                    <span className={`px-2 py-1 rounded-full text-sm font-medium ${colors[value] || 'bg-gray-100 text-gray-800'}`}>
                        {types[value] || value}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-eye-care-appoinments', 'edit-eye-care-appoinments', 'delete-eye-care-appoinments'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, eyecareappoinment: EyeCareAppoinment) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-eye-care-appoinments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(eyecareappoinment)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-eye-care-appoinments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', eyecareappoinment)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-eye-care-appoinments') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(eyecareappoinment.id)}
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
                {label: t('Optical & EyeCareCenter'), url: route('optical-and-eye-care-center.dashboard')},
                {label: t('Eye Care Appointments')}
            ]}
            pageTitle={t('Manage Eye Care Appointments')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-eye-care-appoinments') && (
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
                </TooltipProvider>
            }
        >
            <Head title={t('Eye Care Appointments')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.doctor_name}
                                onChange={(value) => setFilters({...filters, doctor_name: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Eye Care Appointments...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="optical-and-eye-care-center.eye-care-appoinments.index"
                                filters={{...filters, per_page: perPage}}
                            />
                            <PerPageSelector
                                routeName="optical-and-eye-care-center.eye-care-appoinments.index"
                                filters={{...filters, view: viewMode}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.status, filters.appointment_type].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select value={filters.status} onValueChange={(value) => setFilters({...filters, status: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Scheduled')}</SelectItem>
                                        <SelectItem value="1">{t('Confirmed')}</SelectItem>
                                        <SelectItem value="2">{t('Completed')}</SelectItem>
                                        <SelectItem value="3">{t('Cancelled')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Appointment Type')}</label>
                                <Select value={filters.appointment_type} onValueChange={(value) => setFilters({...filters, appointment_type: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Appointment Type')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Consultation')}</SelectItem>
                                        <SelectItem value="1">{t('Follow-up')}</SelectItem>
                                        <SelectItem value="2">{t('Emergency')}</SelectItem>
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

                {/* Table Content */}
                <CardContent className="p-0">
                    {viewMode === 'list' ? (
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                            <DataTable
                                data={eyecareappoinments?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={CalendarIcon}
                                        title={t('No Eye Care Appointments found')}
                                        description={t('Get started by creating your first Eye Care Appoinment.')}
                                        hasFilters={!!(filters.doctor_name || filters.status || filters.appointment_type)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-eye-care-appoinments"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Eye Care Appoinment')}
                                        className="h-auto"
                                    />
                                }
                            />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {eyecareappoinments?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                    {eyecareappoinments?.data?.map((eyecareappoinment) => (
                                        <Card key={eyecareappoinment.id} className="p-0 hover:shadow-lg transition-all duration-200 relative overflow-hidden flex flex-col h-full min-w-0">
                                            {/* Header */}
                                            <div className="p-4 bg-gradient-to-r from-primary/5 to-transparent border-b flex-shrink-0">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 bg-primary/10 rounded-lg">
                                                        <CalendarIcon className="h-5 w-5 text-primary" />
                                                    </div>
                                                    <div className="min-w-0 flex-1">
                                                        <h3 className="font-semibold text-sm text-gray-900 truncate">{eyecareappoinment.doctor?.name || '-'}</h3>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Body */}
                                            <div className="p-4 flex-1 min-h-0">
                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Patient')}</p>
                                                        <p className="font-medium text-xs truncate">{eyecareappoinment.patient?.patient_name || '-'}</p>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Date & Time')}</p>
                                                        <p className="font-medium text-xs">{eyecareappoinment.appointment_datetime ? formatDateTime(eyecareappoinment.appointment_datetime) : '-'}</p>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Status')}</p>
                                                        <span className={`px-2 py-1 rounded-full text-xs font-medium inline-block ${(() => {
                                                            const colors: any = {
                                                                "0": "bg-blue-100 text-blue-800",
                                                                "1": "bg-green-100 text-green-800",
                                                                "2": "bg-gray-100 text-gray-800",
                                                                "3": "bg-red-100 text-red-800"
                                                            };
                                                            return colors[eyecareappoinment.status] || 'bg-gray-100 text-gray-800';
                                                        })()}`}>
                                                            {(() => { const options: any = {"0":"Scheduled","1":"Confirmed","2":"Completed","3":"Cancelled"}; return options[eyecareappoinment.status] || '-'; })()}
                                                        </span>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Type')}</p>
                                                        <span className={`px-2 py-1 rounded-full text-xs font-medium inline-block ${(() => {
                                                            const colors: any = {
                                                                "0": "bg-purple-100 text-purple-800",
                                                                "1": "bg-yellow-100 text-yellow-800",
                                                                "2": "bg-orange-100 text-orange-800"
                                                            };
                                                            return colors[eyecareappoinment.appointment_type] || 'bg-gray-100 text-gray-800';
                                                        })()}`}>
                                                            {(() => { const options: any = {"0":"Consultation","1":"Follow-up","2":"Emergency"}; return options[eyecareappoinment.appointment_type] || '-'; })()}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Actions Footer */}
                                            <div className="flex justify-end gap-1 p-3 border-t bg-gray-50/50 flex-shrink-0 mt-auto">
                                                <TooltipProvider>
                                                    {auth.user?.permissions?.includes('view-eye-care-appoinments') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => setViewingItem(eyecareappoinment)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                    <Eye className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>{t('View')}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('edit-eye-care-appoinments') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => openModal('edit', eyecareappoinment)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                    <EditIcon className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>{t('Edit')}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('delete-eye-care-appoinments') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    onClick={() => openDeleteDialog(eyecareappoinment.id)}
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
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={CalendarIcon}
                                    title={t('No Eye Care Appointments found')}
                                    description={t('Get started by creating your first Eye Care Appoinment.')}
                                    hasFilters={!!(filters.doctor_name || filters.status || filters.appointment_type)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-eye-care-appoinments"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Eye Care Appoinment')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={eyecareappoinments || { data: [], links: [], meta: {} }}
                        routeName="optical-and-eye-care-center.eye-care-appoinments.index"
                        filters={{...filters, per_page: perPage, view: viewMode}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditEyeCareAppoinment
                        eyecareappoinment={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View eyecareappoinment={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Eye Care Appoinment')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
