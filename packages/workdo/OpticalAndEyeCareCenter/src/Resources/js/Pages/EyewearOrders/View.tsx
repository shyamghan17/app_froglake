import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { ShoppingCart } from 'lucide-react';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { Badge } from '@/components/ui/badge';

interface ViewProps {
    order: any;
}

export default function View({ order }: ViewProps) {
    const { t } = useTranslation();

    const getStatusBadge = (status: string) => {
        const classes = {
            pending: 'bg-yellow-100 text-yellow-800',
            processing: 'bg-blue-100 text-blue-800',
            ready: 'bg-purple-100 text-purple-800',
            delivered: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800'
        };
        return <Badge className={classes[status] || 'bg-gray-100 text-gray-800'}>{t(status.toUpperCase())}</Badge>;
    };

    const getPaymentBadge = (status: string) => {
        const classes = {
            unpaid: 'bg-red-100 text-red-800',
            partial: 'bg-orange-100 text-orange-800',
            paid: 'bg-green-100 text-green-800',
            refunded: 'bg-gray-100 text-gray-800'
        };
        return <Badge className={classes[status] || 'bg-gray-100 text-gray-800'}>{t(status.toUpperCase())}</Badge>;
    };

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <ShoppingCart className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Eyeware Order Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">#{order.order_number}</p>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Order Number')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.order_number}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Order Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatDate(order.order_date)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Patient Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.patient?.patient_name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Contact Number')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.patient?.contact_no || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Delivery Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.delivery_date ? formatDate(order.delivery_date) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Payment Method')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.payment_method || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Order Status')}</label>
                        <div className="bg-gray-50 p-2 rounded">{getStatusBadge(order.order_status)}</div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Payment Status')}</label>
                        <div className="bg-gray-50 p-2 rounded">{getPaymentBadge(order.payment_status)}</div>
                    </div>
                </div>

                {order.prescription_details && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Prescription Details')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.prescription_details}</p>
                    </div>
                )}

                {order.special_notes && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Special Notes')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{order.special_notes}</p>
                    </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Subtotal')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatCurrency(order.subtotal)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Discount')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatCurrency(order.discount_amount)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Tax')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatCurrency(order.tax_amount)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Total Amount')}</label>
                        <p className="text-sm text-gray-900 bg-green-50 p-2 rounded font-semibold">{formatCurrency(order.total_amount)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Paid Amount')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatCurrency(order.paid_amount || 0)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Balance Due')}</label>
                        <p className="text-sm text-gray-900 bg-blue-50 p-2 rounded font-semibold">{formatCurrency(order.balance_amount)}</p>
                    </div>
                </div>
                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Order Items')}</label>
                    <div className="bg-gray-50 p-2 rounded">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b">
                                    <th className="px-2 py-2 text-left">{t('Product')}</th>
                                    <th className="px-2 py-2 text-right">{t('Qty')}</th>
                                    <th className="px-2 py-2 text-right">{t('Price')}</th>
                                    <th className="px-2 py-2 text-right">{t('Total')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {order.items?.map((item, index) => {
                                    const lineTotal = item.quantity * item.unit_price;
                                    const discountAmount = (lineTotal * item.discount_percentage) / 100;
                                    const afterDiscount = lineTotal - discountAmount;
                                    const taxAmount = (afterDiscount * item.tax_percentage) / 100;
                                    const totalAmount = afterDiscount + taxAmount;

                                    return (
                                        <tr key={index} className="border-b">
                                            <td className="px-2 py-2">{item.product?.name || 'N/A'}</td>
                                            <td className="px-2 py-2 text-right">{item.quantity}</td>
                                            <td className="px-2 py-2 text-right">{formatCurrency(item.unit_price)}</td>
                                            <td className="px-2 py-2 text-right">{formatCurrency(totalAmount)}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DialogContent>
    );
}
