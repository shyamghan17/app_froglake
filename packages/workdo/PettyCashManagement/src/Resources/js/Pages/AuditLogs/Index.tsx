import { useMemo, useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { formatDateTime } from '@/utils/helpers';
import { PettyCashAuditLogFilters, PettyCashAuditLogsIndexProps } from './types';

export default function Index() {
    const { t } = useTranslation();
    const { auditLogs, subjectTypes, filters: serverFilters } = usePage<PettyCashAuditLogsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<PettyCashAuditLogFilters>({
        action: serverFilters?.action || '',
        subject_type: serverFilters?.subject_type || '',
        subject_id: serverFilters?.subject_id || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');

    const handleFilter = () => {
        router.get(
            route('petty-cash-management.audit-logs.index'),
            { ...filters, per_page: perPage },
            { preserveState: true, replace: true }
        );
    };

    const clearFilters = () => {
        const cleared: PettyCashAuditLogFilters = {
            action: '',
            subject_type: '',
            subject_id: '',
        };
        setFilters(cleared);
        router.get(route('petty-cash-management.audit-logs.index'), { per_page: perPage });
    };

    const tableColumns = useMemo(
        () => [
            {
                key: 'created_at',
                header: t('When'),
                sortable: false,
                render: (value: string) => (value ? formatDateTime(value) : '-'),
            },
            {
                key: 'actor',
                header: t('Actor'),
                sortable: false,
                render: (_: unknown, row: any) => row.actor?.name || '-',
            },
            {
                key: 'action',
                header: t('Action'),
                sortable: false,
                render: (value: string) => value || '-',
            },
            {
                key: 'subject',
                header: t('Subject'),
                sortable: false,
                render: (_: unknown, row: any) =>
                    row.subject_type ? `${row.subject_type}${row.subject_id ? ` #${row.subject_id}` : ''}` : '-',
            },
            {
                key: 'meta',
                header: t('Meta'),
                sortable: false,
                render: (_: unknown, row: any) => (row.meta ? JSON.stringify(row.meta) : '-'),
            },
        ],
        [t]
    );

    return (
        <AuthenticatedLayout>
            <Head title={t('Audit Logs')} />

            <div className="space-y-6">
                <div>
                    <h1 className="text-xl font-semibold">{t('Audit Logs')}</h1>
                    <p className="text-sm text-muted-foreground">{t('Read-only record of approvals, reversals, and receipt changes.')}</p>
                </div>

                <Card>
                    <CardContent className="p-4">
                        <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
                            <div>
                                <Input
                                    value={filters.action}
                                    onChange={(e) => setFilters((prev) => ({ ...prev, action: e.target.value }))}
                                    placeholder={t('Action contains...')}
                                />
                            </div>
                            <div>
                                <Select
                                    value={filters.subject_type || 'all'}
                                    onValueChange={(value) => setFilters((prev) => ({ ...prev, subject_type: value === 'all' ? '' : value }))}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Subject Type')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">{t('All')}</SelectItem>
                                        {subjectTypes.map((type) => (
                                            <SelectItem key={type} value={type}>
                                                {type}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <Input
                                    value={filters.subject_id}
                                    onChange={(e) => setFilters((prev) => ({ ...prev, subject_id: e.target.value }))}
                                    placeholder={t('Subject ID')}
                                />
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
                            <DataTable data={auditLogs?.data || []} columns={tableColumns as any} className="rounded-none" />
                        </div>
                    </CardContent>

                    <CardContent className="px-4 py-2 border-t bg-gray-50/30 flex items-center justify-between gap-4">
                        <PerPageSelector routeName="petty-cash-management.audit-logs.index" filters={filters} />
                        <Pagination
                            data={auditLogs || { data: [], links: [], current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }}
                            routeName="petty-cash-management.audit-logs.index"
                            filters={{ ...filters, per_page: perPage }}
                        />
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
