import React, { useState, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Trash2, Package } from "lucide-react";
import { formatCurrency } from '@/utils/helpers';
import { InputError } from '@/components/ui/input-error';

interface RepairOrderRequest {
    id: number;
    product_name: string;
    product_quantity: number;
    customer_name: string;
    customer_email: string;
    customer_mobile_no: string;
    location: string;
    date: string;
    expiry_date: string;
    status: number;
}

interface RepairPart {
    id?: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    discount_percentage: number;
    discount_amount: number;
    tax_percentage: number;
    tax_amount: number;
    total_amount: number;
}

interface ProductPart {
    id: number;
    name: string;
    sale_price: number;
    description: string;
}

interface RepairProductPartsIndexProps {
    repairOrderRequest: RepairOrderRequest;
    repairstatuses: Array<{id: number; name: string}>;
    productParts?: Array<ProductPart>;
    existingParts?: Array<RepairPart>;
}

export default function Index() {
    const { t } = useTranslation();
    const { repairOrderRequest, repairstatuses, productParts = [], existingParts = [] } = usePage<RepairProductPartsIndexProps>().props;
    const { errors = {} } = usePage().props;
    
    const [parts, setParts] = useState<RepairPart[]>([{
        product_id: 0,
        quantity: 1,
        unit_price: 0,
        discount_percentage: 0,
        discount_amount: 0,
        tax_percentage: 0,
        tax_amount: 0,
        total_amount: 0
    }]);
    


    useEffect(() => {
        if (existingParts.length > 0) {
            setParts(existingParts);
        }
    }, [existingParts]);

    useFlashMessages();

    const addPart = () => {
        const newItem: RepairPart = {
            product_id: 0,
            quantity: 1,
            unit_price: 0,
            discount_percentage: 0,
            discount_amount: 0,
            tax_percentage: 0,
            tax_amount: 0,
            total_amount: 0
        };
        setParts([...parts, newItem]);
    };

    const handleProductSelect = (index: number, productId: number, product?: any) => {
        const newParts = [...parts];
        const totalTaxRate = product?.taxes?.reduce((sum: number, tax: any) => sum + Number(tax.rate), 0) || 0;

        newParts[index] = {
            ...newParts[index],
            product_id: productId,
            unit_price: Number(product?.sale_price) || 0,
            tax_percentage: Number(totalTaxRate) || 0
        };

        // Recalculate with new price and tax
        const item = newParts[index];
        item.quantity = Number(item.quantity) || 1;
        item.discount_percentage = Number(item.discount_percentage) || 0;

        const calculations = calculateLineItemAmounts(
            item.quantity,
            item.unit_price,
            item.discount_percentage,
            item.tax_percentage
        );

        item.discount_amount = Number(calculations.discountAmount) || 0;
        item.tax_amount = Number(calculations.taxAmount) || 0;
        item.total_amount = Number(calculations.totalAmount) || 0;

        setParts(newParts);
    };

    const removePart = async (index: number) => {
        if (parts.length > 1) {
            const part = parts[index];
            
            // If part has an ID, delete from database
            if (part.id) {
                try {
                    await fetch(route('repair-management-system.repair-product-parts.destroy', part.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                } catch (error) {
                    // Handle error silently
                }
            }
            
            setParts(parts.filter((_, i) => i !== index));
        }
    };

    const updatePart = (index: number, field: keyof RepairPart, value: any) => {
        const newParts = [...parts];
        newParts[index] = { ...newParts[index], [field]: value };

        // Recalculate amounts
        const item = newParts[index];

        // If tax_percentage is 0 but product has taxes, recalculate tax_percentage
        if (item.tax_percentage === 0 && item.product_id > 0) {
            const product = productParts.find(p => p.id === item.product_id);
            if (product?.taxes?.length) {
                item.tax_percentage = product.taxes.reduce((sum, tax) => sum + tax.rate, 0);
            }
        }

        const calculations = calculateLineItemAmounts(
            item.quantity,
            item.unit_price,
            item.discount_percentage,
            item.tax_percentage
        );

        item.discount_amount = calculations.discountAmount;
        item.tax_amount = calculations.taxAmount;
        item.total_amount = calculations.totalAmount;

        setParts(newParts);
    };

    const calculateLineItemAmounts = (
        quantity: number,
        unitPrice: number,
        discountPercentage: number,
        taxPercentage: number
    ) => {
        const subtotal = Math.round((quantity * unitPrice) * 100) / 100;
        const discountAmount = Math.round((subtotal * discountPercentage / 100) * 100) / 100;
        const afterDiscount = Math.round((subtotal - discountAmount) * 100) / 100;
        const taxAmount = Math.round((afterDiscount * taxPercentage / 100) * 100) / 100;
        const totalAmount = Math.round((afterDiscount + taxAmount) * 100) / 100;

        return {
            subtotal,
            discountAmount,
            taxAmount,
            totalAmount
        };
    };

    const calculateTotals = () => {
        const subtotal = Math.round(parts.reduce((sum, part) => sum + (part.quantity * part.unit_price), 0) * 100) / 100;
        const taxAmount = Math.round(parts.reduce((sum, part) => sum + (part.tax_amount || 0), 0) * 100) / 100;
        const discountAmount = Math.round(parts.reduce((sum, part) => sum + (part.discount_amount || 0), 0) * 100) / 100;
        const total = Math.round(parts.reduce((sum, part) => sum + (part.total_amount || 0), 0) * 100) / 100;
        
        return { subtotal, taxAmount, discountAmount, total };
    };

    const totals = calculateTotals();



    const handleSubmit = () => {
        const formData = {
            repair_id: repairOrderRequest.id,
            items: parts.map(part => ({
                id: part.id,
                item: part.product_id,
                quantity: part.quantity,
                price: part.unit_price,
                discount: part.discount_amount,
                tax: '',
                description: ''
            }))
        };
        
        router.post(route('repair-management-system.repair-product-parts.store'), formData);
    };

    const getStatusBadge = (status: number) => {
        const statusNames = {
            0: 'Pending',
            1: 'Start Repairing',
            2: 'End Repairing',
            3: 'Start Testing',
            4: 'End Testing',
            5: 'Irrepairable',
            6: 'Cancel',
            7: 'Invoice Created'
        };
        
        const statusName = repairstatuses?.find(item => item.id === status)?.name || statusNames[status] || 'Unknown';
        
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                status === 1 || status === 2 || status === 4 ? 'bg-green-100 text-green-800' :
                status === 5 ? 'bg-red-100 text-red-800' :
                status === 0 ? 'bg-yellow-100 text-yellow-800' :
                status === 7 ? 'bg-blue-100 text-blue-800' :
                'bg-gray-100 text-gray-800'
            }`}>
                {statusName}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Repair')},
                {label: t('Order Requests'), url: route('repair-management-system.repair-order-requests.index')},
                {label: t('Repair Product Parts')}
            ]}
            pageTitle={t('Repair Product Parts')}
            pageActions={null}
        >
            <Head title={t('Repair Product Parts')} />

            {/* Customer Information Card */}
            <Card className="shadow-sm mb-6">
                {/* Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <h3 className="text-lg font-semibold">{t('Product & Customer Details')}</h3>
                        <div className="text-right">
                            {getStatusBadge(repairOrderRequest.status)}
                        </div>
                    </div>
                </CardContent>

                {/* Content */}
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div className="space-y-4">
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Product Name')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.product_name}</p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Product Quantity')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.product_quantity}</p>
                            </div>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Customer Name')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.customer_name}</p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Customer Email')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.customer_email}</p>
                            </div>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Customer Mobile')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.customer_mobile_no}</p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 mb-1">{t('Location')}</p>
                                <p className="text-base text-gray-900 font-medium">{repairOrderRequest.location}</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Parts Summary Card */}
            <Card className="shadow-sm">
                <CardHeader>
                    <div className="flex items-center justify-between">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Package className="h-5 w-5" />
                            {t('Parts Summary')}
                        </CardTitle>
                        <Button
                            type="button"
                            onClick={addPart}
                            variant="default"
                            size="sm"
                        >
                            + {t('Add Item')}
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div className="space-y-4">
                        <div className="overflow-x-auto">
                            <table className="min-w-full">
                                <thead>
                                    <tr className="border-b border-border">
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Product')} <span className="text-red-500">*</span>
                                        </th>
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Qty')} <span className="text-red-500">*</span>
                                        </th>
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Unit Price')} <span className="text-red-500">*</span>
                                        </th>
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Discount')} %
                                        </th>
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Tax')}
                                        </th>
                                        <th className="px-4 py-3 text-left text-sm font-semibold text-foreground">
                                            {t('Total')}
                                        </th>
                                        <th className="px-4 py-3 text-center text-sm font-semibold text-foreground">
                                            {t('Action')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {parts.map((part, index) => (
                                        <tr key={index}>
                                            <td className="px-4 py-4">
                                                <Select value={part.product_id?.toString() || ''} onValueChange={(value) => {
                                                    const productId = parseInt(value);
                                                    const product = productParts.find(p => p.id === productId);
                                                    handleProductSelect(index, productId, product);
                                                }}>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder={t('Select Product')} />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {productParts.map((product) => (
                                                            <SelectItem key={product.id} value={product.id.toString()}>
                                                                {product.name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                <InputError message={errors[`items.${index}.item`]} />
                                            </td>
                                            <td className="px-4 py-4">
                                                <Input
                                                    type="number"
                                                    value={part.quantity}
                                                    onChange={(e) => updatePart(index, 'quantity', parseInt(e.target.value) || 0)}
                                                    className="w-20 text-sm"
                                                    min="1"
                                                    step="1"
                                                    required
                                                />
                                                <InputError message={errors[`items.${index}.quantity`]} />
                                            </td>
                                            <td className="px-4 py-4">
                                                <Input
                                                    type="number"
                                                    value={part.unit_price}
                                                    onChange={(e) => updatePart(index, 'unit_price', parseFloat(e.target.value) || 0)}
                                                    className="w-24 text-sm"
                                                    min="0"
                                                    step="0.01"
                                                    required
                                                />
                                                <InputError message={errors[`items.${index}.price`]} />
                                            </td>
                                            <td className="px-4 py-4">
                                                <Input
                                                    type="number"
                                                    value={part.discount_percentage}
                                                    onChange={(e) => updatePart(index, 'discount_percentage', parseFloat(e.target.value) || 0)}
                                                    className="w-20 text-sm"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                />
                                            </td>
                                            <td className="px-4 py-4">
                                                {(() => {
                                                    const product = productParts.find(p => p.id === part.product_id);
                                                    return product?.taxes && product.taxes.length > 0 ? (
                                                        <div className="flex flex-wrap gap-1">
                                                            {product.taxes.map((tax) => (
                                                                <span key={tax.id} className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {tax.tax_name} ({tax.rate}%)
                                                                </span>
                                                            ))}
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-muted-foreground">No tax</span>
                                                    );
                                                })()}
                                            </td>
                                            <td className="px-4 py-4">
                                                <span className="text-sm font-medium">
                                                    {formatCurrency(part.total_amount)}
                                                </span>
                                            </td>
                                            <td className="px-4 py-4 text-center">
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => removePart(index)}
                                                    className="text-red-600 hover:text-red-800 h-8 w-8 p-0"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Invoice Summary - Bottom of Items */}
                        <div className="mt-6 flex justify-end">
                            <div className="w-80 bg-muted/30 rounded-lg p-4">
                                <h3 className="font-semibold mb-3">{t('Invoice Summary')}</h3>
                                <div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Subtotal')}</span>
                                        <span className="font-medium">{formatCurrency(totals.subtotal)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Tax')}</span>
                                        <span className="font-medium">{formatCurrency(totals.taxAmount)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Discount')}</span>
                                        <span className="font-medium text-red-600">-{formatCurrency(totals.discountAmount)}</span>
                                    </div>
                                    <div className="border-t my-2" />
                                    <div className="flex justify-between">
                                        <span className="font-semibold">{t('Total')}</span>
                                        <span className="font-bold text-lg">{formatCurrency(totals.total)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Action Buttons */}
            <div className="flex justify-end gap-3 mt-6">
                <Button 
                    variant="outline" 
                    onClick={() => window.history.back()}
                >
                    {t('Cancel')}
                </Button>
                <Button onClick={handleSubmit}>
                    {t('Save Changes')}
                </Button>
            </div>


        </AuthenticatedLayout>
    );
}