import React, { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { EditEyewearOrderProps, UpdateEyewearOrderFormData } from './types';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { useTaxCalculator, calculateLineItemAmounts } from '@/pages/Sales/components/TaxCalculator';
import InvoiceItemsTable from '@/pages/Sales/components/InvoiceItemsTable';
import { formatCurrency } from '@/utils/helpers';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { InputError } from '@/components/ui/input-error';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import { Separator } from '@/components/ui/separator';
import { CurrencyInput } from '@/components/ui/currency-input';
import { ShoppingCart, Package } from 'lucide-react';
import { useFormFields } from '@/hooks/useFormFields';

export default function Edit({ order, patients, warehouses }: EditEyewearOrderProps) {
    const { t } = useTranslation();
    const [availableProducts, setAvailableProducts] = useState([]);

    useFlashMessages();
    const { data, setData, put, processing, errors } = useForm<UpdateEyewearOrderFormData>({
        ...order,
        delivery_date: order.delivery_date || '',
        payment_method: order.payment_method || '',
        extra_charge: order.extra_charge || '',
        prescription_details: order.prescription_details || '',
        special_notes: order.special_notes || '',
        items: (order.items || []).map(item => {
            const calculations = calculateLineItemAmounts(
                item.quantity,
                item.unit_price,
                item.discount_percentage,
                item.tax_percentage
            );
            return {
                ...item,
                taxes: item.taxes || [],
                discount_amount: calculations.discountAmount,
                tax_amount: calculations.taxAmount,
                total_amount: calculations.totalAmount
            };
        })
    });

    const bankAccountField = useFormFields('bankAccountField', data, setData, errors);

    useEffect(() => {
        if (data.warehouse_id) {
            handleWarehouseChange(data.warehouse_id);
        }
    }, []);

    const handleWarehouseChange = async (warehouseId: number | undefined) => {
        setData('warehouse_id', warehouseId);
        if (warehouseId) {
            try {
                const response = await fetch(route('optical-and-eye-care-center.eyewear-orders.warehouse-products') + `?warehouse_id=${warehouseId}`);
                const products = await response.json();
                setAvailableProducts(products);
            } catch (error) {
                setAvailableProducts([]);
            }
        } else {
            setAvailableProducts([]);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('optical-and-eye-care-center.eyewear-orders.update', order.id), {
            onSuccess: () => {
                // Handle success if needed
            }
        });
    };

    const totals = useTaxCalculator(data.items);

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Eyewear Orders'), url: route('optical-and-eye-care-center.eyewear-orders.index') },
                { label: t('Edit Order') }
            ]}
            pageTitle={t('Edit Eyewear Order')}
        >
            <Head title={t('Edit Eyewear Order')} />

            <form onSubmit={handleSubmit} className="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <ShoppingCart className="h-5 w-5" />
                            {t('Order Details')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <Label htmlFor="order_date" required>{t('Order Date')}</Label>
                                <DatePicker
                                    id="order_date"
                                    value={data.order_date}
                                    onChange={(value) => setData('order_date', value)}
                                    required
                                />
                                <InputError message={errors.order_date} />
                            </div>

                            <div>
                                <Label htmlFor="patient_id" required>{t('Patient')}</Label>
                                <Select value={data.patient_id?.toString()} onValueChange={(value) => setData('patient_id', parseInt(value))}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select Patient')} />
                                    </SelectTrigger>
                                    <SelectContent searchable>
                                        {patients?.map((patient) => (
                                            <SelectItem key={patient.id} value={patient.id.toString()}>
                                                {patient.patient_name} - {patient.contact_no}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.patient_id} />
                            </div>

                            <div>
                                <Label htmlFor="warehouse_id">{t('Warehouse')}</Label>
                                <Select value={data.warehouse_id?.toString()} onValueChange={(value) => handleWarehouseChange(parseInt(value))}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select Warehouse')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {warehouses?.map((warehouse) => (
                                            <SelectItem key={warehouse.id} value={warehouse.id.toString()}>
                                                {warehouse.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.warehouse_id} />
                            </div>

                            <div>
                                <Label htmlFor="delivery_date">{t('Delivery Date')}</Label>
                                <DatePicker
                                    id="delivery_date"
                                    value={data.delivery_date}
                                    onChange={(value) => setData('delivery_date', value)}
                                />
                                <InputError message={errors.delivery_date} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <Label htmlFor="payment_method">{t('Payment Method')}</Label>
                                <Input
                                    id="payment_method"
                                    value={data.payment_method}
                                    onChange={(e) => setData('payment_method', e.target.value)}
                                    placeholder={t('e.g., Cash, Card')}
                                />
                            </div>
                            <div>
                                {bankAccountField.map((field) => (
                                    <div key={field.id}>{field.component}</div>
                                ))}
                            </div>
                            <div>
                                <CurrencyInput
                                    id="extra_charge"
                                    label={t('Extra Charge')}
                                    value={data.extra_charge}
                                    onChange={(value) => setData('extra_charge', value)}
                                    placeholder="0.00"
                                    error={errors.extra_charge}
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <Label htmlFor="special_notes">{t('Special Notes')}</Label>
                                <Textarea
                                    id="special_notes"
                                    value={data.special_notes}
                                    onChange={(e) => setData('special_notes', e.target.value)}
                                    rows={2}
                                />
                            </div>
                            <div>
                                <Label htmlFor="prescription_details">{t('Prescription Details')}</Label>
                                <Textarea
                                    id="prescription_details"
                                    value={data.prescription_details}
                                    onChange={(e) => setData('prescription_details', e.target.value)}
                                    rows={2}
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <Package className="h-5 w-5" />
                                {t('Order Items')}
                            </CardTitle>
                            <Button
                                type="button"
                                onClick={() => setData('items', [...data.items, { product_id: 0, item_type: 'standard', quantity: 1, unit_price: 0, discount_percentage: 0, tax_percentage: 0, discount_amount: 0, tax_amount: 0, total_amount: 0, taxes: [] }])}
                                variant="default"
                                size="sm"
                            >
                                + {t('Add Item')}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <InvoiceItemsTable
                            items={data.items}
                            onChange={(items) => setData('items', items)}
                            errors={errors}
                            products={availableProducts}
                            showAddButton={false}
                            invoiceType="product"
                        />

                        <div className="mt-6 flex justify-end">
                            <div className="w-80 bg-muted/30 rounded-lg p-4">
                                <h3 className="font-semibold mb-3">{t('Order Summary')}</h3>
                                <div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Subtotal')}</span>
                                        <span className="font-medium">{formatCurrency(totals.subtotal)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Discount')}</span>
                                        <span className="font-medium text-red-600">-{formatCurrency(totals.discountAmount)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Tax')}</span>
                                        <span className="font-medium">{formatCurrency(totals.taxAmount)}</span>
                                    </div>
                                    {data.extra_charge && parseFloat(data.extra_charge) > 0 && (
                                        <div className="flex justify-between text-sm">
                                            <span className="text-muted-foreground">{t('Extra Charge')}</span>
                                            <span className="font-medium">{formatCurrency(parseFloat(data.extra_charge))}</span>
                                        </div>
                                    )}
                                    <Separator className="my-2" />
                                    <div className="flex justify-between">
                                        <span className="font-semibold">{t('Total')}</span>
                                        <span className="font-bold text-lg">{formatCurrency(totals.total + (data.extra_charge ? parseFloat(data.extra_charge) : 0))}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-3">
                    <Button type="button" variant="outline" onClick={() => window.history.back()}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing || data.items.length === 0}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
