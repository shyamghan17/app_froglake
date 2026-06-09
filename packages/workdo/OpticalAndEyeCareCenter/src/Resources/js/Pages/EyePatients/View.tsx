import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { User } from 'lucide-react';
import { EyePatient } from './types';
import { formatDate } from '@/utils/helpers';

interface ViewProps {
    eyepatient: EyePatient;
}

export default function View({ eyepatient }: ViewProps) {
    const { t } = useTranslation();
    const genderOptions: any = { "0": "male", "1": "female", "2": "other" };

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <User className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Eye Patient Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{eyepatient.patient_name}</p>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Patient Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyepatient.patient_name}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Date of Birth')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyepatient.dob ? formatDate(eyepatient.dob) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Gender')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{genderOptions[eyepatient.gender] || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Contact No')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyepatient.contact_no || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Preferred Doctor')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyepatient.doctor?.name || '-'}</p>
                    </div>
                </div>

                {eyepatient.address && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Address')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{eyepatient.address}</p>
                    </div>
                )}

                {eyepatient.medical_history && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Medical History')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyepatient.medical_history}</p>
                    </div>
                )}

                {eyepatient.previous_prescriptions && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Previous Prescriptions')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{eyepatient.previous_prescriptions}</p>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}