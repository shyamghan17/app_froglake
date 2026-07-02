import { Head, Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { formatCurrency, formatDateTime } from '@/utils/helpers';
import { ReconciliationShowProps } from './types';

export default function Show() {
    const { t } = useTranslation();
    const { reconciliation } = usePage<ReconciliationShowProps>().props;

    return (
        <AuthenticatedLayout>
            <Head title={t('Reconciliation')} />

            <div className="space-y-6 max-w-4xl">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-xl font-semibold">{t('Reconciliation')}</h1>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <span>
                                {reconciliation.period_start} → {reconciliation.period_end}
                            </span>
                            <Badge variant={reconciliation.locked ? 'default' : 'secondary'}>{reconciliation.locked ? t('Locked') : t('Unlocked')}</Badge>
                        </div>
                    </div>
                    <Button variant="outline" asChild>
                        <Link href={route('petty-cash-management.reconciliations.index')}>{t('Back')}</Link>
                    </Button>
                </div>

                <Card>
                    <CardContent className="p-6 space-y-6">
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Opening Balance')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.opening_balance)}</div>
                            </div>
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Additions')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.additions_total)}</div>
                            </div>
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Expenses')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.expenses_total)}</div>
                            </div>
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Expected Closing')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.expected_closing)}</div>
                            </div>
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Counted Cash')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.counted_cash)}</div>
                            </div>
                            <div className="rounded-lg border p-4">
                                <div className="text-sm text-muted-foreground">{t('Variance')}</div>
                                <div className="text-lg font-semibold">{formatCurrency(reconciliation.variance)}</div>
                            </div>
                        </div>

                        <div className="text-sm text-muted-foreground">
                            {t('Created at')}: {reconciliation.created_at ? formatDateTime(reconciliation.created_at) : '-'}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}

