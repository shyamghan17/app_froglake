import { useEffect, useRef, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import html2pdf from 'html2pdf.js';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { PettyCashReportPrintPageProps } from './types';

export default function PettyCashReportPrint() {
    const { t } = useTranslation();
    const { expenses, totals, filters } = usePage<PettyCashReportPrintPageProps>().props;
    const [isDownloading, setIsDownloading] = useState(false);
    const downloadInitiatedRef = useRef(false);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('download') === 'pdf' && !downloadInitiatedRef.current) {
            downloadInitiatedRef.current = true;
            setTimeout(() => downloadPdf(), 300);
        }
    }, []);

    const downloadPdf = async () => {
        if (isDownloading) return;
        setIsDownloading(true);

        const content = document.querySelector('.report-container');
        if (content) {
            const filenameParts: string[] = ['petty-cash-report'];
            if (filters?.start_date) filenameParts.push(filters.start_date);
            if (filters?.end_date) filenameParts.push(filters.end_date);
            const filename = `${filenameParts.join('-')}.pdf`;

            const opt = {
                margin: 0.35,
                filename,
                image: { type: 'jpeg' as const, quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' as const }
            };

            try {
                await html2pdf().set(opt).from(content as HTMLElement).save();
                setTimeout(() => window.close(), 1000);
            } catch (error) {
                console.error('PDF generation failed:', error);
            }
        }

        setIsDownloading(false);
    };

    return (
        <div className="min-h-screen bg-white">
            <Head title={t('Petty Cash Report')} />

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

            <div className="report-container bg-white max-w-5xl mx-auto p-8">
                <div className="flex items-start justify-between gap-6 mb-6">
                    <div>
                        <h1 className="text-2xl font-bold">{t('Petty Cash Report')}</h1>
                        <div className="text-sm text-gray-600 mt-1">
                            {t('Period')}: {filters?.start_date || '-'} {t('to')} {filters?.end_date || '-'}
                        </div>
                    </div>
                    <div className="text-right">
                        <div className="text-sm text-gray-600">{t('Transactions')}</div>
                        <div className="text-xl font-semibold">{totals?.count ?? 0}</div>
                        <div className="text-sm text-gray-600 mt-2">{t('Total Amount')}</div>
                        <div className="text-lg font-semibold">{formatCurrency(totals?.total_amount || '0')}</div>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div className="border border-gray-300 p-4">
                        <div className="text-sm text-gray-600">{t('Petty Cash Amount')}</div>
                        <div className="text-lg font-semibold">{formatCurrency(totals?.pettycash_amount || '0')}</div>
                    </div>
                    <div className="border border-gray-300 p-4">
                        <div className="text-sm text-gray-600">{t('Reimbursement Amount')}</div>
                        <div className="text-lg font-semibold">{formatCurrency(totals?.reimbursement_amount || '0')}</div>
                    </div>
                </div>

                <div className="border border-gray-300">
                    <table className="w-full table-fixed">
                        <thead>
                            <tr className="border-b border-gray-300 bg-gray-50">
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Petty Cash Date')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Petty Cash Number')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Request/Reimbursement Number')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('User')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Category')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Type')}</th>
                                <th className="text-right py-3 px-3 font-bold text-sm">{t('Amount')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Approved At')}</th>
                                <th className="text-left py-3 px-3 font-bold text-sm">{t('Approved By')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(expenses || []).map((row: any, idx: number) => {
                                const typeLabel = row.type === 'pettycash' ? t('Petty Cash') : row.type === 'reimbursement' ? t('Reimbursement') : row.type;
                                const reference = row.request?.request_number || row.reimbursement?.reimbursement_number || '-';
                                const userName = row.request?.user?.name || row.reimbursement?.user?.name || '-';
                                const categoryName = row.request?.category?.name || row.reimbursement?.category?.name || '-';
                                const pettyCashDate = row.petty_cash?.date ? formatDate(row.petty_cash.date) : '-';
                                const pettyCashNumber = row.petty_cash?.pettycash_number || '-';
                                const approvedAt = row.approved_at ? formatDate(row.approved_at) : '-';
                                const approvedBy = row.approver?.name || '-';

                                return (
                                    <tr key={idx} className="border-b border-gray-200 last:border-b-0">
                                        <td className="py-2 px-3 text-sm">{pettyCashDate}</td>
                                        <td className="py-2 px-3 text-sm">{pettyCashNumber}</td>
                                        <td className="py-2 px-3 text-sm">{reference}</td>
                                        <td className="py-2 px-3 text-sm">{userName}</td>
                                        <td className="py-2 px-3 text-sm">{categoryName}</td>
                                        <td className="py-2 px-3 text-sm">{typeLabel}</td>
                                        <td className="py-2 px-3 text-sm text-right">{row.amount ? formatCurrency(String(row.amount)) : '-'}</td>
                                        <td className="py-2 px-3 text-sm">{approvedAt}</td>
                                        <td className="py-2 px-3 text-sm">{approvedBy}</td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
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
                `}</style>
            </div>
        </div>
    );
}

