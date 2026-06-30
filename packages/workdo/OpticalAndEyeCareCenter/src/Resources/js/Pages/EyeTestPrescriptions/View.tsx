import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { FileText } from 'lucide-react';
import { EyeTestPrescription } from './types';
import { formatDate } from '@/utils/helpers';
import {
    DisplayItem,
    getClinicalSummaryLines,
    getDiagnosisItems,
    getDoctorDisplayName,
    getExaminerItems,
    getEyeDiagramItems,
    getEyeExaminationItems,
    getGlassesItems,
    getIntraocularPressureItems,
    getMedicalHistoryItems,
    getMedicineItems,
    getPrescriptionRecordType,
    getRefractionItems,
    getVisualAcuityItems,
    hasStructuredClinicalData,
} from './presentation';

interface ViewProps {
    eyetestprescription: EyeTestPrescription;
}

export default function View({ eyetestprescription }: ViewProps) {
    const { t } = useTranslation();
    const isExpired = eyetestprescription.prescription_expiry_date && new Date(eyetestprescription.prescription_expiry_date) < new Date();
    const hasStructuredData = hasStructuredClinicalData(eyetestprescription);
    const recordType = getPrescriptionRecordType(eyetestprescription);
    const doctorDisplayName = getDoctorDisplayName(eyetestprescription);
    const clinicalSummary = getClinicalSummaryLines(eyetestprescription);
    const diagnosisItems = getDiagnosisItems(eyetestprescription);
    const visualAcuityItems = getVisualAcuityItems(eyetestprescription);
    const refractionItems = getRefractionItems(eyetestprescription);
    const eyeExaminationItems = getEyeExaminationItems(eyetestprescription);
    const intraocularPressureItems = getIntraocularPressureItems(eyetestprescription);
    const medicalHistoryItems = getMedicalHistoryItems(eyetestprescription);
    const medicineItems = getMedicineItems(eyetestprescription);
    const glassesItems = getGlassesItems(eyetestprescription);
    const eyeDiagramItems = getEyeDiagramItems(eyetestprescription);
    const examinerItems = getExaminerItems(eyetestprescription);
    const structuredNotes = eyetestprescription.notes?.trim() || '';
    const glassesNotes = eyetestprescription.glasses_prescription?.notes?.trim() || '';
    const legacySections = [
        {
            title: t('Legacy Test Results'),
            value: eyetestprescription.test_results?.trim() || '',
        },
        {
            title: t('Legacy Prescription Details'),
            value: eyetestprescription.prescription_details?.trim() || '',
        },
    ].filter((section) => section.value);

    return (
        <DialogContent className="max-w-5xl max-h-[90vh] overflow-hidden">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <FileText className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <div className="flex items-center gap-2">
                            <DialogTitle className="text-xl font-semibold">{t('Eye Test Prescription Details')}</DialogTitle>
                            <Badge variant={recordType === 'Structured' ? 'default' : 'outline'}>
                                {t(recordType)}
                            </Badge>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            {eyetestprescription.patient?.patient_name || '-'}
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{t('Visit Overview')}</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <DetailField label={t('Patient Name')} value={eyetestprescription.patient?.patient_name || '-'} />
                        <DetailField label={t('Doctor Name')} value={doctorDisplayName} />
                        <DetailField label={t('Test Date')} value={eyetestprescription.test_date ? formatDate(eyetestprescription.test_date) : '-'} />
                        <DetailField label={t('Follow Up Date')} value={eyetestprescription.follow_up_date ? formatDate(eyetestprescription.follow_up_date) : '-'} />
                        <DetailField
                            label={t('Prescription Expiry Date')}
                            value={eyetestprescription.prescription_expiry_date ? formatDate(eyetestprescription.prescription_expiry_date) : '-'}
                            valueClassName={isExpired ? 'text-red-600 font-semibold' : undefined}
                            suffix={isExpired ? ` (${t('Expired')})` : undefined}
                        />
                        <DetailField label={t('Clinical Summary')} value={clinicalSummary.length > 0 ? clinicalSummary.join(' | ') : '-'} />
                    </CardContent>
                </Card>

                {hasStructuredData ? (
                    <>
                        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <StructuredSectionCard title={t('Chief Complaints')}>
                                {eyetestprescription.complaints ? (
                                    <div className="space-y-3">
                                        {Array.isArray(eyetestprescription.complaints.items) && eyetestprescription.complaints.items.length > 0 && (
                                            <DetailField
                                                label={t('Selected Complaints')}
                                                value={eyetestprescription.complaints.items.join(', ')}
                                            />
                                        )}
                                        {eyetestprescription.complaints.custom && (
                                            <DetailField label={t('Custom Complaint')} value={eyetestprescription.complaints.custom} />
                                        )}
                                        {eyetestprescription.complaints.affected_eye && eyetestprescription.complaints.affected_eye !== 'na' && (
                                            <DetailField
                                                label={t('Affected Eye')}
                                                value={t(eyetestprescription.complaints.affected_eye === 'both' ? 'Both Eyes' : eyetestprescription.complaints.affected_eye === 'right' ? 'Right Eye' : 'Left Eye')}
                                            />
                                        )}
                                    </div>
                                ) : (
                                    <EmptyState label={t('No structured complaint data recorded.')} />
                                )}
                            </StructuredSectionCard>

                            <StructuredSectionCard title={t('Diagnosis')}>
                                {diagnosisItems.length > 0 ? (
                                    <DetailList items={diagnosisItems} />
                                ) : (
                                    <EmptyState label={t('No structured diagnosis recorded.')} />
                                )}
                            </StructuredSectionCard>
                        </div>

                        <PerEyeSection title={t('Visual Acuity')} items={visualAcuityItems} emptyLabel={t('No visual acuity data recorded.')} />
                        <PerEyeSection title={t('Refraction')} items={refractionItems} emptyLabel={t('No refraction data recorded.')} />
                        <PerEyeSection title={t('Eye Examination')} items={eyeExaminationItems} emptyLabel={t('No eye examination data recorded.')} />

                        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <StructuredSectionCard title={t('Intraocular Pressure')}>
                                {intraocularPressureItems.length > 0 ? (
                                    <DetailList items={intraocularPressureItems} />
                                ) : (
                                    <EmptyState label={t('No IOP data recorded.')} />
                                )}
                            </StructuredSectionCard>

                            <StructuredSectionCard title={t('Medical History')}>
                                {medicalHistoryItems.length > 0 ? (
                                    <DetailList items={medicalHistoryItems} />
                                ) : (
                                    <EmptyState label={t('No medical history recorded.')} />
                                )}
                            </StructuredSectionCard>
                        </div>

                        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <StructuredSectionCard title={t('Medicines')}>
                                {medicineItems.length > 0 ? (
                                    <ul className="space-y-2 text-sm text-gray-900">
                                        {medicineItems.map((item) => (
                                            <li key={item} className="rounded-md bg-gray-50 px-3 py-2">
                                                {item}
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <EmptyState label={t('No medicines recorded.')} />
                                )}
                            </StructuredSectionCard>

                            <StructuredSectionCard title={t('Glasses Prescription')}>
                                <div className="space-y-4">
                                    <PerEyeMiniGrid items={glassesItems} emptyLabel={t('No glasses prescription recorded.')} />
                                    {glassesNotes && (
                                        <DetailField label={t('Notes')} value={glassesNotes} />
                                    )}
                                </div>
                            </StructuredSectionCard>
                        </div>

                        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <StructuredSectionCard title={t('Eye Diagram')}>
                                {eyeDiagramItems.length > 0 ? (
                                    <DetailList items={eyeDiagramItems} />
                                ) : (
                                    <EmptyState label={t('No eye diagram notes recorded.')} />
                                )}
                            </StructuredSectionCard>

                            <StructuredSectionCard title={t('Examiner Details')}>
                                {examinerItems.length > 0 ? (
                                    <DetailList items={examinerItems} />
                                ) : (
                                    <EmptyState label={t('No examiner details recorded.')} />
                                )}
                            </StructuredSectionCard>
                        </div>

                        {structuredNotes && (
                            <StructuredSectionCard title={t('Clinical Notes')}>
                                <p className="whitespace-pre-wrap rounded-md bg-gray-50 px-3 py-3 text-sm text-gray-900">
                                    {structuredNotes}
                                </p>
                            </StructuredSectionCard>
                        )}
                    </>
                ) : (
                    <StructuredSectionCard title={t('Legacy Clinical Record')}>
                        <div className="space-y-4">
                            {eyetestprescription.test_results && (
                                <DetailBlock title={t('Test Results')} value={eyetestprescription.test_results} />
                            )}
                            {eyetestprescription.prescription_details && (
                                <DetailBlock title={t('Prescription Details')} value={eyetestprescription.prescription_details} />
                            )}
                            {structuredNotes && (
                                <DetailBlock title={t('Notes')} value={structuredNotes} />
                            )}
                        </div>
                    </StructuredSectionCard>
                )}

                {hasStructuredData && legacySections.length > 0 && (
                    <StructuredSectionCard title={t('Legacy Compatibility Fields')}>
                        <div className="space-y-4">
                            {legacySections.map((section) => (
                                <DetailBlock key={section.title} title={section.title} value={section.value} />
                            ))}
                        </div>
                    </StructuredSectionCard>
                )}
            </div>
        </DialogContent>
    );
}

function StructuredSectionCard({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <Card>
            <CardHeader className="pb-3">
                <CardTitle className="text-base">{title}</CardTitle>
            </CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}

function DetailField({
    label,
    value,
    valueClassName,
    suffix,
}: {
    label: string;
    value: string;
    valueClassName?: string;
    suffix?: string;
}) {
    return (
        <div className="space-y-1">
            <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{label}</p>
            <p className={`text-sm text-gray-900 ${valueClassName ?? ''}`.trim()}>
                {value}
                {suffix}
            </p>
        </div>
    );
}

function DetailList({ items }: { items: DisplayItem[] }) {
    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {items.map((item) => (
                <DetailField key={`${item.label}-${item.value}`} label={item.label} value={item.value} />
            ))}
        </div>
    );
}

function PerEyeSection({
    title,
    items,
    emptyLabel,
}: {
    title: string;
    items: Record<'right' | 'left', DisplayItem[]>;
    emptyLabel: string;
}) {
    const hasItems = items.right.length > 0 || items.left.length > 0;

    return (
        <StructuredSectionCard title={title}>
            {hasItems ? (
                <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <EyeColumn title="Right Eye" items={items.right} />
                    <EyeColumn title="Left Eye" items={items.left} />
                </div>
            ) : (
                <EmptyState label={emptyLabel} />
            )}
        </StructuredSectionCard>
    );
}

function PerEyeMiniGrid({
    items,
    emptyLabel,
}: {
    items: Record<'right' | 'left', DisplayItem[]>;
    emptyLabel: string;
}) {
    const hasItems = items.right.length > 0 || items.left.length > 0;

    if (!hasItems) {
        return <EmptyState label={emptyLabel} />;
    }

    return (
        <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <EyeColumn title="Right Eye" items={items.right} />
            <EyeColumn title="Left Eye" items={items.left} />
        </div>
    );
}

function EyeColumn({
    title,
    items,
}: {
    title: string;
    items: DisplayItem[];
}) {
    return (
        <div className="rounded-lg border bg-gray-50 p-4">
            <p className="mb-3 text-sm font-semibold text-gray-900">{title}</p>
            {items.length > 0 ? (
                <div className="space-y-3">
                    {items.map((item) => (
                        <DetailField key={`${title}-${item.label}-${item.value}`} label={item.label} value={item.value} />
                    ))}
                </div>
            ) : (
                <p className="text-sm text-muted-foreground">-</p>
            )}
        </div>
    );
}

function DetailBlock({
    title,
    value,
}: {
    title: string;
    value: string;
}) {
    return (
        <div className="space-y-2">
            <p className="text-sm font-medium text-gray-700">{title}</p>
            <p className="whitespace-pre-wrap rounded-md bg-gray-50 px-3 py-3 text-sm text-gray-900">
                {value}
            </p>
        </div>
    );
}

function EmptyState({ label }: { label: string }) {
    return <p className="text-sm text-muted-foreground">{label}</p>;
}
