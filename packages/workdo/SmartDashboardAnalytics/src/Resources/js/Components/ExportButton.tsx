import React, { useState } from 'react';
import { usePage } from '@inertiajs/react';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { FileSpreadsheet, FileText, FileDown, ChevronDown, ChevronUp } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { formatDate } from '@/utils/helpers';

export type ExportColumn = {
    key: string;
    header: string;
    render?: (value: any, row: any) => string;
};

interface ExportButtonProps {
    data: any[];
    columns: ExportColumn[];
    filename?: string;
    title?: string;
}

function formatValue(value: any, row: any, col: ExportColumn): string {
    if (col.render) {
        const rendered = col.render(value, row);
        return rendered ? rendered.replace(/<[^>]*>/g, '') : '';
    }
    if (value === null || value === undefined) return '';
    if (typeof value === 'boolean') return value ? 'Yes' : 'No';
    if (typeof value === 'object') {
        try {
            return JSON.stringify(value);
        } catch {
            return String(value);
        }
    }
    return String(value);
}

// PDF-safe version: replaces non-Latin currency symbols with ASCII equivalents
function sanitizeForPdf(text: string): string {
    return text
        .replace(/₹/g, 'Rs.')
        .replace(/₩/g, 'KRW')
        .replace(/₪/g, 'ILS')
        .replace(/₦/g, 'NGN')
        .replace(/₫/g, 'VND')
        .replace(/₨/g, 'Rs.')
        .replace(/฿/g, 'THB')
        .replace(/₴/g, 'UAH')
        .replace(/₲/g, 'PYG')
        .replace(/₡/g, 'CRC');
}

// Use char codes to avoid auto-formatter corrupting HTML entities
const _a = '&';
const _l = '<';
const _g = '>';
const _q = '"';
const _ap = "'";

