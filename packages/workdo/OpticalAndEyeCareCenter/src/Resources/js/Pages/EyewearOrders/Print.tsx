import React, { useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import html2pdf from 'html2pdf.js';
import { formatCurrency, formatDate, getCompanySetting } from '@/utils/helpers';

interface PrintProps {
    order: any;
    [key: string]: any;
}

export default function Print() {
    const { t } = useTranslation();
    const { order } = usePage<PrintProps>().props;
    const [isDownloading, setIsDownloading] = useState(false);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('download') === 'pdf') {
            setTimeout(() => downloadPDF(), 1000);
        }
    }, []);

    const downloadPDF = async () => {
        setIsDownloading(true);

        const printContent = document.querySelector('.order-container');
        if (printContent) {
            const opt = {
                margin: 0.25,
                filename: `eyewear-order-${order.order_number}.pdf`,
                image: { type: 'jpeg' as const, quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' as const }
            };

            try {
                await html2pdf().set(opt).from(printContent as HTMLElement).save();
                setTimeout(() => window.close(), 1000);
            } catch (error) {
                console.error('PDF generation failed:', error);
            }
        }

        setIsDownloading(false);
    };

    return (
        <div className="min-h-screen bg-white">
            <Head title={t('Eyewear Order')} />

            {isDownloading && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white p-6 rounded-lg shadow-lg">
                        <div className="flex items-center space-x-3">
                            <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <p className="text-lg font-semibold text-gray-700">{t('Generating PDF...')}</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="order-container bg-white max-w-4xl mx-auto p-8">
                <div className="flex justify-between items-start mb-8">
                    <div className="w-1/2">
                        <h1 className="text-2xl font-bold mb-4">{getCompanySetting('company_name') || 'YOUR COMPANY'}</h1>
                        <div className="text-sm space-y-1">
                            {getCompanySetting('company_address') && <p>{getCompanySetting('company_address')}</p>}
                            {(getCompanySetting('company_city') || getCompanySetting('company_state') || getCompanySetting('company_zipcode')) && (
                                <p>
                                    {getCompanySetting('company_city')}{getCompanySetting('company_state') && `, ${getCompanySetting('company_state')}`} {getCompanySetting('company_zipcode')}
                                </p>
                            )}
                            {getCompanySetting('company_country') && <p>{getCompanySetting('company_country')}</p>}
                            {getCompanySetting('company_telephone') && <p>{t('Phone')}: {getCompanySetting('company_telephone')}</p>}
                            {getCompanySetting('company_email') && <p>{t('Email')}: {getCompanySetting('company_email')}</p>}
                            {getCompanySetting('registration_number') && <p>{t('Registration')}: {getCompanySetting('registration_number')}</p>}
                        </div>
                    </div>
                    <div className="text-right w-1/2">
                        <h2 className="text-2xl font-bold mb-2">{t('EYEWEAR ORDER')}</h2>
                        <p className="text-lg font-semibold">#{order.order_number}</p>
                        <div className="text-sm mt-2 space-y-1">
                            <p>{t('Order Date')}: {formatDate(order.order_date)}</p>
                            {order.delivery_date && <p>{t('Delivery Date')}: {formatDate(order.delivery_date)}</p>}
                        </div>
                    </div>
                </div>

                <div className="flex justify-between mb-8">
                    <div className="w-1/2">
                        <h3 className="font-bold mb-3">{t('PATIENT')}</h3>
                        <div className="text-sm space-y-1">
                            <p className="font-semibold">{order.patient?.patient_name}</p>
                            <p>{order.patient?.contact_no}</p>
                            {order.patient?.address && <p>{order.patient.address}</p>}
                        </div>
                    </div>
                    <div className="text-right w-1/2">
                        <h3 className="font-bold mb-3">{t('PRESCRIPTION')}</h3>
                        <div className="text-sm space-y-1">
                            <p>{order.prescription_details || t('No prescription details')}</p>
                            {order.special_notes && (
                                <>
                                    <p className="font-semibold mt-2">{t('Special Notes')}:</p>
                                    <p>{order.special_notes}</p>
                                </>
                            )}
                        </div>
                    </div>
                </div>

                <div className="mb-8">
                    <table className="w-full table-fixed">
                        <thead>
                            <tr className="border-b border-gray-300">
                                <th className="text-left py-3 font-bold">{t('PRODUCT')}</th>
                                <th className="text-center py-3 font-bold">{t('TYPE')}</th>
                                <th className="text-center py-3 font-bold">{t('QTY')}</th>
                                <th className="text-right py-3 font-bold">{t('PRICE')}</th>
                                <th className="text-right py-3 font-bold">{t('DISCOUNT')}</th>
                                <th className="text-right py-3 font-bold">{t('TAX')}</th>
                                <th className="text-right py-3 font-bold">{t('TOTAL')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {order.items?.map((item: any, index: number) => (
                                <tr key={index} className="page-break-inside-avoid">
                                    <td className="py-4">
                                        <div className="font-semibold">{item.product_name || item.product?.name || '-'}</div>
                                        {(item.brand_name || item.eyewear_item?.brand_name) && (
                                            <div className="text-xs text-gray-500">{item.brand_name || item.eyewear_item?.brand_name}</div>
                                        )}
                                        {(item.customization_details || item.eyewear_item?.customization_details) && (
                                            <div className="text-xs text-gray-500">{item.customization_details || item.eyewear_item?.customization_details}</div>
                                        )}
                                    </td>
                                    <td className="text-center py-4 capitalize">{item.item_type}</td>
                                    <td className="text-center py-4">{item.quantity}</td>
                                    <td className="text-right py-4">{formatCurrency(item.unit_price)}</td>
                                    <td className="text-right py-4">
                                        {item.discount_percentage > 0 ? (
                                            <>
                                                <div className="text-sm">{item.discount_percentage}%</div>
                                                <div className="text-sm font-medium">-{formatCurrency(item.discount_amount)}</div>
                                            </>
                                        ) : (
                                            <div className="text-sm">0%</div>
                                        )}
                                    </td>
                                    <td className="text-right py-4">
                                        {item.taxes && item.taxes.length > 0 ? (
                                            <>
                                                {item.taxes.map((tax: any, taxIndex: number) => (
                                                    <div key={taxIndex} className="text-sm">{tax.tax_name} ({tax.tax_rate}%)</div>
                                                ))}
                                                <div className="text-sm font-medium">{formatCurrency(item.tax_amount)}</div>
                                            </>
                                        ) : item.tax_percentage > 0 ? (
                                            <>
                                                <div className="text-sm">{item.tax_percentage}%</div>
                                                <div className="text-sm font-medium">{formatCurrency(item.tax_amount)}</div>
                                            </>
                                        ) : (
                                            <div className="text-sm">0%</div>
                                        )}
                                    </td>
                                    <td className="text-right py-4 font-semibold">{formatCurrency(item.total_amount)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="flex justify-end mb-4 page-break-inside-avoid">
                    <div className="w-80 page-break-inside-avoid">
                        <div className="border border-gray-400 p-4 page-break-inside-avoid">
                            <div className="space-y-2">
                                <div className="flex justify-between">
                                    <span>{t('Subtotal')}:</span>
                                    <span>{formatCurrency(order.subtotal)}</span>
                                </div>
                                {order.discount_amount > 0 && (
                                    <div className="flex justify-between">
                                        <span>{t('Discount')}:</span>
                                        <span>-{formatCurrency(order.discount_amount)}</span>
                                    </div>
                                )}
                                {order.tax_amount > 0 && (
                                    <div className="flex justify-between">
                                        <span>{t('Tax')}:</span>
                                        <span>{formatCurrency(order.tax_amount)}</span>
                                    </div>
                                )}
                                {order.extra_charge > 0 && (
                                    <div className="flex justify-between">
                                        <span>{t('Extra Charge')}:</span>
                                        <span>{formatCurrency(order.extra_charge)}</span>
                                    </div>
                                )}
                                <div className="border-t border-gray-400 pt-2 mt-2">
                                    <div className="flex justify-between font-bold text-lg">
                                        <span>{t('TOTAL')}:</span>
                                        <span>{formatCurrency(order.total_amount)}</span>
                                    </div>
                                </div>
                                {order.paid_amount > 0 && (
                                    <div className="flex justify-between">
                                        <span>{t('Paid Amount')}:</span>
                                        <span className="text-green-600">{formatCurrency(order.paid_amount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between font-bold">
                                    <span>{t('Balance Due')}:</span>
                                    <span>{formatCurrency(order.balance_amount)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="border-t border-gray-400 pt-4 text-center">
                    <p className="font-semibold">{t('PAYMENT METHOD')}: {order.payment_method ? order.payment_method.toUpperCase() : t('N/A')}</p>
                    <p className="text-sm mt-2">{t('Thank you for your business!')}</p>
                </div>
            </div>

            <style>{`
                body {
                    -webkit-print-color-adjust: exact;
                    color-adjust: exact;
                    font-family: Arial, sans-serif;
                }

                @page {
                    margin: 0.5in;
                    size: A4;
                }

                .order-container {
                    max-width: 100%;
                    margin: 0;
                    box-shadow: none;
                }

                .page-break-inside-avoid {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }

                @media print {
                    body {
                        background: white;
                    }

                    .order-container {
                        box-shadow: none;
                    }
                }
            `}</style>
        </div>
    );
}
