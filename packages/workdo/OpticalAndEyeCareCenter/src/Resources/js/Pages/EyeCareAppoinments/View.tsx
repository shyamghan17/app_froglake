import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { Calendar } from 'lucide-react';
import { EyeCareAppoinment } from './types';
import { formatDateTime } from '@/utils/helpers';

interface ViewProps {
    eyecareappoinment: EyeCareAppoinment;
}

export default function View({ eyecareappoinment }: ViewProps) {
    const { t } = useTranslation();
    
    const getStatusBadge = (status: any) => {
        const statuses: any = {"0":"Scheduled","1":"Confirmed","2":"Completed","3":"Cancelled"};
        const colors: any = {
            "0": "bg-blue-100 text-blue-800",
            "1": "bg-green-100 text-green-800",
            "2": "bg-gray-100 text-gray-800",
            "3": "bg-red-100 text-red-800"
        };
        return { text: statuses[status] || status, color: colors[status] || 'bg-gray-100 text-gray-800' };
    };

    const getTypeBadge = (type: any) => {
        const types: any = {"0":"Consultation","1":"Follow-up","2":"Emergency"};
        const colors: any = {
            "0": "bg-purple-100 text-purple-800",
            "1": "bg-yellow-100 text-yellow-800",
            "2": "bg-orange-100 text-orange-800"
        };
        return { text: types[type] || type, color: colors[type] || 'bg-gray-100 text-gray-800' };
    };

    const statusBadge = getStatusBadge(eyecareappoinment.status);
    const typeBadge = getTypeBadge(eyecareappoinment.appointment_type);

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Calendar className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Eye Care Appointment Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{eyecareappoinment.patient?.patient_name || '-'}</p>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Patient Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyecareappoinment.patient?.patient_name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Doctor Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyecareappoinment.doctor_name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Appointment Date & Time')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyecareappoinment.appointment_datetime ? formatDateTime(eyecareappoinment.appointment_datetime) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Status')}</label>
                        <div className="bg-gray-50 p-2 rounded">
                            <span className={`px-2 py-1 rounded-full text-sm font-medium ${statusBadge.color}`}>
                                {statusBadge.text}
                            </span>
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Appointment Type')}</label>
                        <div className="bg-gray-50 p-2 rounded">
                            <span className={`px-2 py-1 rounded-full text-sm font-medium ${typeBadge.color}`}>
                                {typeBadge.text}
                            </span>
                        </div>
                    </div>
                </div>

                {eyecareappoinment.notes && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Notes')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyecareappoinment.notes}</p>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}
