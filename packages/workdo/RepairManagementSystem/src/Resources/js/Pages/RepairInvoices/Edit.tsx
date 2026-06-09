import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatCurrency, formatDate } from '@/utils/helpers';

interface RepairPart {
    id: number;
    product_id: number;
    quantity: number;
    price: number;
    discount: number;
    tax: string;
    description: string;
    product_name?: string;
}

interface RepairOrder {
    id: number;
    product_name: string;
    customer_name: string;
    customer_email: string;
    customer_mobile_no: string;
    date: string;
    expiry_date: string;
    status: number;
}

interface RepairInvoice {
    id: number;
    invoice_id: string;
    repair_id: number;
    repair_charge: number;
    total_amount: number;
    status: string;
    created_at: string;
}

interface Props {
    repairinvoice: RepairInvoice;
    repair_order: RepairOrder;
    repair_parts: RepairPart[];
    subtotal: number;
    total_discount: number;
    total_tax: number;
    total_amount: number;
}

export default function Edit({ repairinvoice, repair_order, repair_parts, subtotal, total_discount, total_tax, total_amount }: Props) {
    const { t } = useTranslation();
    const [repairCharge, setRepairCharge] = useState(repairinvoice.repair_charge.toString());
    const [isProcessing, setIsProcessing] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsProcessing(true);
        
        router.put(route('repair-management-system.repair-invoices.update', repairinvoice.id), {
            repair_charge: parseFloat(repairCharge)
        }, {
            onSuccess: () => setIsProcessing(false),
            onError: () => setIsProcessing(false)
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Repair Invoice'), url: route('repair-management-system.repair-invoices.index')},
                {label: repairinvoice.invoice_id, url: route('repair-management-system.repair-invoices.show', repairinvoice.id)},
                {label: t('Edit')}
            ]}
            pageTitle={`${t('Edit Invoice')} ${repairinvoice.invoice_id}`}
        >
            <Head title={`${t('Edit Invoice')} ${repairinvoice.invoice_id}`} />

            <div className="space-y-6">
                <Card>
                    <CardHeader>
                        <h3 className="text-lg font-semibold">{t('Edit Invoice')}</h3>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 className="font-semibold mb-3">{t('Customer Information')}</h4>
                                    <div className="space-y-2 text-sm">
                                        <p><strong>{t('Name')}:</strong> {repair_order.customer_name}</p>
                                        <p><strong>{t('Email')}:</strong> {repair_order.customer_email}</p>
                                        <p><strong>{t('Mobile')}:</strong> {repair_order.customer_mobile_no}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 className="font-semibold mb-3">{t('Product Information')}</h4>
                                    <div className="space-y-2 text-sm">
                                        <p><strong>{t('Product')}:</strong> {repair_order.product_name}</p>
                                        <p><strong>{t('Repair Date')}:</strong> {formatDate(repair_order.date)}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div>
                                    <Label htmlFor="repair_charge">{t('Repair Charge')}</Label>
                                    <Input
                                        id="repair_charge"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={repairCharge}
                                        onChange={(e) => setRepairCharge(e.target.value)}
                                        required
                                    />
                                </div>

                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h4 className="font-semibold mb-3">{t('Invoice Summary')}</h4>
                                    <div className="space-y-2 text-sm">
                                        <div className="flex justify-between">
                                            <span>{t('Parts Subtotal')}:</span>
                                            <span>{formatCurrency(subtotal)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>{t('Discount')}:</span>
                                            <span>{formatCurrency(total_discount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>{t('Tax')}:</span>
                                            <span>{formatCurrency(total_tax)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>{t('Repair Charge')}:</span>
                                            <span>{formatCurrency(parseFloat(repairCharge) || 0)}</span>
                                        </div>
                                        <div className="flex justify-between font-semibold border-t pt-2">
                                            <span>{t('Total')}:</span>
                                            <span>{formatCurrency((subtotal - total_discount + total_tax) + (parseFloat(repairCharge) || 0))}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="flex justify-end gap-3">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => router.visit(route('repair-management-system.repair-invoices.show', repairinvoice.id))}
                                >
                                    {t('Cancel')}
                                </Button>
                                <Button type="submit" disabled={isProcessing}>
                                    {isProcessing ? t('Updating...') : t('Update Invoice')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}