import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { formatCurrency, getImagePath } from '@/utils/helpers';
import { OpticalDoctorShowProps } from './types';
import { Stethoscope } from 'lucide-react';

const getGenderLabel = (value: any, t: any) => {
    const options: Record<string, string> = {
        0: t("Male"),
        1: t("Female"),
        2: t("Other"),
    };
    return options[value] || value;
};

const getDoctorStatusBadge = (status: any, t: any) => {
    const statuses: Record<string, string> = {
        0: t("Active"),
        1: t("On Leave"),
        2: t("Busy"),
        3: t("Inactive"),
    };
    const colors: Record<string, string> = {
        0: "bg-green-100 text-green-800",
        1: "bg-yellow-100 text-yellow-800",
        2: "bg-blue-100 text-blue-800",
        3: "bg-red-100 text-red-800",
    };
    const text = statuses[status] || status;
    const colorClass = colors[status] || "bg-gray-100 text-gray-800";

    return `<span class="px-2 py-1 rounded-full text-sm font-medium ${colorClass}">${text}</span>`;
};

export default function View({ opticaldoctor }: OpticalDoctorShowProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Stethoscope className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Doctor Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{opticaldoctor.user?.name || '-'}</p>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Doctor Code')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{opticaldoctor.doctor_code}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('License Number')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{opticaldoctor.license_number || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Gender')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{getGenderLabel(opticaldoctor.gender, t)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Years of Experience')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{opticaldoctor.years_of_experience ? `${opticaldoctor.years_of_experience} ${t('years')}` : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Consultation Fee')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{opticaldoctor.consultation_fee ? formatCurrency(opticaldoctor.consultation_fee) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Status')}</label>
                        <div className="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                            <span dangerouslySetInnerHTML={{ __html: getDoctorStatusBadge(opticaldoctor.status, t) }} />
                        </div>
                    </div>
                </div>

                {opticaldoctor.qualifications && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Qualifications')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{opticaldoctor.qualifications}</p>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}
