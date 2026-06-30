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
import { Plus, Edit as EditIcon, Trash2, Eye, Printer, FileText as FileTextIcon } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import Create from './Create';
import EditEyeTestPrescription from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { EyeTestPrescription, EyeTestPrescriptionsIndexProps, EyeTestPrescriptionFilters, EyeTestPrescriptionModalState } from './types';
import { formatDate } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { eyetestprescriptions, auth, eyepatients, opticaldoctors } = usePage<EyeTestPrescriptionsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<EyeTestPrescriptionFilters>({
        search: urlParams.get('search') || '',
        patient_id: urlParams.get('patient_id') || '',
        doctor_name: urlParams.get('doctor_name') || '',
        test_date: urlParams.get('test_date') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [modalState, setModalState] = useState<EyeTestPrescriptionModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<EyeTestPrescription | null>(null);

    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'optical-and-eye-care-center.eye-test-prescriptions.destroy',
        defaultMessage: t('Are you sure you want to delete this eye test prescription?')
    });

    const handleFilter = () => {
        router.get(route('optical-and-eye-care-center.eye-test-prescriptions.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('optical-and-eye-care-center.eye-test-prescriptions.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            search: '',
            patient_id: '',
            doctor_name: '',
            test_date: '',
        });
        router.get(route('optical-and-eye-care-center.eye-test-prescriptions.index'), {per_page: perPage});
    };

    const openModal = (mode: 'add' | 'edit', data: EyeTestPrescription | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const tableColumns = [
        {
            key: 'patient.patient_name',
            header: t('Patient Name'),
            sortable: false,
            render: (_: unknown, row: EyeTestPrescription) => (
                <div className="space-y-0.5">
                    <p className="font-medium text-gray-900">{row.patient?.patient_name || '-'}</p>
                    <p className="text-xs text-muted-foreground">{formatDate(row.created_at)}</p>
                </div>
            ),
        },
        {
            key: 'test_date',
            header: t('Test Date'),
            sortable: false,
            render: (value: string) => (
                <span className="text-sm text-gray-700">{value ? formatDate(value) : '-'}</span>
            ),
        },
        {
            key: 'follow_up_date',
            header: t('Follow Up Date'),
            sortable: false,
            render: (value: string) => (
                <span className="text-sm text-gray-700">{value ? formatDate(value) : '-'}</span>
            ),
        },
        {
            key: 'prescription_expiry_date',
            header: t('Prescription Expiry Date'),
            sortable: false,
            render: (value: string) => {
                if (!value) return '-';
                const isExpired = new Date(value) < new Date();
                return (
                    <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                        {formatDate(value)}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-eye-test-prescriptions', 'edit-eye-test-prescriptions', 'delete-eye-test-prescriptions'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, eyetestprescription: EyeTestPrescription) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-eye-test-prescriptions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(eyetestprescription)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-eye-test-prescriptions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => window.open(`${route('optical-and-eye-care-center.eye-test-prescriptions.print', eyetestprescription.id)}?download=pdf`, '_blank')}
                                        className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                    >
                                        <Printer className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Export PDF')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-eye-test-prescriptions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', eyetestprescription)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-eye-test-prescriptions') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(eyetestprescription.id)}
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
                {label: t('Eye Test Prescriptions')}
            ]}
            pageTitle={t('Manage Eye Test Prescriptions')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-eye-test-prescriptions') && (
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
            <Head title={t('Eye Test Prescriptions')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search by patient, phone, or doctor...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="optical-and-eye-care-center.eye-test-prescriptions.index"
                                filters={{...filters}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.patient_id, filters.doctor_name, filters.test_date].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Patient')}</label>
                                <Select value={filters.patient_id} onValueChange={(value) => setFilters({...filters, patient_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Patient')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {eyepatients.map((patient: any) => (
                                            <SelectItem key={patient.id} value={patient.id.toString()}>
                                                {patient.patient_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Doctor')}</label>
                                <Select value={filters.doctor_name} onValueChange={(value) => setFilters({...filters, doctor_name: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Doctor')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {opticaldoctors.map((doctor: any) => (
                                            <SelectItem key={doctor.id} value={doctor.id.toString()}>
                                                {doctor.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Test Date')}</label>
                                <DatePicker
                                    value={filters.test_date}
                                    onChange={(date) => setFilters({...filters, test_date: date})}
                                    placeholder={t('Select Test Date')}
                                />
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
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[780px]">
                        <DataTable
                            data={eyetestprescriptions?.data || []}
                            columns={tableColumns}
                            onSort={handleSort}
                            sortKey={sortField}
                            sortDirection={sortDirection as 'asc' | 'desc'}
                            className="rounded-none"
                            emptyState={
                                <NoRecordsFound
                                    icon={FileTextIcon}
                                    title={t('No Eye Test Prescriptions found')}
                                    description={t('Get started by creating your first Eye Test Prescription.')}
                                    hasFilters={!!(filters.search || filters.patient_id || filters.doctor_name || filters.test_date)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-eye-test-prescriptions"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Eye Test Prescription')}
                                    className="h-auto"
                                />
                            }
                        />
                        </div>
                    </div>
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={eyetestprescriptions || { data: [], links: [], meta: {} }}
                        routeName="optical-and-eye-care-center.eye-test-prescriptions.index"
                        filters={{...filters, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditEyeTestPrescription
                        eyetestprescription={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View eyetestprescription={viewingItem} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Eye Test Prescription')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
