import { useMemo, useState, type ChangeEvent } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { PettyCashReportFilters, PettyCashReportPageProps } from './types';

export default function PettyCashReport() {
    const { t } = useTranslation();
    const { expenses, totals, users, categories, filters: serverFilters } = usePage<PettyCashReportPageProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<PettyCashReportFilters>({
        start_date: serverFilters?.start_date || '',
        end_date: serverFilters?.end_date || '',
        user_id: serverFilters?.user_id || '',
        category_id: serverFilters?.category_id || '',
        type: serverFilters?.type || '',
        status: serverFilters?.status || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const handleFilter = () => {
        router.get(
            route('petty-cash-management.reports.petty-cash'),
            { ...filters, per_page: perPage, sort: sortField, direction: sortDirection },
            { preserveState: true, replace: true }
        );
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(
            route('petty-cash-management.reports.petty-cash'),
            { ...filters, per_page: perPage, sort: field, direction },
            { preserveState: true, replace: true }
        );
    };

    const clearFilters = () => {
        const cleared: PettyCashReportFilters = {
            start_date: '',
            end_date: '',
            user_id: '',
            category_id: '',
            type: '',
            status: '',
        };
        setFilters(cleared);
        router.get(route('petty-cash-management.reports.petty-cash'), { per_page: perPage });
    };

    const exportParams = useMemo(
        () => ({ ...filters, sort: sortField, direction: sortDirection }),
        [filters, sortField, sortDirection]
    );

    const buildExportUrl = (routeName: string, extraParams: Record<string, string> = {}) => {
        const baseUrl = route(routeName);
        const params = new URLSearchParams();

        Object.entries({ ...exportParams, ...extraParams }).forEach(([key, value]) => {
            if (value !== null && value !== undefined && String(value).trim() !== '') {
                params.set(key, String(value));
            }
        });

        const query = params.toString();
        return query ? `${baseUrl}?${query}` : baseUrl;
    };

    const handleExportCsv = () => {
        window.location.href = buildExportUrl('petty-cash-management.reports.petty-cash.export.csv');
    };

    const handleExportPdf = () => {
        const url = buildExportUrl('petty-cash-management.reports.petty-cash.print', { download: 'pdf' });
        window.open(url, '_blank', 'noopener,noreferrer');
    };

    const tableColumns = useMemo(
        () => [
            {
                key: 'pettycash_date',
                header: t('Petty Cash Date'),
                sortable: false,
                render: (_: any, row: any) => (row.petty_cash?.date ? formatDate(row.petty_cash.date) : '-'),
            },
            {
                key: 'pettycash_number',
                header: t('Petty Cash Number'),
                sortable: false,
                render: (_: any, row: any) => row.petty_cash?.pettycash_number || '-',
            },
            {
                key: 'reference_number',
                header: t('Request/Reimbursement Number'),
                sortable: false,
                render: (_: any, row: any) => row.request?.request_number || row.reimbursement?.reimbursement_number || '-',
            },
            {
                key: 'user',
                header: t('User'),
                sortable: false,
                render: (_: any, row: any) => row.request?.user?.name || row.reimbursement?.user?.name || '-',
            },
            {
                key: 'category',
                header: t('Category'),
                sortable: false,
                render: (_: any, row: any) => row.request?.category?.name || row.reimbursement?.category?.name || '-',
            },
            {
                key: 'type',
                header: t('Type'),
                sortable: true,
                render: (value: string) => {
                    const options: any = {
                        pettycash: t('Petty Cash'),
                        reimbursement: t('Reimbursement'),
                    };
                    return options[value] || value || '-';
                },
            },
            {
                key: 'amount',
                header: t('Amount'),
                sortable: true,
                render: (value: string) => (value ? formatCurrency(value) : '-'),
            },
            {
                key: 'approved_at',
                header: t('Approved At'),
                sortable: true,
                render: (value: string) => (value ? formatDate(value) : '-'),
            },
            {
                key: 'approved_by',
                header: t('Approved By'),
                sortable: false,
                render: (_: any, row: any) => row.approver?.name || '-',
            },
        ],
        [t]
    );

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t('Petty Cash Management') }, { label: t('Reports') }]}
            pageTitle={t('Petty Cash Report')}
            pageActions={
                <div className="flex items-center gap-2">
                    <Button variant="outline" size="sm" onClick={handleExportCsv}>
                        {t('Export CSV')}
                    </Button>
                    <Button variant="outline" size="sm" onClick={handleExportPdf}>
                        {t('Export PDF')}
                    </Button>
                </div>
            }
        >
            <Head title={t('Petty Cash Report')} />

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                <Card className="shadow-sm">
                    <CardContent className="p-4">
                        <div className="text-sm text-muted-foreground">{t('Total Amount')}</div>
                        <div className="text-xl font-semibold mt-1">{formatCurrency(totals?.total_amount || '0')}</div>
                        <div className="mt-2">
                            <Badge variant="secondary">{t('Transactions')}: {totals?.count ?? 0}</Badge>
                        </div>
                    </CardContent>
                </Card>
                <Card className="shadow-sm">
                    <CardContent className="p-4">
                        <div className="text-sm text-muted-foreground">{t('Petty Cash Amount')}</div>
                        <div className="text-xl font-semibold mt-1">{formatCurrency(totals?.pettycash_amount || '0')}</div>
                    </CardContent>
                </Card>
                <Card className="shadow-sm">
                    <CardContent className="p-4">
                        <div className="text-sm text-muted-foreground">{t('Reimbursement Amount')}</div>
                        <div className="text-xl font-semibold mt-1">{formatCurrency(totals?.reimbursement_amount || '0')}</div>
                    </CardContent>
                </Card>
                <Card className="shadow-sm">
                    <CardContent className="p-4">
                        <div className="text-sm text-muted-foreground">{t('Period')}</div>
                        <div className="text-sm font-medium mt-1">
                            {filters.start_date || '-'} {t('to')} {filters.end_date || '-'}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('Start Date')}</label>
                            <Input
                                type="date"
                                value={filters.start_date}
                                onChange={(e: ChangeEvent<HTMLInputElement>) => setFilters({ ...filters, start_date: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('End Date')}</label>
                            <Input
                                type="date"
                                value={filters.end_date}
                                onChange={(e: ChangeEvent<HTMLInputElement>) => setFilters({ ...filters, end_date: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('User')}</label>
                            <Select value={filters.user_id} onValueChange={(value: string) => setFilters({ ...filters, user_id: value })}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('All Users')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {users?.map((u) => (
                                        <SelectItem key={u.id} value={String(u.id)}>
                                            {u.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('Category')}</label>
                            <Select value={filters.category_id} onValueChange={(value: string) => setFilters({ ...filters, category_id: value })}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('All Categories')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {categories?.map((c) => (
                                        <SelectItem key={c.id} value={String(c.id)}>
                                            {c.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('Type')}</label>
                            <Select value={filters.type} onValueChange={(value: string) => setFilters({ ...filters, type: value })}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('All Types')} />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pettycash">{t('Petty Cash')}</SelectItem>
                                    <SelectItem value="reimbursement">{t('Reimbursement')}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div className="flex gap-2">
                            <Button onClick={handleFilter} size="sm">
                                {t('Apply')}
                            </Button>
                            <Button variant="outline" onClick={clearFilters} size="sm">
                                {t('Clear')}
                            </Button>
                        </div>
                    </div>
                </CardContent>

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[1000px]">
                            <DataTable
                                data={expenses?.data || []}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30 flex items-center justify-between gap-4">
                    <PerPageSelector
                        routeName="petty-cash-management.reports.petty-cash"
                        filters={{ ...filters, sort: sortField, direction: sortDirection }}
                    />
                    <Pagination
                        data={expenses || { data: [], links: [], meta: {} }}
                        routeName="petty-cash-management.reports.petty-cash"
                        filters={{ ...filters, per_page: perPage, sort: sortField, direction: sortDirection }}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
