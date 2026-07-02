import { Head, Link, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Switch } from '@/components/ui/switch';
import { ReconciliationCreateFormData } from './types';

export default function Create() {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm<ReconciliationCreateFormData>({
        period_start: '',
        period_end: '',
        counted_cash: '',
        locked: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('petty-cash-management.reconciliations.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title={t('Create Reconciliation')} />

            <div className="space-y-6 max-w-3xl">
                <div>
                    <h1 className="text-xl font-semibold">{t('Create Reconciliation')}</h1>
                    <p className="text-sm text-muted-foreground">{t('Expected balances are computed automatically for the selected period.')}</p>
                </div>

                <Card>
                    <CardContent className="p-6">
                        <form onSubmit={submit} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label htmlFor="period_start" required>
                                        {t('Period Start')}
                                    </Label>
                                    <Input
                                        id="period_start"
                                        type="date"
                                        value={data.period_start}
                                        onChange={(e) => setData('period_start', e.target.value)}
                                    />
                                    <InputError message={errors.period_start} />
                                </div>
                                <div>
                                    <Label htmlFor="period_end" required>
                                        {t('Period End')}
                                    </Label>
                                    <Input
                                        id="period_end"
                                        type="date"
                                        value={data.period_end}
                                        onChange={(e) => setData('period_end', e.target.value)}
                                    />
                                    <InputError message={errors.period_end} />
                                </div>
                            </div>

                            <div>
                                <CurrencyInput
                                    label={t('Counted Cash')}
                                    value={data.counted_cash}
                                    onChange={(value) => setData('counted_cash', value)}
                                    error={errors.counted_cash}
                                    required
                                />
                            </div>

                            <div className="flex items-center justify-between rounded-lg border p-4">
                                <div>
                                    <div className="font-medium">{t('Lock reconciliation')}</div>
                                    <div className="text-sm text-muted-foreground">{t('Locked reconciliations are read-only snapshots.')}</div>
                                </div>
                                <Switch checked={data.locked} onCheckedChange={(checked) => setData('locked', checked)} />
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <Button type="button" variant="outline" asChild>
                                    <Link href={route('petty-cash-management.reconciliations.index')}>{t('Cancel')}</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? t('Creating...') : t('Create')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}

