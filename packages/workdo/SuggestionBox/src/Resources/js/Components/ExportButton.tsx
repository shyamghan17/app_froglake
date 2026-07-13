import React from 'react';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { FileDown, FileSpreadsheet, FileText, ChevronDown } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

interface ExportColumn {
    key: string;
    header: string;
    render?: (value: any, row: any) => string;
}

interface ExportButtonProps {
    data: any[];
    columns: ExportColumn[];
    filename: string;
    title: string;
    className?: string;
    variant?: 'default' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    fetchAllData?: () => Promise<any[]>;
}

export default function ExportButton({
    data,
    columns,
    filename,
    title,
    className = '',
    variant = 'outline',
    size = 'sm',
    fetchAllData
}: ExportButtonProps) {
    const { t } = useTranslation();

    const formatValue = (value: any, row: any, col: ExportColumn): string => {
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
    };

    const escapeXml = (str: string): string => {
        return String(str)
            .replace(/[&]/g, '&amp;')
            .replace(/[<]/g, '&lt;')
            .replace(/[>]/g, '&gt;')
            .replace(/["]/g, '&quot;')
            .replace(/[']/g, '&apos;');
    };

    const escapeCsv = (str: string): string => {
        if (str.indexOf(',') >= 0 || str.indexOf('"') >= 0 || str.indexOf('\n') >= 0 || str.indexOf('\r') >= 0) {
            return '"' + str.replace(/"/g, '""') + '"';
        }
        return str;
    };

    const exportToCSV = (exportData: any[], columns: ExportColumn[], filename: string) => {
        if (!exportData || exportData.length === 0) return;

        const headers = columns.map(col => col.header);
        const rows = exportData.map(row => {
            return columns.map(col => formatValue(row[col.key], row, col));
        });

        const csvLines: string[] = [];
        csvLines.push(headers.map(h => escapeCsv(h)).join(','));

        for (let i = 0; i < rows.length; i++) {
            const escapedRow = rows[i].map(cell => escapeCsv(cell));
            csvLines.push(escapedRow.join(','));
        }

        const csvContent = csvLines.join('\r\n');
        const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
        const blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    };

    const exportToExcel = (exportData: any[], columns: ExportColumn[], filename: string) => {
        if (!exportData || exportData.length === 0) return;

        const headers = columns.map(col => col.header);
        const rows = exportData.map(row => {
            return columns.map(col => formatValue(row[col.key], row, col));
        });

        let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
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

        xml += '   <Row>\n';
        for (let i = 0; i < headers.length; i++) {
            xml += '    <Cell ss:StyleID="Header"><Data ss:Type="String">' + escapeXml(String(headers[i])) + '</Data></Cell>\n';
        }
        xml += '   </Row>\n';

        for (let i = 0; i < rows.length; i++) {
            xml += '   <Row>\n';
            const row = rows[i];
            for (let j = 0; j < row.length; j++) {
                const cell = String(row[j]).trim();
                const isNumeric = cell !== '' && isFinite(Number(cell)) && cell !== '-';
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

        const bytes: number[] = [];
        bytes.push(0xEF, 0xBB, 0xBF);
        for (let i = 0; i < xml.length; i++) {
            const code = xml.charCodeAt(i);
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

        const blob = new Blob([new Uint8Array(bytes)], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    };

    const exportToPDF = (exportData: any[], columns: ExportColumn[], filename: string, title: string) => {
        if (!exportData || exportData.length === 0) return;

        const doc = new jsPDF('landscape');
        
        doc.setFontSize(16);
        doc.text(title, 14, 20);
        
        doc.setFontSize(10);
        doc.text('Generated: ' + new Date().toLocaleDateString(), 14, 28);
        
        const headers = columns.map(col => col.header);
        const rows = exportData.map(row => {
            return columns.map(col => formatValue(row[col.key], row, col));
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
                fillColor: [59, 130, 246],
                textColor: 255,
                fontStyle: 'bold',
            },
            alternateRowStyles: {
                fillColor: [245, 247, 250],
            },
        });
        
        doc.save(filename + '.pdf');
    };

    const handleExport = async (format: 'csv' | 'excel' | 'pdf') => {
        try {
            let exportData = data;
            
            if (fetchAllData) {
                exportData = await fetchAllData();
            }

            if (!exportData || exportData.length === 0) {
                return;
            }

            switch (format) {
                case 'csv':
                    exportToCSV(exportData, columns, filename);
                    break;
                case 'excel':
                    exportToExcel(exportData, columns, filename);
                    break;
                case 'pdf':
                    exportToPDF(exportData, columns, filename, title);
                    break;
            }
        } catch (error) {
            console.error('Export error:', error);
        }
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button 
                    variant={variant} 
                    className={`flex items-center gap-2 ${className}`}
                >
                    <FileDown className="h-4 w-4" />
                    {t('Export')}
                    <ChevronDown className="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-36">
                <DropdownMenuItem onClick={() => handleExport('excel')}>
                    <FileSpreadsheet className="h-4 w-4 mr-2 text-green-600" />
                    {t('Excel')}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => handleExport('pdf')}>
                    <FileText className="h-4 w-4 mr-2 text-red-600" />
                    {t('PDF')}
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => handleExport('csv')}>
                    <FileDown className="h-4 w-4 mr-2 text-blue-600" />
                    {t('CSV')}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}