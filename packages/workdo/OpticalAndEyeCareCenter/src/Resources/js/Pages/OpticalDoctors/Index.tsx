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
import { Plus, Edit as EditIcon, Trash2, Eye, UserCheck as UserCheckIcon, Lock } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import EditOpticalDoctor from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { OpticalDoctor, OpticalDoctorsIndexProps, OpticalDoctorFilters, OpticalDoctorModalState } from './types';
import { formatCurrency, getImagePath } from '@/utils/helpers';

const getGenderLabel = (value: any, t: any) => {
    const options: Record<string, string> = {
        0: t("Male"),
        1: t("Female"),
        2: t("Other"),
    };
    return options[value] || value;
};

const getDoctorStatusBadge = (status: any, t: any) => {
    const statuses: Record<string, string> = {
        0: t("Active"),
        1: t("On Leave"),
        2: t("Busy"),
        3: t("Inactive"),
    };
    const colors: Record<string, string> = {
        0: "bg-green-100 text-green-800",
        1: "bg-yellow-100 text-yellow-800",
        2: "bg-blue-100 text-blue-800",
        3: "bg-red-100 text-red-800",
    };
    const text = statuses[status] || status;
    const colorClass = colors[status] || "bg-gray-100 text-gray-800";

    return { text, colorClass };
};

