import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { usePage } from '@inertiajs/react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Download } from 'lucide-react';
import { Employee } from './types';
import { formatDate } from '@/utils/helpers';
import { usePDFGenerator } from './usePDFGenerator';

interface DownloadDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    employees: Employee[];
    scheduleData?: any;
    startDate: string;
    endDate: string;
    leaveApplications?: any;
}

export default function DownloadDialog({
    open,
    onOpenChange,
    employees,
    scheduleData,
    startDate,
    endDate,
    leaveApplications
}: DownloadDialogProps) {
    const { t } = useTranslation();
    const [selectedEmployees, setSelectedEmployees] = useState<number[]>([]);
    const [isDownloading, setIsDownloading] = useState(false);

    const { generatePDF } = usePDFGenerator({
        employees,
        scheduleData,
        startDate,
        endDate,
        leaveApplications,
        onlyPublished: true
    });

    const handleSelectAll = () => {
        if (selectedEmployees.length === employees.length) {
            setSelectedEmployees([]);
        } else {
            setSelectedEmployees(employees.map(emp => emp.id));
        }
    };

    const handleEmployeeToggle = (employeeId: number) => {
        setSelectedEmployees(prev =>
            prev.includes(employeeId)
                ? prev.filter(id => id !== employeeId)
                : [...prev, employeeId]
        );
    };

    const handleDownload = async () => {
        if (selectedEmployees.length === 0) return;
        setIsDownloading(true);
        try {
            const filteredEmployees = employees.filter(emp => selectedEmployees.includes(emp.id));
            await generatePDF(filteredEmployees, t('Selected Employees Schedule'));
            onOpenChange(false);
            setSelectedEmployees([]);
        } catch (error) {
            console.error('Download failed:', error);
        } finally {
            setIsDownloading(false);
        }
    };

    const { pageProps } = usePage().props as any;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{t('Download Rotas')}</DialogTitle>
                    <p className="text-sm text-muted-foreground">
                        {t('Select employees to include in the PDF download for')} {formatDate(startDate, pageProps)} - {formatDate(endDate, pageProps)} - {formatDate(endDate, pageProps)}
                    </p>
                </DialogHeader>

                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <span className="text-sm font-medium">{t('Select Employees')}</span>
                        <Button variant="outline" size="sm" onClick={handleSelectAll}>
                            {selectedEmployees.length === employees.length ? t('Deselect All') : t('Select All')}
                        </Button>
                    </div>

                    <div className="max-h-80 overflow-y-auto border rounded-md p-4">
                        <div className="grid grid-cols-2 gap-3">
                            {employees.map((employee) => (
                                <div key={employee.id} className="flex items-center space-x-3 p-2 rounded hover:bg-gray-50">
                                    <Checkbox
                                        id={`employee-${employee.id}`}
                                        checked={selectedEmployees.includes(employee.id)}
                                        onCheckedChange={() => handleEmployeeToggle(employee.id)}
                                    />
                                    <div className="flex items-center space-x-2 flex-1">
                                        <div className="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span className="text-xs font-medium text-primary">
                                                {employee.user?.name?.charAt(0).toUpperCase() || ''}
                                            </span>
                                        </div>
                                        <label
                                            htmlFor={`employee-${employee.id}`}
                                            className="text-sm font-medium leading-none cursor-pointer flex-1"
                                        >
                                            {employee.user?.name || t('No User Assigned')}
                                        </label>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="flex justify-end space-x-2">
                        <Button variant="outline" onClick={() => onOpenChange(false)}>
                            {t('Cancel')}
                        </Button>
                        <Button
                            onClick={handleDownload}
                            disabled={selectedEmployees.length === 0 || isDownloading}
                        >
                            <Download className="h-4 w-4 mr-2" />
                            {isDownloading ? t('Downloading...') : t('Download PDF')}
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}