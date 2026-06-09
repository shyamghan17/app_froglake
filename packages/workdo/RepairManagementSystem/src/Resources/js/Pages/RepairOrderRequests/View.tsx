import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { usePage } from '@inertiajs/react';
import { Wrench, User, Mail, Phone, MapPin, Calendar, Clock, Settings, CheckCircle } from 'lucide-react';
import { RepairOrderRequest, RepairOrderRequestsIndexProps } from './types';
import { formatDate } from '@/utils/helpers';
import { Badge } from '@/components/ui/badge';

interface ViewProps {
    repairorderrequest: RepairOrderRequest;
}

export default function View({ repairorderrequest }: ViewProps) {
    const { t } = useTranslation();
    const { repairtechnicians, repairstatuses } = usePage<RepairOrderRequestsIndexProps>().props;

    const getTechnicianName = (technicianId: number) => {
        const technician = repairtechnicians?.find(item => item.id.toString() === technicianId?.toString());
        return technician?.name || '-';
    };

    const getStatusInfo = (statusId: number) => {
        const status = repairstatuses?.find(item => item.id.toString() === statusId?.toString());
        const statusColors = {
            '0': 'bg-yellow-100 text-yellow-800',
            '1': 'bg-green-100 text-green-800',
            '2': 'bg-green-100 text-green-800',
            '3': 'bg-green-100 text-green-800',
            '4': 'bg-green-100 text-green-800',
            '5': 'bg-gray-100 text-gray-800',
            '6': 'bg-red-100 text-red-800',
            '7': 'bg-blue-100 text-blue-800'
        };
        return {
            name: status?.name || '-',
            colorClass: statusColors[statusId?.toString()] || 'bg-gray-100 text-gray-800'
        };
    };

    const statusInfo = getStatusInfo(repairorderrequest.status);



    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Wrench className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Order Request Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-6 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Settings className="h-4 w-4" />
                                {t('Product Name')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.product_name || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <User className="h-4 w-4" />
                                {t('Customer Name')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.customer_name || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Mail className="h-4 w-4" />
                                {t('Email')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.customer_email || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Phone className="h-4 w-4" />
                                {t('Mobile Number')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.customer_mobile_no || '-'}</p>
                        </div>
                    </div>
                    
                    <div className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <MapPin className="h-4 w-4" />
                                {t('Location')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.location || '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <User className="h-4 w-4" />
                                {t('Repair Technician')}
                            </label>
                            <p className="mt-1 font-medium">{getTechnicianName(repairorderrequest.repair_technician)}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <CheckCircle className="h-4 w-4" />
                                {t('Status')}
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
                                {t('Request Date')}
                            </label>
                            <p className="mt-1 font-medium">{repairorderrequest.date ? formatDate(repairorderrequest.date) : '-'}</p>
                        </div>
                        
                        <div>
                            <label className="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <Clock className="h-4 w-4" />
                                {t('Expiry Date')}
                            </label>
                            <p className="mt-1 font-medium">
                                {repairorderrequest.expiry_date ? (() => {
                                    const isExpired = new Date(repairorderrequest.expiry_date) <= new Date();
                                    return (
                                        <span className={isExpired ? 'text-red-600 font-medium' : ''}>
                                            {formatDate(repairorderrequest.expiry_date)}
                                        </span>
                                    );
                                })() : '-'}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    );
}