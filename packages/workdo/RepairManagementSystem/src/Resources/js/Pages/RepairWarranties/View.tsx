import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { Shield, Package, Settings, Calendar, FileText, CheckCircle } from 'lucide-react';
import { formatDate } from '@/utils/helpers';
import { RepairWarranty } from './types';

interface ViewRepairWarrantyProps {
    repairwarranty: RepairWarranty;
}

export default function View({ repairwarranty }: ViewRepairWarrantyProps) {
    const { t } = useTranslation();

    const statusInfo = (() => {
        const options: any = {"0":"Active","1":"Pending","2":"Claimed","3":"Expired"};
        const colors: any = {
            "0":"bg-green-100 text-green-800",
            "1":"bg-yellow-100 text-yellow-800", 
            "2":"bg-blue-100 text-blue-800",
            "3":"bg-red-100 text-red-800"
        };
        return {
            name: options[repairwarranty.claim_status] || '-',
            colorClass: colors[repairwarranty.claim_status] || 'bg-gray-100 text-gray-800'
        };
    })();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Shield className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Warranty Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-6 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Shield className="h-4 w-4" />
                                {t('Warranty Number')}
                            </label>
                            <p className="mt-1 font-medium">{repairwarranty.warranty_number || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Settings className="h-4 w-4" />
                                {t('Repair Order')}
                            </label>
                            <p className="mt-1 font-medium">{repairwarranty.repair_order?.product_name || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Package className="h-4 w-4" />
                                {t('Part')}
                            </label>
                            <p className="mt-1 font-medium">{repairwarranty.part?.name || '-'}</p>
                        </div>
                    </div>
                    
                    <div className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <CheckCircle className="h-4 w-4" />
                                {t('Claim Status')}
                            </label>
                            <div className="mt-1">
                                <span className={`px-2 py-1 rounded-full text-sm ${statusInfo.colorClass}`}>
                                    {statusInfo.name}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Calendar className="h-4 w-4" />
                                {t('Warranty Period')}
                            </label>
                            <p className="mt-1 font-medium">
                                {repairwarranty.warranty_period ? (() => {
                                    const dates = repairwarranty.warranty_period.split(' - ');
                                    if (dates.length === 2) {
                                        const endDate = new Date(dates[1]);
                                        const isExpired = !isNaN(endDate.getTime()) && endDate <= new Date();
                                        const startDate = formatDate(dates[0]);
                                        const formattedEndDate = formatDate(dates[1]);
                                        return (
                                            <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                                                {startDate} - {formattedEndDate}
                                            </span>
                                        );
                                    }
                                    const date = new Date(repairwarranty.warranty_period);
                                    const isValidDate = !isNaN(date.getTime());
                                    const isExpired = isValidDate && date <= new Date();
                                    return (
                                        <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                                            {isValidDate ? formatDate(repairwarranty.warranty_period) : repairwarranty.warranty_period}
                                        </span>
                                    );
                                })() : '-'}
                            </p>
                        </div>
                    </div>
                </div>
                
                {repairwarranty.warranty_terms && (
                    <div>
                        <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                            <FileText className="h-4 w-4" />
                            {t('Warranty Terms')}
                        </label>
                        <div className="mt-2 p-3 bg-gray-50 rounded-lg">
                            <p className="text-sm whitespace-pre-wrap">{repairwarranty.warranty_terms}</p>
                        </div>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}