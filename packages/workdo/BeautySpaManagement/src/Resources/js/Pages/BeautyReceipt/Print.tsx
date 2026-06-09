import React, { useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import html2pdf from 'html2pdf.js';
import { formatCurrency, formatDate } from '@/utils/helpers';

interface ReceiptData {
    id: number;
    beauty_booking_id: number;
    name: string;
    service: string;
    number: string;
    gender: string;
    start_time: string;
    end_time: string;
    price: number;
    payment_type: string;
    created_at: string;
}

interface PrintProps {
    receipt: ReceiptData;
}

export default function Print() {
    const { t } = useTranslation();
    const { receipt } = usePage<PrintProps>().props;
    const [isDownloading, setIsDownloading] = useState(false);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('download') === 'pdf') {
            downloadPDF();
        }
    }, []);

    const downloadPDF = async () => {
        setIsDownloading(true);

        const printContent = document.querySelector('.receipt-container');
        if (printContent) {
            const opt = {
                margin: 0.25,
                filename: `beauty-receipt-${receipt.number}.pdf`,
                image: { type: 'jpeg' as const, quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' as const }
            };

            try {
                await html2pdf().set(opt).from(printContent as HTMLElement).save();
                setTimeout(() => window.close(), 1000);
            } catch (error) {
            }
        }

        setIsDownloading(false);
    };

    return (
        <div className="min-h-screen bg-white">
            <Head title={`Receipt`} />

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

            <div className="receipt-container bg-white max-w-4xl mx-auto p-12">
                {/* Header */}
                <div className="text-center mb-12">
                    <h1 className="text-3xl font-bold mb-2">{t('Beauty spa')}</h1>
                </div>

                {/* Customer Information */}
                <div className="mb-12">
                    <h3 className="font-bold mb-4 text-lg">{t('Customer Information')}</h3>
                    <div className="grid grid-cols-2 gap-8">
                        <div>
                            <span className="font-medium text-gray-600">{t('Name')}:</span>
                            <p className="font-semibold text-lg">{receipt.name}</p>
                        </div>
                        <div>
                            <span className="font-medium text-gray-600">{t('Phone Number')}:</span>
                            <p className="font-semibold text-lg">{receipt.number}</p>
                        </div>
                        <div>
                            <span className="font-medium text-gray-600">{t('Gender')}:</span>
                            <p className="font-semibold text-lg">{receipt.gender}</p>
                        </div>
                        <div>
                            <span className="font-medium text-gray-600">{t('Date')}:</span>
                            <p className="font-semibold text-lg">{formatDate(receipt.created_at)}</p>
                        </div>
                    </div>
                </div>

                {/* Service Table */}
                <div className="mb-8">
                    <table className="w-full table-fixed">
                        <thead>
                            <tr className="border-b-2 border-gray-300">
                                <th className="text-left py-3 font-bold">{t('Service')}</th>
                                <th className="text-center py-3 font-bold">{t('Time')}</th>
                                <th className="text-right py-3 font-bold">{t('Price')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr className="border-b border-gray-200">
                                <td className="py-4">
                                    <div className="font-semibold">{receipt.service}</div>
                                </td>
                                <td className="text-center py-4">{receipt.start_time} - {receipt.end_time}</td>
                                <td className="text-right py-4 font-semibold">{formatCurrency(receipt.price)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {/* Summary */}
                <div className="flex justify-end mb-8">
                    <div className="w-80">
                        <div className="border-2 border-gray-400 p-4">
                            <div className="space-y-2">
                                <div className="flex justify-between">
                                    <span>{t('Subtotal')}:</span>
                                    <span>{formatCurrency(receipt.price)}</span>
                                </div>
                                <div className="border-t border-gray-400 pt-2 mt-2">
                                    <div className="flex justify-between font-bold text-lg">
                                        <span>{t('TOTAL')}:</span>
                                        <span>{formatCurrency(receipt.price)}</span>
                                    </div>
                                </div>
                                <div className="flex justify-between text-sm mt-2">
                                    <span>{t('Payment Method')}:</span>
                                    <span className="font-medium">{receipt.payment_type}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="border-t-2 border-gray-400 pt-6 text-center">
                    <p className="font-semibold">{t('Thank you for choosing our beauty spa services!')}</p>
                    <p className="text-sm mt-2">{t('We look forward to serving you again!')}</p>
                </div>
            </div>

            <style>{`
                body {
                    -webkit-print-color-adjust: exact;
                    color-adjust: exact;
                    font-family: Arial, sans-serif;
                }

                @page {
                    margin: 0.25in;
                    size: A4;
                }

                .receipt-container {
                    max-width: 100%;
                    margin: 0;
                    box-shadow: none;
                }

                table {
                    border-collapse: collapse;
                }

                .page-break-inside-avoid {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }

                @media print {
                    body {
                        background: white;
                    }

                    .receipt-container {
                        box-shadow: none;
                    }
                }
            `}</style>
        </div>
    );
}
