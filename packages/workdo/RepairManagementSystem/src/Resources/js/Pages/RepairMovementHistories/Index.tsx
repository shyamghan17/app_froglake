import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { History } from "lucide-react";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import NoRecordsFound from '@/components/no-records-found';
import { formatDateTime } from '@/utils/helpers';

interface RepairMovementHistory {
    id: number;
    movement_from: string;
    movement_to: string;
    movement_reason: string;
    date_time: string;
}

interface RepairOrderRequest {
    id: number;
    product_name: string;
}

interface MovementHistoriesIndexProps {
    movementHistories: {
        data: RepairMovementHistory[];
        links: any[];
        meta: any;
    };
    repairOrderRequest: RepairOrderRequest;
}

export default function Index() {
    const { t } = useTranslation();
    const { movementHistories, repairOrderRequest } = usePage<MovementHistoriesIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [searchTerm, setSearchTerm] = useState(urlParams.get('search') || '');
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    useFlashMessages();

    const handleSearch = () => {
        router.get(route('repair-management-system.repair-order-requests.movement-history', repairOrderRequest.id), {search: searchTerm, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('repair-management-system.repair-order-requests.movement-history', repairOrderRequest.id), {search: searchTerm, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const tableColumns = [

        {
            key: 'date_time',
            header: t('Date Time'),
            sortable: false,
            render: (value: string) => value ? formatDateTime(value) : '-'
        },
        {
            key: 'movement_from',
            header: t('From Location'),
            sortable: false,
            render: (value: string) => value || '-'
        },
        {
            key: 'movement_to',
            header: t('To Location'),
            sortable: false,
            render: (value: string) => value || '-'
        },

        {
            key: 'movement_reason',
            header: t('Reason'),
            sortable: false,
            render: (value: string) => {
                if (!value) return '-';
                
                const colorMap = {
                    'Repair': 'px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800',
                    'Testing': 'px-2 py-1 rounded-full text-sm bg-green-100 text-green-800',
                    'Irrepairable': 'px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-800',
                    'Cancel': 'px-2 py-1 rounded-full text-sm bg-red-100 text-red-800',
                };
                
                const colorClass = colorMap[value as keyof typeof colorMap] || 'px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-800';
                
                return (
                    <span className={colorClass}>
                        {value}
                    </span>
                );
            }
        }
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Repair')},
                {label: t('Order Requests'), url: route('repair-management-system.repair-order-requests.index')},
                {label: t('Movement History')}
            ]}
            pageTitle={`${t('Movement History')} - ${repairOrderRequest.product_name}`} 
        >
            <Head title={t('Movement History')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={searchTerm}
                                onChange={(value) => setSearchTerm(value)}
                                onSearch={handleSearch}
                                placeholder={t('Search Movement History...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="repair-management-system.repair-order-requests.movement-history"
                                filters={{search: searchTerm}}
                            />
                        </div>
                    </div>
                </CardContent>

                {/* Table Content */}
                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={movementHistories?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={History}
                                        title={t('No Movement History found')}
                                        description={t('No movement records available for this repair order.')}
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
                        data={movementHistories || { data: [], links: [], meta: {} }}
                        routeName="repair-management-system.repair-order-requests.movement-history"
                        filters={{search: searchTerm, per_page: perPage}}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}