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
import { Plus, Edit as EditIcon, Trash2, Eye, Wrench as WrenchIcon, Download, FileImage, History, Play, X, CheckCircle, AlertTriangle, Package, FileText, Settings, TestTube, Square } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import EditRepairOrderRequest from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { RepairOrderRequest, RepairOrderRequestsIndexProps, RepairOrderRequestFilters, RepairOrderRequestModalState } from './types';
import { formatDate } from '@/utils/helpers';
import { useForm } from '@inertiajs/react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/ui/input-error';

export default function Index() {
    const { t } = useTranslation();
    const { repairorderrequests, auth, repairtechnicians, repairstatuses } = usePage<RepairOrderRequestsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState<RepairOrderRequestFilters>({
        product_name: urlParams.get('product_name') || '',
        customer_name: urlParams.get('customer_name') || '',
        customer_email: urlParams.get('customer_email') || '',
        repair_technician: urlParams.get('repair_technician') || '',
        status: urlParams.get('status') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [modalState, setModalState] = useState<RepairOrderRequestModalState>({
        isOpen: false,
        mode: '',
        data: null
    });
    const [viewingItem, setViewingItem] = useState<RepairOrderRequest | null>(null);
    const [invoiceModal, setInvoiceModal] = useState<{isOpen: boolean, repairOrder: RepairOrderRequest | null}>({isOpen: false, repairOrder: null});

    const [showFilters, setShowFilters] = useState(false);



    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'repair-management-system.repair-order-requests.destroy',
        defaultMessage: t('Are you sure you want to delete this order request?')
    });

    const handleFilter = () => {
        router.get(route('repair-management-system.repair-order-requests.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('repair-management-system.repair-order-requests.index'), {...filters, per_page: perPage, sort: field, direction, view: viewMode}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            product_name: '',
            customer_name: '',
            customer_email: '',
            repair_technician: '',
            status: '',
        });
        router.get(route('repair-management-system.repair-order-requests.index'), {per_page: perPage, view: viewMode});
    };

    const openModal = (mode: 'add' | 'edit', data: RepairOrderRequest | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const handleStatusChange = (id: number, status: number) => {
        router.get(route('repair-management-system.repair-order-requests.steps-change', [id, status]));
    };

    const showMovementHistory = (id: number) => {
        router.get(route('repair-management-system.repair-order-requests.movement-history', id));
    };

    const openInvoiceModal = (repairOrder: RepairOrderRequest) => {
        setInvoiceModal({isOpen: true, repairOrder});
    };

    const closeInvoiceModal = () => {
        setInvoiceModal({isOpen: false, repairOrder: null});
    };

    const tableColumns = [
        {
            key: 'product_name',
            header: t('Product Name'),
            sortable: true
        },
        {
            key: 'customer_name',
            header: t('Customer Name'),
            sortable: true
        },
        {
            key: 'customer_mobile_no',
            header: t('Mobile No'),
            sortable: false
        },
        {
            key: 'location',
            header: t('Location'),
            sortable: false
        },
        {
            key: 'expiry_date',
            header: t('Expiry Date'),
            sortable: false,
            render: (value: string) => {
                if (!value) return '-';
                const isExpired = new Date(value) <= new Date();
                return (
                    <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                        {formatDate(value)}
                    </span>
                );
            }
        },
        {
            key: 'repair_technician',
            header: t('Technician'),
            sortable: false,
            render: (value: string, row: any) => {
                const modelData = repairtechnicians?.find(item => item.id.toString() === value?.toString());
                return modelData?.name || value || '-';
            }
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: string, row: any) => {
                const modelData = repairstatuses?.find(item => item.id.toString() === value?.toString());
                const statusColors = {
                    '0': 'bg-yellow-100 text-yellow-800', // Pending
                    '1': 'bg-green-100 text-green-800',   // Start Repair
                    '2': 'bg-green-100 text-green-800',   // End Repair
                    '3': 'bg-green-100 text-green-800',   // Start Testing
                    '4': 'bg-green-100 text-green-800',   // End Testing
                    '5': 'bg-gray-100 text-gray-800',     // Irrepairable
                    '6': 'bg-red-100 text-red-800',       // Cancel
                    '7': 'bg-blue-100 text-blue-800'      // Invoice Created
                };
                const colorClass = statusColors[value?.toString()] || 'bg-gray-100 text-gray-800';
                return (
                    <span className={`px-2 py-1 rounded-full text-sm whitespace-nowrap ${colorClass}`}>
                        {modelData?.name || value || '-'}
                    </span>
                );
            }
        },

        ...(auth.user?.permissions?.some((p: string) => ['edit-repair-order-requests', 'delete-repair-order-requests', 'update-status-repair-order-requests', 'view-repair-order-requests', 'manage-repair-product-parts', 'view-history-repair-order-requests', 'create-repair-invoices'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, repairorderrequest: RepairOrderRequest) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {/* Pending (0): Start Repair, Cancel Order */}
                        {repairorderrequest.status === 0 && auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                            <>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => handleStatusChange(repairorderrequest.id, 1)}
                                            className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                        >
                                            <Play className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Start Repair')}</p>
                                    </TooltipContent>
                                </Tooltip>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => handleStatusChange(repairorderrequest.id, 6)}
                                            className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                        >
                                            <X className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Cancel Order')}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </>
                        )}
                        
                        {/* Start Repairing (1): End Repair, Add Parts, Irrepairable, Cancel, Movement History */}
                        {repairorderrequest.status === 1 && (
                            <>
                                {auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                    <>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 2)}
                                                    className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                                >
                                                    <Square className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('End Repair')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 5)}
                                                    className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                                >
                                                    <AlertTriangle className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Irrepairable')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 6)}
                                                    className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                                >
                                                    <X className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Cancel Order')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </>
                                )}
                                {auth.user?.permissions?.includes('manage-repair-product-parts') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => router.get(route('repair-management-system.repair-product-parts.index', repairorderrequest.id))}
                                                className="h-8 w-8 p-0 text-indigo-600 hover:text-indigo-700"
                                            >
                                                <Package className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Add Parts')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                                {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => showMovementHistory(repairorderrequest.id)}
                                                className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                            >
                                                <History className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Movement History')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </>
                        )}
                        
                        {/* End Repairing (2): Start Testing, Irrepairable, Cancel, Movement History */}
                        {repairorderrequest.status === 2 && (
                            <>
                                {auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                    <>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 3)}
                                                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                                >
                                                    <Play className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Start Testing')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 5)}
                                                    className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                                >
                                                    <AlertTriangle className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Irrepairable')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 6)}
                                                    className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                                >
                                                    <X className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Cancel Order')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </>
                                )}
                                {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => showMovementHistory(repairorderrequest.id)}
                                                className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                            >
                                                <History className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Movement History')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </>
                        )}
                        
                        {/* Start Testing (3): End Testing, Irrepairable, Cancel, Movement History */}
                        {repairorderrequest.status === 3 && (
                            <>
                                {auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                    <>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 4)}
                                                    className="h-8 w-8 p-0 text-purple-600 hover:text-purple-700"
                                                >
                                                    <Square className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('End Testing')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 5)}
                                                    className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                                >
                                                    <AlertTriangle className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Irrepairable')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleStatusChange(repairorderrequest.id, 6)}
                                                    className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                                >
                                                    <X className="h-4 w-4" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Cancel Order')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </>
                                )}
                                {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => showMovementHistory(repairorderrequest.id)}
                                                className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                            >
                                                <History className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Movement History')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </>
                        )}
                        
                        {/* End Testing (4): Movement History, Create Invoice */}
                        {repairorderrequest.status === 4 && (
                            <>
                                {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => showMovementHistory(repairorderrequest.id)}
                                                className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                            >
                                                <History className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Movement History')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                                {!repairorderrequest.invoice && auth.user?.permissions?.includes('create-repair-invoices') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => openInvoiceModal(repairorderrequest)}
                                                className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                            >
                                                <FileText className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Create Invoice')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </>
                        )}
                        
                        {/* Invoice Created (7): Movement History */}
                        {repairorderrequest.status === 7 && auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => showMovementHistory(repairorderrequest.id)}
                                        className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                    >
                                        <History className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Movement History')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        
                        {/* Irrepairable (5): Start Repair, Add Parts, Movement History */}
                        {repairorderrequest.status === 5 && (
                            <>
                                {auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => handleStatusChange(repairorderrequest.id, 1)}
                                                className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                            >
                                                <Play className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Start Repair')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                                {auth.user?.permissions?.includes('manage-repair-product-parts') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => router.get(route('repair-management-system.repair-product-parts.index', repairorderrequest.id))}
                                                className="h-8 w-8 p-0 text-indigo-600 hover:text-indigo-700"
                                            >
                                                <Package className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Product parts edit')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                                {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => showMovementHistory(repairorderrequest.id)}
                                                className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700"
                                            >
                                                <History className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Movement History')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </>
                        )}
                        {auth.user?.permissions?.includes('view-repair-order-requests') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(repairorderrequest)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-repair-order-requests') && repairorderrequest.status !== 6 && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', repairorderrequest)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-repair-order-requests') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(repairorderrequest.id)}
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
                {label: t('Repair')},
                {label: t('Order Requests')}
            ]}
            pageTitle={t('Manage Order Requests')}
            pageActions={
                <TooltipProvider>
                    {auth.user?.permissions?.includes('create-repair-order-requests') && (
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
            <Head title={t('Repair Order Requests')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.product_name}
                                onChange={(value) => setFilters({...filters, product_name: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search Order Requests...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle
                                currentView={viewMode}
                                routeName="repair-management-system.repair-order-requests.index"
                                filters={{...filters, per_page: perPage}}
                            />
                            <PerPageSelector
                                routeName="repair-management-system.repair-order-requests.index"
                                filters={{...filters, view: viewMode}}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.repair_technician, filters.status].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Repair Technician')}</label>
                                <Select value={filters.repair_technician} onValueChange={(value) => setFilters({...filters, repair_technician: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Repair Technician')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {repairtechnicians?.map((item: any) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select value={filters.status} onValueChange={(value) => setFilters({...filters, status: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {repairstatuses?.map((item: any) => (
                                            <SelectItem key={item.id} value={item.id.toString()}>
                                                {item.name}
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

                {/* Table Content */}
                <CardContent className="p-0">
                    {viewMode === 'list' ? (
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                            <DataTable
                                data={repairorderrequests?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={WrenchIcon}
                                        title={t('No Order Requests found')}
                                        description={t('Get started by creating your first Order Requests.')}
                                        hasFilters={!!(filters.product_name || filters.customer_name || filters.customer_email || filters.repair_technician || filters.status)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-repair-order-requests"
                                        onCreateClick={() => openModal('add')}
                                        createButtonText={t('Create Order Request')}
                                        className="h-auto"
                                    />
                                }
                            />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {repairorderrequests?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                                    {repairorderrequests?.data?.map((repairorderrequest) => {
                                        const statusInfo = (() => {
                                            const status = repairstatuses?.find(item => item.id.toString() === repairorderrequest.status?.toString());
                                            const statusColors = {
                                                '0': 'bg-yellow-100 text-yellow-800',
                                                '1': 'bg-green-100 text-green-800',
                                                '2': 'bg-green-100 text-green-800',
                                                '3': 'bg-green-100 text-green-800',
                                                '4': 'bg-green-100 text-green-800',
                                                '5': 'bg-gray-100 text-gray-800',
                                                '6': 'bg-red-100 text-red-800',
                                                '7': 'bg-blue-100 text-blue-800'
                                            };
                                            return {
                                                name: status?.name || '-',
                                                colorClass: statusColors[repairorderrequest.status?.toString()] || 'bg-gray-100 text-gray-800'
                                            };
                                        })();
                                        
                                        return (
                                        <Card key={repairorderrequest.id} className="p-0 hover:shadow-lg transition-all duration-200 relative overflow-hidden flex flex-col h-full">
                                            
                                            {/* Header */}
                                            <div className="p-4 bg-gradient-to-r from-primary/5 to-transparent border-b flex-shrink-0">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 bg-primary/10 rounded-lg">
                                                        <WrenchIcon className="h-5 w-5 text-primary" />
                                                    </div>
                                                    <div>
                                                        <h3 className="font-semibold text-sm text-gray-900">{repairorderrequest.product_name}</h3>
                                                        <p className="text-xs font-medium text-gray">{repairorderrequest.customer_name || '-'}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Body */}
                                            <div className="p-4 flex-1 min-h-0">
                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Mobile No')}</p>
                                                        <p className="font-medium text-xs">{repairorderrequest.customer_mobile_no || '-'}</p>
                                                    </div>
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Location')}</p>
                                                        <p className="font-medium text-xs">{repairorderrequest.location || '-'}</p>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 gap-4 mb-4">
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Date')}</p>
                                                        <p className="font-medium text-xs">{repairorderrequest.date ? formatDate(repairorderrequest.date) : '-'}</p>
                                                    </div>
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Expiry Date')}</p>
                                                        <p className={`font-medium text-xs ${repairorderrequest.expiry_date && new Date(repairorderrequest.expiry_date) < new Date() ? 'text-red-600' : ''}`}>
                                                            {repairorderrequest.expiry_date ? formatDate(repairorderrequest.expiry_date) : '-'}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Technician')}</p>
                                                        <p className="font-medium text-xs">{repairtechnicians?.find(item => item.id.toString() === repairorderrequest.repair_technician?.toString())?.name || '-'}</p>
                                                    </div>
                                                    <div className="text-xs">
                                                        <p className="text-muted-foreground mb-1 text-xs uppercase tracking-wide">{t('Status')}</p>
                                                        <span className={`px-3 py-1 rounded-full text-xs font-medium ${statusInfo.colorClass}`}>
                                                            {statusInfo.name}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Actions Footer */}
                                            <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 flex-shrink-0 mt-auto">
                                                <TooltipProvider>
                                                    {/* Status-based action buttons */}
                                                    {repairorderrequest.status === 0 && auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                                        <>
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => handleStatusChange(repairorderrequest.id, 1)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                        <Play className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Start Repair')}</p></TooltipContent>
                                                            </Tooltip>
                                                            <Tooltip delayDuration={0}>
                                                                <TooltipTrigger asChild>
                                                                    <Button variant="ghost" size="sm" onClick={() => handleStatusChange(repairorderrequest.id, 6)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                                                        <X className="h-4 w-4" />
                                                                    </Button>
                                                                </TooltipTrigger>
                                                                <TooltipContent><p>{t('Cancel Order')}</p></TooltipContent>
                                                            </Tooltip>
                                                        </>
                                                    )}
                                                    
                                                    {repairorderrequest.status === 1 && (
                                                        <>
                                                            {auth.user?.permissions?.includes('update-status-repair-order-requests') && (
                                                                <>
                                                                    <Tooltip delayDuration={0}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => handleStatusChange(repairorderrequest.id, 2)} className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700">
                                                                                <Square className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('End Repair')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                    <Tooltip delayDuration={0}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => handleStatusChange(repairorderrequest.id, 5)} className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700">
                                                                                <AlertTriangle className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('Irrepairable')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                    <Tooltip delayDuration={0}>
                                                                        <TooltipTrigger asChild>
                                                                            <Button variant="ghost" size="sm" onClick={() => handleStatusChange(repairorderrequest.id, 6)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                                                                <X className="h-4 w-4" />
                                                                            </Button>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent><p>{t('Cancel Order')}</p></TooltipContent>
                                                                    </Tooltip>
                                                                </>
                                                            )}
                                                            {auth.user?.permissions?.includes('manage-repair-product-parts') && (
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button variant="ghost" size="sm" onClick={() => router.get(route('repair-management-system.repair-product-parts.index', repairorderrequest.id))} className="h-8 w-8 p-0 text-indigo-600 hover:text-indigo-700">
                                                                            <Package className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent><p>{t('Add Parts')}</p></TooltipContent>
                                                                </Tooltip>
                                                            )}
                                                            {auth.user?.permissions?.includes('view-history-repair-order-requests') && (
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button variant="ghost" size="sm" onClick={() => showMovementHistory(repairorderrequest.id)} className="h-8 w-8 p-0 text-gray-600 hover:text-gray-700">
                                                                            <History className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent><p>{t('Movement History')}</p></TooltipContent>
                                                                </Tooltip>
                                                            )}
                                                        </>
                                                    )}
                                                    
                                                    {/* Common actions for all statuses */}
                                                    {auth.user?.permissions?.includes('view-repair-order-requests') && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => setViewingItem(repairorderrequest)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                                                    <Eye className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('edit-repair-order-requests') && repairorderrequest.status !== 6 && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => openModal('edit', repairorderrequest)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                                                    <EditIcon className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('delete-repair-order-requests') && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(repairorderrequest.id)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                                                    <Trash2 className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('Delete')}</p></TooltipContent>
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
                                    icon={WrenchIcon}
                                    title={t('No Order Requests found')}
                                    description={t('Get started by creating your first Order Request.')}
                                    hasFilters={!!(filters.product_name || filters.customer_name || filters.customer_email || filters.repair_technician || filters.status)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-repair-order-requests"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Create Order Request')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={repairorderrequests || { data: [], links: [], meta: {} }}
                        routeName="repair-management-system.repair-order-requests.index"
                        filters={{...filters, per_page: perPage, view: viewMode}}
                    />
                </CardContent>
            </Card>

            <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
                {modalState.mode === 'add' && (
                    <Create onSuccess={closeModal} />
                )}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditRepairOrderRequest
                        repairorderrequest={modalState.data}
                        onSuccess={closeModal}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View repairorderrequest={viewingItem} />}
            </Dialog>

            <Dialog open={invoiceModal.isOpen} onOpenChange={closeInvoiceModal}>
                {invoiceModal.repairOrder && <CreateInvoiceModal repairOrder={invoiceModal.repairOrder} onSuccess={closeInvoiceModal} />}
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Order Request')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}

function CreateInvoiceModal({ repairOrder, onSuccess }: { repairOrder: RepairOrderRequest, onSuccess: () => void }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        repair_charge: ''
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('repair-management-system.repair-invoices.create-from-order', repairOrder.id), {
            onSuccess: () => {
                onSuccess();
            },
            onError: () => {
                // Error handling is done by flash messages
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Invoice')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label required>{t('Repair Charge')}</Label>
                    <Input
                        id="repair_charge"
                        type="number"
                        step="0.01"
                        value={data.repair_charge}
                        onChange={(e) => setData('repair_charge', e.target.value)}
                        placeholder={t('Enter Repair Charge')}
                    />
                    <InputError message={errors.repair_charge} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Creating...') : t('Create Invoice')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}