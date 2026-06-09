import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { Download, Edit, CreditCard, History, ArrowLeft } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { toast } from 'sonner';

interface RepairPart {
    id: number;
    product_id: number;
    quantity: number;
    price: number;
    discount: number;
    tax: string;
    description: string;
    product_name?: string;
    tax_rate?: number;
    tax_amount?: number;
    item_total_with_tax?: number;
}

interface RepairOrder {
    id: number;
    product_name: string;
    customer_name: string;
    customer_email: string;
    customer_mobile_no: string;
    location: string;
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
    total_paid?: number;
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

export default function Show({ repairinvoice, repair_order, repair_parts, subtotal, total_discount, total_tax, total_amount }: Props) {
    const { t } = useTranslation();
    const { auth } = usePage<any>().props;
    const [paymentAmount, setPaymentAmount] = useState((Math.round((total_amount - (repairinvoice.total_paid || 0)) * 100) / 100).toString());
    const [paymentNote, setPaymentNote] = useState('');
    const [isPaymentDialogOpen, setIsPaymentDialogOpen] = useState(false);
    const [isProcessing, setIsProcessing] = useState(false);
    const [paymentHistory, setPaymentHistory] = useState([]);
    
    // Fetch payment history on component mount
    React.useEffect(() => {
        if (auth.user?.permissions?.includes('view-payment-history-repair-invoices')) {
            fetch(route('repair-management-system.repair-invoices.payment-history', repairinvoice.id))
                .then(res => res.json())
                .then(data => {
                    setPaymentHistory(data.payments || []);
                })
                .catch(() => {
                    setPaymentHistory([]);
                });
        }
    }, [repairinvoice.id]);
    
    const getStatusBadge = (status: string) => {
        const statusMap = {
            '0': { label: t('Pending'), className: 'px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800' },
            '1': { label: t('Partially Paid'), className: 'px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800' },
            '2': { label: t('Paid'), className: 'px-2 py-1 rounded-full text-sm bg-green-100 text-green-800' }
        };
        const statusInfo = statusMap[status as keyof typeof statusMap] || { label: t('Unknown'), className: 'px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-800' };
        return <span className={statusInfo.className}>{statusInfo.label}</span>;
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Repair Invoice'), url: route('repair-management-system.repair-invoices.index')},
                {label: t('Repair Invoice Details')}
            ]}
            pageTitle={`${t('Repair Invoice')} ${repairinvoice.invoice_id}`}
        >
            <Head title={`${t('Repair Invoice')} ${repairinvoice.invoice_id}`} />

            <div className="space-y-6">

                


                <Card>
                    <CardContent className="p-6">
                        <div className="flex justify-between items-center mb-6">
                            <div>
                                <p className="text-lg text-muted-foreground">{repairinvoice.invoice_id}</p>
                            </div>
                            <div className="flex items-center gap-4">
                                {repairinvoice.status !== '2' && auth.user?.permissions?.includes('manage-repair-product-parts') && (
                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onClick={() => router.visit(route('repair-management-system.repair-product-parts.index', repairinvoice.repair_id))}
                                                    className="h-9 w-9 hover:bg-blue-50 transition-colors"
                                                >
                                                    <Edit className="h-4 w-4 text-blue-600" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>{t('Edit')}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                )}
                                {getStatusBadge(repairinvoice.status)}
                                <div className="text-right">
                                    <div className="text-2xl font-bold">{formatCurrency(total_amount)}</div>
                                    <div className="text-sm text-muted-foreground">{t('Total Amount')}</div>
                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 className="font-semibold mb-2">{t('CUSTOMER DETAILS')}</h3>
                                <div className="text-sm space-y-1">
                                    <div className="font-medium">{repair_order.customer_name || '-'}</div>
                                    <div className="text-muted-foreground">{repair_order.customer_email || '-'}</div>
                                    {repair_order.customer_mobile_no && (
                                        <div className="text-muted-foreground">{repair_order.customer_mobile_no}</div>
                                    )}
                                </div>
                            </div>

                            <div>
                                <h3 className="font-semibold mb-2">{t('PRODUCT')}</h3>
                                <div className="text-sm space-y-1">
                                    <div className="font-medium">{repair_order.product_name || '-'}</div>
                                    {repair_order.location && (
                                        <div className="text-muted-foreground">{t('Location')}: {repair_order.location}</div>
                                    )}
                                </div>
                            </div>