export default function Index() {
    const { t } = useTranslation();
    const { opticaldoctors, auth, users, opticalspecializations } = usePage<OpticalDoctorsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<OpticalDoctorFilters>({
        doctor_code: urlParams.get('doctor_code') || '',
        status: urlParams.get('status') || '',
        gender: urlParams.get('gender') || '',
        hospital_specialization_id: urlParams.get('hospital_specialization_id') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [modalState, setModalState] = useState<OpticalDoctorModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<OpticalDoctor | null>(null);

    const [showFilters, setShowFilters] = useState(false);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'optical-and-eye-care-center.optical-doctors.destroy',
        defaultMessage: t('Are you sure you want to delete this Doctor?')
    });

    const handleFilter = () => {
        router.get(route('optical-and-eye-care-center.optical-doctors.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('optical-and-eye-care-center.optical-doctors.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        const clearedFilters = {
            doctor_code: '',
            status: '',
            gender: '',
            hospital_specialization_id: '',
        };
        setFilters(clearedFilters);
        router.get(route('optical-and-eye-care-center.optical-doctors.index'), { per_page: perPage, view: viewMode });
    };

    const openModal = (mode: 'add' | 'edit', data: OpticalDoctor | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'doctor_code',
            header: t('Doctor Code'),
            sortable: true,
            render: (value: string, row: OpticalDoctor) =>
                auth.user?.permissions?.includes('view-optical-doctors') ? (
                    <span className="text-blue-600 hover:text-blue-700 cursor-pointer" onClick={() => setViewingItem(row)}>{value}</span>
                ) : (
                    `${value}`
                )
        },
        {
            key: 'user.name',
            header: t('Name'),
            sortable: false,
            render: (value: any, row: any) => {
                console.log('Row data:', row);
                console.log('User data:', row.user);
                console.log('User name:', row.user?.name);
                return row.user?.name || '-';
            }
        },
        {
            key: 'gender',
            header: t('Gender'),
            sortable: false,
            render: (value: any) => getGenderLabel(value, t)
        },
        {
            key: 'license_number',
            header: t('License Number'),
            sortable: true
        },
        {
            key: 'consultation_fee',
            header: t('Consultation Fee'),
            sortable: true,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: any) => {
                const { text, colorClass } = getDoctorStatusBadge(value, t);
                return (
                    <span className={`px-2 py-1 rounded-full text-sm font-medium ${colorClass}`}>
                        {text}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-optical-doctors', 'edit-optical-doctors', 'delete-optical-doctors'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, opticaldoctor: OpticalDoctor) => (
                <div className="flex gap-1">
                    {opticaldoctor.user?.is_disable === 1 ? (
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <div className="h-8 w-8 p-0 flex items-center justify-center text-gray-400">
                                    <Lock className="h-4 w-4" />
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('User is disabled')}</p>
                            </TooltipContent>
                        </Tooltip>
                    ) : (
                        <TooltipProvider>
                            {auth.user?.permissions?.includes('view-optical-doctors') && (
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => setViewingItem(opticaldoctor)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                            <Eye className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('View')}</p>
                                    </TooltipContent>
                                </Tooltip>
                            )}
                            {auth.user?.permissions?.includes('edit-optical-doctors') && (
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => openModal('edit', opticaldoctor)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                            <EditIcon className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Edit')}</p>
                                    </TooltipContent>
                                </Tooltip>
                            )}
                            {auth.user?.permissions?.includes('delete-optical-doctors') && (
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => openDeleteDialog(opticaldoctor.id)}
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
                    )}
                </div>
            )
        }] : [])
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Optical & Eye Care Center'), url: route('optical-and-eye-care-center.dashboard') },
                { label: t('Doctors') }
            ]}
            pageTitle={t('Manage Doctors')}
            pageActions={
                <div className="flex gap-2">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('create-optical-doctors') && (
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
                </div>
            }
        >
            <Head title={t('Doctors')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.doctor_code}
                                onChange={(value) => setFilters({ ...filters, doctor_code: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search Doctors...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="optical-and-eye-care-center.optical-doctors.index"
                                filters={{ ...filters, per_page: perPage }}
                            />
                            <PerPageSelector
                                routeName="optical-and-eye-care-center.optical-doctors.index"
                                filters={{ ...filters, view: viewMode }}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [
                                        filters.status,
                                        filters.gender
                                    ].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Gender')}</label>
                                <Select value={filters.gender} onValueChange={(value) => setFilters({ ...filters, gender: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Gender')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Male')}</SelectItem>
                                        <SelectItem value="1">{t('Female')}</SelectItem>
                                        <SelectItem value="2">{t('Other')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select value={filters.status} onValueChange={(value) => setFilters({ ...filters, status: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">{t('Active')}</SelectItem>
                                        <SelectItem value="1">{t('On Leave')}</SelectItem>
                                        <SelectItem value="2">{t('Busy')}</SelectItem>
                                        <SelectItem value="3">{t('Inactive')}</SelectItem>
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
                            <div className="min-w-[800px]">
                                <DataTable
                                    data={opticaldoctors?.data || []}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    className="rounded-none"
                                    emptyState={
                                        <NoRecordsFound
                                            icon={UserCheckIcon}
                                            title={t('No Doctors found')}
                                            description={t('Get started by creating your first Doctor.')}
                                            hasFilters={!!(filters.doctor_code || filters.status || filters.gender)}
                                            onClearFilters={clearFilters}
                                            createPermission="create-optical-doctors"
                                            onCreateClick={() => openModal('add')}
                                            createButtonText={t('Create Doctor')}
                                            className="h-auto"
                                        />
                                    }
                                />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {opticaldoctors?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                    {opticaldoctors?.data?.map((opticaldoctor: OpticalDoctor) => (
                                        <Card key={opticaldoctor.id} className="p-0 hover:shadow-lg transition-all duration-200 relative overflow-hidden flex flex-col h-full min-w-0">
                                            {/* Header */}
                                            <div className="p-4 bg-gradient-to-r from-primary/5 to-transparent border-b flex-shrink-0">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                        {opticaldoctor.user?.avatar ? (
                                                            <img
                                                                src={getImagePath(opticaldoctor.user.avatar)}
                                                                alt={opticaldoctor.user?.name || 'Doctor'}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <div className="w-full h-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                                                                {opticaldoctor.user?.name ? opticaldoctor.user.name.charAt(0).toUpperCase() : 'D'}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="min-w-0 flex-1">
                                                        <h3 className="font-semibold text-sm text-gray-900 truncate">{opticaldoctor.user?.name || '-'}</h3>
                                                        <p className="text-xs text-gray-500 truncate">{opticaldoctor.doctor_code}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Body */}
                                            <div className="p-4 flex-1 min-h-0">
                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Gender')}</p>
                                                        <p className="font-medium text-xs truncate">{getGenderLabel(opticaldoctor.gender, t)}</p>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('License')}</p>
                                                        <p className="font-medium text-xs truncate">{opticaldoctor.license_number || '-'}</p>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Fee')}</p>
                                                        <p className="font-medium text-xs truncate">{opticaldoctor.consultation_fee ? formatCurrency(opticaldoctor.consultation_fee) : '-'}</p>
                                                    </div>
                                                    <div className="text-xs min-w-0">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Status')}</p>
                                                        <span className={`px-2 py-1 rounded-full text-xs font-medium inline-block ${getDoctorStatusBadge(opticaldoctor.status, t).colorClass}`}>
                                                            {getDoctorStatusBadge(opticaldoctor.status, t).text}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Actions Footer */}
                                            <div className="flex justify-end gap-1 p-3 border-t bg-gray-50/50 flex-shrink-0 mt-auto">
                                                {opticaldoctor.user?.is_disable === 1 ? (
                                                    <Tooltip delayDuration={0}>
                                                        <TooltipTrigger asChild>
                                                            <div className="h-8 w-8 p-0 flex items-center justify-center text-gray-400">
                                                                <Lock className="h-4 w-4" />
                                                            </div>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <p>{t('User is disabled')}</p>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                ) : (
                                                    <TooltipProvider>
                                                        {auth.user?.permissions?.includes('view-optical-doctors') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(opticaldoctor)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                        <Eye className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('View')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('edit-optical-doctors') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', opticaldoctor)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                        <EditIcon className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent>
                                                                    <p>{t('Edit')}</p>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        )}
                                                        {auth.user?.permissions?.includes('delete-optical-doctors') && (
                                                            <Tooltip delayDuration={300}>
                                                                <TooltipTrigger asChild>
                                                                    <Button
                                                                        variant="ghost"
                                                                        size="sm"
                                                                        onClick={() => openDeleteDialog(opticaldoctor.id)}
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
                                                )}
                                            </div>
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={UserCheckIcon}
                                    title={t('No Doctors found')}
                                    description={t('Get started by creating your first Doctor.')}
                                    hasFilters={!!(filters.doctor_code || filters.status || filters.gender)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-optical-doctors"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Doctor')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={opticaldoctors || { data: [], links: [], meta: {} }}
                        routeName="optical-and-eye-care-center.optical-doctors.index"
                        filters={{ ...filters, per_page: perPage, view: viewMode }}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditOpticalDoctor
                        opticaldoctor={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View opticaldoctor={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Doctor')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
