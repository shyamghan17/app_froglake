import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Receipt, Eye, Download } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";

import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { formatCurrency } from '@/utils/helpers';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Badge } from '@/components/ui/badge';
import NoRecordsFound from '@/components/no-records-found';


interface ReceiptData {
    id: number;
    beauty_booking_id: number;
    name: string;
    service: string;
    number: string;
    gender: string;
    start_time: string;
    end_time: string;
    price: number;
    payment_type: string;
    created_at: string;
    beauty_booking?: {
        beauty_service?: {
            name: string;
        };
    };
}

interface PageProps {
    receipts: {
        data: ReceiptData[];
        links: any[];
        meta: any;
    };
    auth: any;
}



export default function Index() {
    const { t } = useTranslation();
    const { receipts, auth } = usePage<PageProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [searchName, setSearchName] = useState(urlParams.get('name') || '');
    const [selectedReceipt, setSelectedReceipt] = useState<ReceiptData | null>(null);
    const [showViewModal, setShowViewModal] = useState(false);

    useFlashMessages();

    const handleSearch = () => {
        router.get(route('beauty-spa-management.beauty-receipt.index'), { name: searchName, per_page: perPage, sort: sortField, direction: sortDirection }, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('beauty-spa-management.beauty-receipt.index'), { name: searchName, per_page: perPage, sort: field, direction }, {
            preserveState: true,
            replace: true
        });
    };



    const tableColumns = [
        {
            key: 'name',
            header: t('Name'),
            sortable: true
        },
        {
            key: 'service',
            header: t('Service'),
            sortable: false,
            render: (_: any, receipt: ReceiptData) => receipt.beauty_booking?.beauty_service?.name || receipt.service || '-'
        },
        {
            key: 'number',
            header: t('Phone Number'),
            sortable: false
        },
        {
            key: 'gender',
            header: t('Gender'),
            sortable: false
        },
        {
            key: 'price',
            header: t('Price'),
            sortable: false,
            render: (value: number) => value ? formatCurrency(value) : '-'
        },
        {
            key: 'actions',
            header: t('Actions'),
            render: (_: any, receipt: ReceiptData) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('download-beauty-receipt') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => {
                                        const printUrl = route('beauty-spa-management.beauty-receipt.download', receipt.id) + '?download=pdf';
                                        window.open(printUrl, '_blank');
                                    }} className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700">
                                        <Download className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Download Pdf')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('view-beauty-services') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => {
                                            setSelectedReceipt(receipt);
                                            setShowViewModal(true);
                                        }}
                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                    >
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            )
        }
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('Beauty Receipt') }
            ]}
            pageTitle={t('Manage Beauty Receipt')}
        >
            <Head title={t('Beauty Receipt')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={searchName}
                                onChange={(value) => setSearchName(value)}
                                onSearch={handleSearch}
                                placeholder={t('Search Receipts...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="beauty-spa-management.beauty-receipt.index"
                                filters={{ name: searchName }}
                            />
                        </div>
                    </div>


                </CardContent>

                {/* Table Content */}
                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={receipts?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Receipt}
                                        title={t('No Receipts found')}
                                        description={t('No beauty receipts available.')}
                                        hasFilters={!!searchName}
                                        onClearFilters={() => {
                                            setSearchName('');
                                            router.get(route('beauty-spa-management.beauty-receipt.index'), { per_page: perPage });
                                        }}
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
                        data={receipts || { data: [], links: [], meta: {} }}
                        routeName="beauty-spa-management.beauty-receipt.index"
                        filters={{ name: searchName, per_page: perPage }}
                    />
                </CardContent>
            </Card>

            {/* View Receipt Modal */}
            <Dialog open={showViewModal} onOpenChange={setShowViewModal}>
                <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader className="pb-4 border-b">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-primary/10 rounded-lg">
                                <Receipt className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <DialogTitle className="text-xl font-semibold">{t('Receipt Details')}</DialogTitle>
                            </div>
                        </div>
                    </DialogHeader>

                    {selectedReceipt && (
                        <div className="overflow-y-auto flex-1 p-4 space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Name')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.name || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Phone Number')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.number || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Service')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.beauty_booking?.beauty_service?.name || selectedReceipt.service || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Gender')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.gender || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Start Time')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.start_time || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('End Time')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.end_time || '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Price')}</label>
                                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.price ? formatCurrency(selectedReceipt.price) : '-'}</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-gray-700">{t('Payment Type')}</label>
                                    <div className="bg-gray-50 p-2 rounded">
                                        <Badge variant="secondary">{selectedReceipt.payment_type || '-'}</Badge>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium text-gray-700">{t('Date Created')}</label>
                                <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{selectedReceipt.created_at ? new Date(selectedReceipt.created_at).toLocaleDateString() : '-'}</p>
                            </div>
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}