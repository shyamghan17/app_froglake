import React from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FileText, Download } from 'lucide-react';

interface ShowProps {
    eyewearorder: any;
    auth: any;
}

export default function Show() {
    const { t } = useTranslation();
    const { eyewearorder, auth } = usePage<ShowProps>().props;

    useFlashMessages();

    const getPaymentStatusBadge = (status: string) => {
        const badges: Record<string, string> = {
            draft: 'bg-yellow-100 text-yellow-800',
            paid: 'bg-green-100 text-green-800',
        };
        return badges[status] || 'bg-gray-100 text-gray-800';
    };

    const downloadPDF = () => {
        const printUrl = route('optical-and-eye-care-center.eyewear-orders.print', eyewearorder.id) + '?download=pdf';
        window.open(printUrl, '_blank');
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Optical & Eye Care Center') },
                { label: t('Eyewear Orders'), url: route('optical-and-eye-care-center.eyewear-orders.index') },
                { label: t('Order Details') }
            ]}
            pageTitle={`${t('Order')} #${eyewearorder.order_number}`}
        >
            <Head title={`${t('Order')} #${eyewearorder.order_number}`} />

            <div className="space-y-6">
                <Card>
                    <CardContent className="p-6">
                        <div className="flex justify-between items-center mb-6">
                            <div>
                                <p className="text-lg text-muted-foreground">#{eyewearorder.order_number}</p>
                            </div>
                            <div className="flex items-center gap-4">
                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${getPaymentStatusBadge(eyewearorder.payment_status)}`}>
                                    {t(eyewearorder.payment_status?.toUpperCase() || 'DRAFT')}
                                </span>
                                <div className="text-right">
                                    <div className="text-2xl font-bold">{formatCurrency(eyewearorder.total_amount)}</div>
                                    <div className="text-sm text-muted-foreground">{t('Total Amount')}</div>
                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 className="font-semibold mb-2">{t('PATIENT')}</h3>
                                <div className="text-sm space-y-1">
                                    <div className="font-medium">{eyewearorder.patient?.patient_name}</div>
                                    <div className="text-muted-foreground">{eyewearorder.patient?.contact_no}</div>
                                    {eyewearorder.patient?.address && (
                                        <div className="text-muted-foreground mt-2">{eyewearorder.patient.address}</div>
                                    )}
                                </div>
                            </div>

                            <div>
                                <h3 className="font-semibold mb-2">{t('PRESCRIPTION')}</h3>
                                <div className="text-sm text-muted-foreground">
                                    {eyewearorder.prescription_details || t('No prescription details')}
                                </div>
                                {eyewearorder.special_notes && (
                                    <div className="mt-3">
                                        <div className="font-medium text-sm mb-1">{t('Special Notes')}</div>
                                        <div className="text-sm text-muted-foreground">{eyewearorder.special_notes}</div>
                                    </div>
                                )}
                            </div>

                            <div>
                                <h3 className="font-semibold mb-2">{t('ORDER DETAILS')}</h3>
                                <div className="space-y-1 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">{t('Order Date')}</span>
                                        <span>{formatDate(eyewearorder.order_date)}</span>
                                    </div>
                                    {eyewearorder.delivery_date && (
                                        <div className="flex justify-between">
                                            <span className="text-muted-foreground">{t('Delivery Date')}</span>
                                            <span>{formatDate(eyewearorder.delivery_date)}</span>
                                        </div>
                                    )}
                                    {eyewearorder.payment_method && (
                                        <div className="flex justify-between">
                                            <span className="text-muted-foreground">{t('Payment Method')}</span>
                                            <span className="capitalize">{eyewearorder.payment_method}</span>
                                        </div>
                                    )}
                                </div>
                                <div className="mt-4 p-3 bg-blue-50 rounded">
                                    <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                        <div className="flex flex-wrap gap-2">
                                            {auth.user?.permissions?.includes('print-eyewear-orders') && (
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={downloadPDF}
                                                >
                                                    <Download className="h-4 w-4 mr-2" />
                                                    {t('Download PDF')}
                                                </Button>
                                            )}
                                            {eyewearorder.payment_status === 'draft' && auth.user?.permissions?.includes('post-eyewear-orders') && (
                                                <Button
                                                    size="sm"
                                                    onClick={() => router.post(route('optical-and-eye-care-center.eyewear-orders.post', eyewearorder.id), {}, {
                                                        onSuccess: () => {
                                                            router.reload();
                                                        }
                                                    })}
                                                >
                                                    <FileText className="h-4 w-4 mr-2" />
                                                    {t('Post Order')}
                                                </Button>
                                            )}
                                        </div>
                                        <div className="text-right sm:text-right">
                                            <div className="text-lg sm:text-xl font-bold text-blue-600">{formatCurrency(eyewearorder.balance_amount)}</div>
                                            <div className="text-xs sm:text-sm text-muted-foreground">{t('Balance Due')}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <h3 className="text-lg font-semibold">{t('Order Items')}</h3>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="min-w-full">
                                <thead>
                                    <tr className="border-b">
                                        <th className="px-4 py-3 text-left text-sm font-semibold">{t('Product')}</th>
                                        <th className="px-4 py-3 text-center text-sm font-semibold">{t('Type')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Qty')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Unit Price')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Discount')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Tax')}</th>
                                        <th className="px-4 py-3 text-right text-sm font-semibold">{t('Total')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {eyewearorder.items?.map((item: any, index: number) => (
                                        <tr key={index}>
                                            <td className="px-4 py-4">
                                                <div className="font-medium">{item.product_name || item.product?.name || '-'}</div>
                                                {(item.brand_name || item.eyewear_item?.brand_name) && (
                                                    <div className="text-sm text-muted-foreground">{item.brand_name || item.eyewear_item?.brand_name}</div>
                                                )}
                                                {(item.customization_details || item.eyewear_item?.customization_details) && (
                                                    <div className="text-sm text-muted-foreground mt-1">{item.customization_details || item.eyewear_item?.customization_details}</div>
                                                )}
                                            </td>
                                            <td className="px-4 py-4 text-center">
                                                <span className="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 capitalize">
                                                    {item.item_type}
                                                </span>
                                            </td>
                                            <td className="px-4 py-4 text-right">{item.quantity}</td>
                                            <td className="px-4 py-4 text-right">{formatCurrency(item.unit_price)}</td>
                                            <td className="px-4 py-4 text-right">
                                                {item.discount_percentage > 0 ? (
                                                    <div>
                                                        <div>{item.discount_percentage}%</div>
                                                        <div className="text-sm text-muted-foreground">
                                                            -{formatCurrency(item.discount_amount)}
                                                        </div>
                                                    </div>
                                                ) : '-'}
                                            </td>
                                            <td className="px-4 py-4 text-right">
                                                {item.taxes && item.taxes.length > 0 ? (
                                                    <div>
                                                        {item.taxes.map((tax: any, taxIndex: number) => (
                                                            <div key={taxIndex} className="text-sm">{tax.tax_name} ({tax.tax_rate}%)</div>
                                                        ))}
                                                        <div className="text-sm text-muted-foreground">
                                                            {formatCurrency(item.tax_amount)}
                                                        </div>
                                                    </div>
                                                ) : item.tax_percentage > 0 ? (
                                                    <div>
                                                        <div>{item.tax_percentage}%</div>
                                                        <div className="text-sm text-muted-foreground">
                                                            {formatCurrency(item.tax_amount)}
                                                        </div>
                                                    </div>
                                                ) : '-'}
                                            </td>
                                            <td className="px-4 py-4 text-right font-semibold">
                                                {formatCurrency(item.total_amount)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        <div className="mt-6 flex justify-end">
                            <div className="w-80 space-y-3">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">{t('Subtotal')}</span>
                                    <span className="font-medium">{formatCurrency(eyewearorder.subtotal)}</span>
                                </div>
                                {eyewearorder.discount_amount > 0 && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Discount')}</span>
                                        <span className="font-medium text-red-600">-{formatCurrency(eyewearorder.discount_amount)}</span>
                                    </div>
                                )}
                                {eyewearorder.tax_amount > 0 && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Tax')}</span>
                                        <span className="font-medium">{formatCurrency(eyewearorder.tax_amount)}</span>
                                    </div>
                                )}
                                {eyewearorder.extra_charge > 0 && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Extra Charge')}</span>
                                        <span className="font-medium">{formatCurrency(eyewearorder.extra_charge)}</span>
                                    </div>
                                )}
                                <div className="border-t pt-3">
                                    <div className="flex justify-between">
                                        <span className="font-semibold">{t('Total Amount')}</span>
                                        <span className="font-bold text-lg">{formatCurrency(eyewearorder.total_amount)}</span>
                                    </div>
                                </div>
                                {eyewearorder.paid_amount > 0 && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">{t('Paid Amount')}</span>
                                        <span className="font-medium text-green-600">{formatCurrency(eyewearorder.paid_amount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between">
                                    <span className="font-semibold">{t('Balance Due')}</span>
                                    <span className="font-bold text-lg">{formatCurrency(eyewearorder.balance_amount)}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