                            <div>
                                <h3 className="font-semibold mb-2">{t('DETAILS')}</h3>
                                <div className="space-y-1 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">{t('Invoice Date')}</span>
                                        <span>{formatDate(repairinvoice.created_at)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">{t('Repair Date')}</span>
                                        <span>{formatDate(repair_order.date)}</span>
                                    </div>
                                </div>
                                <div className="mt-4 p-3 bg-blue-50 rounded">
                                    <div className="flex justify-between items-center">
                                        <div className="flex gap-2">
                                            <TooltipProvider>
                                                <Tooltip>
                                                    <TooltipTrigger asChild>
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() => {
                                                                const printUrl = route('repair-management-system.repair-invoices.print', repairinvoice.id) + '?download=pdf';
                                                                window.open(printUrl, '_blank');
                                                            }}
                                                            className="hover:bg-blue-50 hover:border-blue-200 transition-colors"
                                                        >
                                                            <Download className="h-4 w-4 mr-2 text-blue-600" />
                                                            {t('Download PDF')}
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent>
                                                        <p>{t('Download Invoice PDF')}</p>
                                                    </TooltipContent>
                                                </Tooltip>
                                            </TooltipProvider>
                                            {repairinvoice.status !== '2' && auth.user?.permissions?.includes('make-payment-repair-invoices') && (
                                                <Dialog open={isPaymentDialogOpen} onOpenChange={setIsPaymentDialogOpen}>
                                                    <Tooltip>
                                                        <TooltipTrigger asChild>
                                                            <DialogTrigger asChild>
                                                                <Button variant="outline" size="sm" className="hover:bg-green-50 hover:border-green-200 transition-colors">
                                                                    <CreditCard className="h-4 w-4 mr-2 text-green-600" />
                                                                    {t('Make Payment')}
                                                                </Button>
                                                            </DialogTrigger>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <p>{t('Make payment')}</p>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                    <DialogContent>
                                                        <DialogHeader>
                                                            <DialogTitle>{t('Add Payment')}</DialogTitle>
                                                        </DialogHeader>
                                                        <div className="space-y-4">
                                                            <div>
                                                                <Label required>{t('Amount')} <span className="text-sm text-muted-foreground"></span></Label>
                                                                <Input
                                                                    id="amount"
                                                                    type="number"
                                                                    step="0.01"
                                                                    min="0.01"
                                                                    max={Math.round((total_amount - (repairinvoice.total_paid || 0)) * 100) / 100}
                                                                    value={paymentAmount}
                                                                    onChange={(e) => setPaymentAmount(e.target.value)}
                                                                    placeholder={t('Enter amount')}
                                                                />
                                                            </div>
                                                            <div>
                                                                <Label htmlFor="note">{t('Note')} <span className="text-muted-foreground"></span></Label>
                                                                <Textarea
                                                                    id="note"
                                                                    value={paymentNote}
                                                                    onChange={(e) => setPaymentNote(e.target.value)}
                                                                    placeholder={t('Enter payment note...')}
                                                                    rows={3}
                                                                />
                                                            </div>
                                                            <div className="flex justify-end gap-2">
                                                                <Button variant="outline" onClick={() => {
                                                                    setIsPaymentDialogOpen(false);
                                                                    setPaymentNote('');
                                                                }}>
                                                                    {t('Cancel')}
                                                                </Button>
                                                                <Button
                                                                    onClick={() => {
                                                                        const amount = parseFloat(paymentAmount);
                                                                        const dueAmount = total_amount - (repairinvoice.total_paid || 0);
                                                                        
                                                                        if (!paymentAmount || amount <= 0) {
                                                                            toast.error(t('Please enter a valid amount'));
                                                                            return;
                                                                        }
                                                                        
                                                                        if (amount > dueAmount) {
                                                                            toast.error(t('Payment amount cannot exceed the due amount'));
                                                                            return;
                                                                        }
                                                                        
                                                                        setIsProcessing(true);
                                                                        router.get(route('repair-management-system.repair-invoices.payment', repairinvoice.id), {
                                                                            amount: Math.round(amount * 100) / 100,
                                                                            note: paymentNote
                                                                        }, {
                                                                            onSuccess: () => {
                                                                                setIsPaymentDialogOpen(false);
                                                                                setPaymentAmount('');
                                                                                setPaymentNote('');
                                                                                setIsProcessing(false);
                                                                                toast.success(t('The Payment has been recorded successfully.'));
                                                                            },
                                                                            onError: (errors) => {
                                                                                setIsProcessing(false);
                                                                                const errorMessage = errors?.message || t('Failed to record payment');
                                                                                toast.error(errorMessage);
                                                                            }
                                                                        });
                                                                    }}
                                                                    disabled={parseFloat(paymentAmount) <= 0 || isProcessing}
                                                                >
                                                                    {isProcessing ? t('Processing...') : t('Record Payment')}
                                                                </Button>
                                                            </div>
                                                        </div>
                                                    </DialogContent>
                                                </Dialog>
                                            )}
                                        </div>
                                        <div className="text-right">
                                            <div className="text-xl font-bold text-blue-600">{formatCurrency(total_amount - (repairinvoice.total_paid || 0))}</div>
                                            <div className="text-sm text-muted-foreground">{t('Total Due')}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <h3 className="text-lg font-semibold">{t('Invoice Items')}</h3>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="min-w-full">
                                <thead>
                                    <tr className="border-b">
                                        <th className="px-4 py-3 text-left text-sm font-semibold">{t('Part')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Qty')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Unit Price')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Discount')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Tax')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Total')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {repair_parts.map((part) => (
                                        <tr key={part.id}>
                                            <td className="px-4 py-4">
                                                <div className="font-medium">{part.product_name || part.description || t('Repair Part')}</div>
                                                {part.description && (
                                                    <div className="text-sm text-muted-foreground">{part.description}</div>
                                                )}
                                            </td>
                                            <td className="px-4 py-4 text-right">{part.quantity}</td>
                                            <td className="px-4 py-4 text-right">{formatCurrency(part.price)}</td>
                                            <td className="px-4 py-4 text-right">
                                                {part.discount > 0 ? (
                                                    <div className="text-sm text-muted-foreground">
                                                        -{formatCurrency(part.discount)}
                                                    </div>
                                                ) : '-'}
                                            </td>
                                            <td className="px-4 py-4 text-right">
                                                {(part as any).tax_amount > 0 ? (
                                                    <div className="text-sm text-muted-foreground">
                                                        {formatCurrency((part as any).tax_amount)}
                                                    </div>
                                                ) : '-'}
                                            </td>
                                            <td className="px-4 py-4 text-right font-semibold">
                                                {formatCurrency((part as any).item_total_with_tax || ((part.price * part.quantity) - part.discount))}
                                            </td>
                                        </tr>
                                    ))}
                                    <tr>
                                        <td className="px-4 py-4">
                                            <div className="font-medium">{t('Repair Service')}</div>
                                        </td>
                                        <td className="px-4 py-4 text-right">1</td>
                                        <td className="px-4 py-4 text-right">{formatCurrency(repairinvoice.repair_charge)}</td>
                                        <td className="px-4 py-4 text-right">-</td>
                                        <td className="px-4 py-4 text-right">-</td>
                                        <td className="px-4 py-4 text-right font-semibold">
                                            {formatCurrency(repairinvoice.repair_charge)}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div className="mt-6 flex justify-end">
                            <div className="w-80 space-y-3">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Sub Total')}</span>
                                    <span className="font-medium">{formatCurrency(subtotal)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Discount')}</span>
                                    <span className="font-medium text-red-600">{formatCurrency(total_discount)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Tax')}</span>
                                    <span className="font-medium">{formatCurrency(total_tax)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Repair Charge')}</span>
                                    <span className="font-medium">{formatCurrency(repairinvoice.repair_charge)}</span>
                                </div>
                                <div className="border-t pt-3">
                                    <div className="flex justify-between">
                                        <span className="font-semibold">{t('Total')}</span>
                                        <span className="font-bold text-lg">{formatCurrency(total_amount)}</span>
                                    </div>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Paid')}</span>
                                    <span className="font-medium text-green-600">{formatCurrency(repairinvoice.total_paid || 0)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="font-semibold">{t('Due')}</span>
                                    <span className="font-bold text-lg">{formatCurrency(total_amount - (repairinvoice.total_paid || 0))}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Payment History */}
                {auth.user?.permissions?.includes('view-payment-history-repair-invoices') && paymentHistory.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>{t('Payment History')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-x-auto">
                                <table className="min-w-full">
                                    <thead>
                                        <tr className="border-b">
                                            <th className="px-4 py-3 text-left text-sm font-semibold">{t('Date')}</th>
                                            <th className="px-4 py-3 text-left text-sm font-semibold">{t('Amount')}</th>
                                            <th className="px-4 py-3 text-left text-sm font-semibold">{t('Payment Type')}</th>
                                            <th className="px-4 py-3 text-left text-sm font-semibold">{t('Note')}</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {paymentHistory.map((payment: any, index: number) => (
                                            <tr key={payment.id || index}>
                                                <td className="px-4 py-4">{formatDate(payment.created_at)}</td>
                                                <td className="px-4 py-4 font-medium">{formatCurrency(payment.amount)}</td>
                                                <td className="px-4 py-4">
                                                    <span className="px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                                        {payment.payment_method || t('Manual')}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 text-muted-foreground">{payment.notes || '-'}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AuthenticatedLayout>
    );
}