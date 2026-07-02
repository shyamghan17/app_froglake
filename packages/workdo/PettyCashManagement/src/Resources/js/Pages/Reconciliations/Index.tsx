import { useMemo, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { PettyCashReconciliationFilters, ReconciliationsIndexProps } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { auth, reconciliations, filters: serverFilters } = usePage<ReconciliationsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<PettyCashReconciliationFilters>({
        period_start: serverFilters?.period_start || '',
        period_end: serverFilters?.period_end || '',
        locked: serverFilters?.locked || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');

    const handleFilter = () => {
        router.get(
            route('petty-cash-management.reconciliations.index'),
            { ...filters, per_page: perPage },
            { preserveState: true, replace: true }
        );
    };

    const clearFilters = () => {
        const cleared: PettyCashReconciliationFilters = {
            period_start: '',
            period_end: '',
            locked: '',
        };
        setFilters(cleared);
        router.get(route('petty-cash-management.reconciliations.index'), { per_page: perPage });
    };

    const tableColumns = useMemo(
        () => [
            {
                key: 'period_start',
                header: t('Start'),
                sortable: false,
                render: (value: string) => (value ? formatDate(value) : '-'),
            },
            {
                key: 'period_end',
                header: t('End'),
                sortable: false,
                render: (value: string) => (value ? formatDate(value) : '-'),
            },
            {
                key: 'expected_closing',
                header: t('Expected Closing'),
                sortable: false,
                render: (value: string) => (value ? formatCurrency(value) : '-'),
            },
            {
                key: 'counted_cash',
                header: t('Counted Cash'),
                sortable: false,
                render: (value: string) => (value ? formatCurrency(value) : '-'),
            },
            {
                key: 'variance',
                header: t('Variance'),
                sortable: false,
                render: (value: string) => (value ? formatCurrency(value) : '-'),
            },
            {
                key: 'locked',
                header: t('Locked'),
                sortable: false,
                render: (value: boolean) => (value ? t('Yes') : t('No')),
            },
            {
                key: 'actions',
                header: '',
                sortable: false,
                render: (_: unknown, row: any) => (
                    <Button variant="outline" size="sm" onClick={() => router.visit(route('petty-cash-management.reconciliations.show', row.id))}>
                        {t('View')}
                    </Button>
                ),
            },
        ],
        [t]
    );

    return (
        <AuthenticatedLayout>
            <Head title={t('Reconciliations')} />

            <div className="space-y-6">
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-xl font-semibold">{t('Reconciliations')}</h1>
                        <p className="text-sm text-muted-foreground">{t('Close a period by comparing expected vs counted cash.')}</p>
                    </div>
                    {auth.user?.permissions?.includes('create-petty-cash-reconciliations') && (
                        <Button asChild>
                            <Link href={route('petty-cash-management.reconciliations.create')}>{t('Create')}</Link>
                        </Button>
                    )}
                </div>

                <Card>
                    <CardContent className="p-4">
                        <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
                            <div>
                                <Input
                                    type="date"
                                    value={filters.period_start}
                                    onChange={(e) => setFilters((prev) => ({ ...prev, period_start: e.target.value }))}
                                />
                            </div>
                            <div>
                                <Input
                                    type="date"
                                    value={filters.period_end}
                                    onChange={(e) => setFilters((prev) => ({ ...prev, period_end: e.target.value }))}
                                />
                            </div>
                            <div>
                                <Select value={filters.locked || 'all'} onValueChange={(value) => setFilters((prev) => ({ ...prev, locked: value === 'all' ? '' : value }))}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Locked')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All')}</SelectItem>
                                        <SelectItem value="1">{t('Locked')}</SelectItem>
                                        <SelectItem value="0">{t('Unlocked')}</SelectItem>
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
                            <DataTable data={reconciliations?.data || []} columns={tableColumns as any} className="rounded-none" />
                        </div>
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30 flex items-center justify-between gap-4">
                        <PerPageSelector routeName="petty-cash-management.reconciliations.index" filters={filters} />
                        <Pagination
                            data={reconciliations || { data: [], links: [], current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }}
                            routeName="petty-cash-management.reconciliations.index"
                            filters={{ ...filters, per_page: perPage }}
                        />
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}