function escapeXml(str: string): string {
    return String(str)
        .replace(/[&]/g, _a + 'amp;')
        .replace(/[<]/g, _l + 'lt;')
        .replace(/[>]/g, _g + 'gt;')
        .replace(/["]/g, _q + 'quot;')
        .replace(/[']/g, _a + 'apos;');
}

function escapeCsv(str: string): string {
    if (str.indexOf(',') >= 0 || str.indexOf('"') >= 0 || str.indexOf('\n') >= 0 || str.indexOf('\r') >= 0) {
        return '"' + str.replace(/"/g, '""') + '"';
    }
    return str;
}

export default function ExportButton({ data, columns, filename = 'export', title = 'Export' }: ExportButtonProps) {
    const { t } = useTranslation();
    // Access page props so formatCurrency etc can read company settings
    const pageProps = usePage().props;

    const exportToCSV = () => {
        if (!data || data.length === 0) return;

        var headers = columns.map(function(col) { return col.header; });
        var rows = data.map(function(row: any) {
            return columns.map(function(col: ExportColumn) { return formatValue(row[col.key], row, col); });
        });

        var csvLines: string[] = [];
        csvLines.push(headers.map(function(h) { return escapeCsv(h); }).join(','));

        for (var ri = 0; ri < rows.length; ri++) {
            var escapedRow = rows[ri].map(function(cell) { return escapeCsv(cell); });
            csvLines.push(escapedRow.join(','));
        }

        var csvContent = csvLines.join('\r\n');
        var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
        var blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    };

    const exportToExcel = () => {
        if (!data || data.length === 0) return;

        var headers = columns.map(function(col: ExportColumn) { return col.header; });
        var rows = data.map(function(row: any) {
            return columns.map(function(col: ExportColumn) { return formatValue(row[col.key], row, col); });
        });

        var xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        xml += '<?mso-application progid="Excel.Sheet"?>\n';
        xml += '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"\n';
        xml += ' xmlns:o="urn:schemas-microsoft-com:office:office"\n';
        xml += ' xmlns:x="urn:schemas-microsoft-com:office:excel"\n';
        xml += ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n';
        xml += ' <Styles>\n';
        xml += '  <Style ss:ID="Header">\n';
        xml += '   <Font ss:Bold="1"/>\n';
        xml += '  </Style>\n';
        xml += ' </Styles>\n';
        xml += ' <Worksheet ss:Name="Sheet1">\n';
        xml += '  <Table>\n';

        // Header row
        xml += '   <Row>\n';
        for (var hi = 0; hi < headers.length; hi++) {
            xml += '    <Cell ss:StyleID="Header"><Data ss:Type="String">' + escapeXml(String(headers[hi])) + '</Data></Cell>\n';
        }
        xml += '   </Row>\n';

        // Data rows
        for (var ri = 0; ri < rows.length; ri++) {
            xml += '   <Row>\n';
            var row = rows[ri];
            for (var ci = 0; ci < row.length; ci++) {
                var cell = String(row[ci]).trim();
                var isNumeric = cell !== '' && isFinite(Number(cell)) && cell !== '-';
                if (isNumeric) {
                    xml += '    <Cell><Data ss:Type="Number">' + escapeXml(cell) + '</Data></Cell>\n';
                } else {
                    xml += '    <Cell><Data ss:Type="String">' + escapeXml(cell) + '</Data></Cell>\n';
                }
            }
            xml += '   </Row>\n';
        }

        xml += '  </Table>\n';
        xml += ' </Worksheet>\n';
        xml += '</Workbook>';

        // Use manual UTF-8 encoding to avoid TextEncoder issues
        var bytes: number[] = [];
        // BOM
        bytes.push(0xEF, 0xBB, 0xBF);
        // XML content as UTF-8
        for (var i = 0; i < xml.length; i++) {
            var code = xml.charCodeAt(i);
            if (code < 0x80) {
                bytes.push(code);
            } else if (code < 0x800) {
                bytes.push(0xC0 | (code >> 6));
                bytes.push(0x80 | (code & 0x3F));
            } else {
                bytes.push(0xE0 | (code >> 12));
                bytes.push(0x80 | ((code >> 6) & 0x3F));
                bytes.push(0x80 | (code & 0x3F));
            }
        }

        var blob = new Blob([new Uint8Array(bytes)], { type: 'application/vnd.ms-excel;charset=utf-8' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    };

    const exportToPDF = () => {
        if (!data || data.length === 0) return;

        var doc = new jsPDF('landscape');

        doc.setFontSize(16);
        doc.text(sanitizeForPdf(title), 14, 20);

        doc.setFontSize(10);
        doc.text('Generated: ' + sanitizeForPdf(formatDate(new Date(), pageProps)), 14, 28);

        var headers = columns.map(function(col: ExportColumn) { return sanitizeForPdf(col.header); });
        var rows = data.map(function(row: any) {
            return columns.map(function(col: ExportColumn) { return sanitizeForPdf(formatValue(row[col.key], row, col)); });
        });

        autoTable(doc, {
            head: [headers],
            body: rows,
            startY: 34,
            styles: {
                fontSize: 8,
                cellPadding: 2,
            },
            headStyles: {
                fillColor: [16, 185, 129],
                textColor: 255,
                fontStyle: 'bold',
            },
            alternateRowStyles: {
                fillColor: [245, 247, 250],
            },
        });

        doc.save(filename + '.pdf');
    };

    const [isOpen, setIsOpen] = useState(false);

    return (
        <DropdownMenu onOpenChange={setIsOpen}>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm" className="h-9 gap-1.5 text-sm">
                    <FileDown className="h-3.5 w-3.5" />
                    {t('Export')}
                    {isOpen ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                    ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-36">
                <DropdownMenuItem onClick={exportToExcel}>
                    <FileSpreadsheet className="h-4 w-4 mr-2 text-green-600" />
                    {t('Excel')}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={exportToPDF}>
                    <FileText className="h-4 w-4 mr-2 text-red-600" />
                    {t('PDF')}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={exportToCSV}>
                    <FileDown className="h-4 w-4 mr-2 text-blue-600" />
                    {t('CSV')}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}