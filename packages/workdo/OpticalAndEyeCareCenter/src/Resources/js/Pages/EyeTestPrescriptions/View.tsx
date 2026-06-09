import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { FileText } from 'lucide-react';
import { EyeTestPrescription } from './types';
import { formatDate } from '@/utils/helpers';

interface ViewProps {
    eyetestprescription: EyeTestPrescription;
}

export default function View({ eyetestprescription }: ViewProps) {
    const { t } = useTranslation();
    const isExpired = eyetestprescription.prescription_expiry_date && new Date(eyetestprescription.prescription_expiry_date) < new Date();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <FileText className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Eye Test Prescription Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{eyetestprescription.patient?.patient_name || '-'}</p>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Patient Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyetestprescription.patient?.patient_name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Doctor Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyetestprescription.doctor_name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Test Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyetestprescription.test_date ? formatDate(eyetestprescription.test_date) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Prescription Expiry Date')}</label>
                        <p className={`text-sm bg-gray-50 p-2 rounded ${isExpired ? 'text-red-600 font-semibold' : 'text-gray-900'}`}>
                            {eyetestprescription.prescription_expiry_date ? formatDate(eyetestprescription.prescription_expiry_date) : '-'}
                            {isExpired && <span className="ml-2 text-xs">({t('Expired')})</span>}
                        </p>
                    </div>
                </div>

                {eyetestprescription.test_results && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Test Results')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyetestprescription.test_results}</p>
                    </div>
                )}

                {eyetestprescription.prescription_details && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Prescription Details')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyetestprescription.prescription_details}</p>
                    </div>
                )}

                {eyetestprescription.notes && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Notes')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyetestprescription.notes}</p>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}
