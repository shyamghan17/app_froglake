import React, { useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import html2pdf from 'html2pdf.js';
import { formatCurrency, formatDate, getCompanySetting } from '@/utils/helpers';

export default function Print() {
    const { t } = useTranslation();
    const { repairinvoice, repair_order, repair_parts, subtotal, total_discount, total_tax, total_amount } = usePage().props;
    const [isDownloading, setIsDownloading] = useState(false);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('download') === 'pdf') {
            downloadPDF();
        }
    }, []);

    const downloadPDF = async () => {
        setIsDownloading(true);
        const printContent = document.querySelector('.invoice-container');
        if (printContent) {
            const opt = {
                margin: 0.25,
                filename: `repair-invoice-${repairinvoice.invoice_id}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            try {
                await html2pdf().set(opt).from(printContent).save();
                setTimeout(() => window.close(), 1000);
            } catch (error) {
                console.error('PDF generation failed:', error);
            }
        }
        setIsDownloading(false);
    };

    return (
        <div className="min-h-screen bg-white">
            <Head title={t('Repair Invoice')} />

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

            <div className="invoice-container bg-white max-w-4xl mx-auto p-12">
                {/* Header */}
                <div className="flex justify-between items-start mb-12">
                    <div className="w-1/2">
                        <h1 className="text-2xl font-bold mb-4">{getCompanySetting('company_name') || t('YOUR COMPANY')}</h1>
                        <div className="text-sm space-y-1">
                            {getCompanySetting('company_address') && <p>{getCompanySetting('company_address')}</p>}
                            {getCompanySetting('company_telephone') && <p>{t('Phone')}: {getCompanySetting('company_telephone')}</p>}
                            {getCompanySetting('company_email') && <p>{t('Email')}: {getCompanySetting('company_email')}</p>}
                        </div>
                    </div>
                    <div className="text-right w-1/2">
                        <h2 className="text-2xl font-bold mb-2">{t('REPAIR INVOICE')}</h2>
                        <p className="text-lg font-semibold">{repairinvoice.invoice_id}</p>
                        <div className="text-sm mt-2 space-y-1">
                            <p>{t('Date')}: {formatDate(repairinvoice.created_at)}</p>
                            <p>{t('Repair Date')}: {formatDate(repair_order.date)}</p>
                        </div>
                    </div>
                </div>

                <div className="flex justify-between mb-12">
                    <div className="w-1/2">
                        <h3 className="font-bold mb-3">{t('CUSTOMER')}</h3>
                        <div className="text-sm space-y-1">
                            <p className="font-semibold">{repair_order.customer_name}</p>
                            <p>{repair_order.customer_email}</p>
                            <p>{repair_order.customer_mobile_no}</p>
                        </div>
                    </div>
                    <div className="text-right w-1/2">
                        <h3 className="font-bold mb-3">{t('PRODUCT')}</h3>
                        <div className="text-sm space-y-1">
                            <p className="font-semibold">{repair_order.product_name}</p>
                        </div>
                    </div>
                </div>

                {/* Items Table */}
                <div className="mb-8">
                    <table className="w-full table-fixed">
                        <thead>
                            <tr className="border-b border-gray-300">
                                <th className="text-left py-3 font-bold">{t('ITEM')}</th>
                                <th className="text-center py-3 font-bold">{t('QTY')}</th>
                                <th className="text-right py-3 font-bold">{t('PRICE')}</th>
                                <th className="text-right py-3 font-bold">{t('DISCOUNT')}</th>
                                <th className="text-right py-3 font-bold">{t('TAX')}</th>
                                <th className="text-right py-3 font-bold">{t('TOTAL')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {repair_parts.map((part) => (
                                <tr key={part.id} className="page-break-inside-avoid">
                                    <td className="py-4">
                                        <div className="font-semibold">{part.product_name || part.description || t('Repair Part')}</div>
                                    </td>
                                    <td className="text-center py-4">{part.quantity}</td>
                                    <td className="text-right py-4">{formatCurrency(part.price)}</td>
                                    <td className="text-right py-4">
                                        {part.discount > 0 ? `-${formatCurrency(part.discount)}` : '0'}
                                    </td>
                                    <td className="text-right py-4">
                                        {formatCurrency(part.tax_amount || 0)}
                                    </td>
                                    <td className="text-right py-4 font-semibold">{formatCurrency(((part.price * part.quantity) - part.discount) + (part.tax_amount || 0))}</td>
                                </tr>
                            ))}
                            <tr className="page-break-inside-avoid">
                                <td className="py-4">
                                    <div className="font-semibold">{t('Repair Service')}</div>
                                </td>
                                <td className="text-center py-4">1</td>
                                <td className="text-right py-4">{formatCurrency(repairinvoice.repair_charge)}</td>
                                <td className="text-right py-4">0</td>
                                <td className="text-right py-4">0</td>
                                <td className="text-right py-4 font-semibold">{formatCurrency(repairinvoice.repair_charge)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {/* Summary */}
                <div className="flex justify-end mb-8">
                    <div className="w-80">
                        <div className="border border-gray-400 p-4">
                            <div className="space-y-2">
                                <div className="flex justify-between">
                                    <span>{t('Sub Total')}:</span>
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
                                    <span>{formatCurrency(repairinvoice.repair_charge)}</span>
                                </div>
                                <div className="border-t border-gray-400 pt-2 mt-2">
                                    <div className="flex justify-between font-bold text-lg">
                                        <span>{t('TOTAL')}:</span>
                                        <span>{formatCurrency(total_amount)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="border-t border-gray-400 pt-6 text-center">
                    <p className="font-semibold">{t('Thank you for your business!')}</p>
                </div>
            </div>


        </div>
    );
}