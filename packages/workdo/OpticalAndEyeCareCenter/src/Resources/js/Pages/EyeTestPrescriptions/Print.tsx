import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useEffect, useState } from 'react';
import html2pdf from 'html2pdf.js';
import { Button } from '@/components/ui/button';
import { formatDate, getCompanySetting, getImagePath } from '@/utils/helpers';
import {
    DisplayItem,
    getComplaintSummary,
    getDiagnosisItems,
    getDoctorDisplayName,
    getEyeDiagramItems,
    getEyeExaminationItems,
    getGlassesItems,
    getIntraocularPressureItems,
    getMedicalHistoryItems,
    getMedicineItems,
    getRefractionItems,
    getVisualAcuityItems,
    hasStructuredClinicalData,
} from './presentation';
import { EyeTestPrescription, EyeTestPrescriptionShowProps } from './types';

interface PrintPageProps extends EyeTestPrescriptionShowProps {
    eyetestprescription: EyeTestPrescription;
}

export default function Print() {
    const { t } = useTranslation();
    const { eyetestprescription } = usePage<PrintPageProps>().props;
    const [isDownloading, setIsDownloading] = useState(false);
    const [logoLoadFailed, setLogoLoadFailed] = useState(false);
    const hasStructuredData = hasStructuredClinicalData(eyetestprescription);
    const doctorDisplayName = getDoctorDisplayName(eyetestprescription);
    const diagnosisItems = getDiagnosisItems(eyetestprescription);
    const visualAcuityItems = getVisualAcuityItems(eyetestprescription);
    const refractionItems = getRefractionItems(eyetestprescription);
    const eyeExaminationItems = getEyeExaminationItems(eyetestprescription);
    const intraocularPressureItems = getIntraocularPressureItems(eyetestprescription);
    const medicalHistoryItems = getMedicalHistoryItems(eyetestprescription);
    const medicineItems = getMedicineItems(eyetestprescription);
    const glassesItems = getGlassesItems(eyetestprescription);
    const eyeDiagramItems = getEyeDiagramItems(eyetestprescription);
    const patientAge = getAgeFromDob(eyetestprescription.patient?.dob);
    const clinicName = getCompanySetting('company_name') || '';
    const logoPath = getCompanySetting('logo_dark') || getCompanySetting('logo_light') || '';
    const logoSrc = logoPath ? getImagePath(logoPath) : '';
    const clinicAddressParts = [
        getCompanySetting('company_address'),
        getCompanySetting('company_city'),
        getCompanySetting('company_state'),
        getCompanySetting('company_zipcode'),
        getCompanySetting('company_country'),
    ].filter(Boolean);
    const complaintSummary = getComplaintSummary(eyetestprescription);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('download') === 'pdf') {
            const timeout = window.setTimeout(() => {
                void downloadPDF();
            }, 700);

            return () => window.clearTimeout(timeout);
        }
    }, []);

    const downloadPDF = async () => {
        setIsDownloading(true);

        const exportContent = document.querySelector('.prescription-print-container');

        if (exportContent) {
            const patientName = eyetestprescription.patient?.patient_name?.trim() || 'patient';
            const safePatientName = patientName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'patient';
            const formattedFileDate = formatFileDate(eyetestprescription.test_date);

            const options = {
                margin: 0.25,
                filename: `${safePatientName}-${formattedFileDate}.pdf`,
                image: { type: 'jpeg' as const, quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' as const },
            };

            try {
                await html2pdf().set(options).from(exportContent as HTMLElement).save();
                window.setTimeout(() => window.close(), 700);
            } catch (error) {
                console.error('PDF export failed:', error);
            }
        }

        setIsDownloading(false);
    };

    const patientDetails: DisplayItem[] = [
        { label: t('Patient Name'), value: eyetestprescription.patient?.patient_name || '-' },
        { label: t('Patient ID'), value: String(eyetestprescription.patient_id || '-') },
        { label: t('Age'), value: patientAge || '-' },
        { label: t('Sex'), value: formatGender(eyetestprescription.patient?.gender) },
        { label: t('Date of Birth'), value: eyetestprescription.patient?.dob ? formatDate(eyetestprescription.patient.dob) : '-' },
        { label: t('Contact No'), value: eyetestprescription.patient?.contact_no || '-' },
        { label: t('Address'), value: eyetestprescription.patient?.address || '-' },
        { label: t('Test Date'), value: eyetestprescription.test_date ? formatDate(eyetestprescription.test_date) : '-' },
        { label: t('Follow Up Date'), value: eyetestprescription.follow_up_date ? formatDate(eyetestprescription.follow_up_date) : '-' },
        { label: t('Prescription Expiry Date'), value: eyetestprescription.prescription_expiry_date ? formatDate(eyetestprescription.prescription_expiry_date) : '-' },
    ];
    const signaturePath = eyetestprescription.examiner_details?.signature_path || '';
    const signatureSrc = signaturePath ? getImagePath(signaturePath) : '';

    return (
        <div className="min-h-screen bg-slate-100 print:bg-white">
            <Head title={t('Export Eye Test Prescription PDF')} />

            {isDownloading && (
                <div className="print-hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="rounded-lg bg-white p-6 shadow-lg">
                        <div className="flex items-center space-x-3">
                            <div className="h-6 w-6 animate-spin rounded-full border-b-2 border-blue-600"></div>
                            <p className="text-lg font-semibold text-slate-700">{t('Generating PDF...')}</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="print-hidden sticky top-0 z-10 border-b bg-white/95 backdrop-blur">
                <div className="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                    <div>
                        <h1 className="text-lg font-semibold text-slate-900">{t('Eye Test Prescription')}</h1>
                        <p className="text-sm text-slate-500">{eyetestprescription.patient?.patient_name || '-'}</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <Button variant="outline" onClick={() => window.close()}>
                            {t('Close')}
                        </Button>
                        <Button onClick={() => void downloadPDF()} disabled={isDownloading}>
                            {isDownloading ? t('Generating PDF...') : t('Export PDF')}
                        </Button>
                    </div>
                </div>
            </div>

            <div className="prescription-print-container mx-auto max-w-5xl bg-white px-8 py-8 text-[11px] leading-[1.45] print:max-w-none">
                <div className="mb-4 pb-2">
                    <div className="relative px-6 py-6">
                        <div className="absolute right-6 top-6 flex h-20 w-20 items-center justify-center rounded-full bg-sky-700/5">
                            {logoSrc && !logoLoadFailed ? (
                                <img
                                    src={logoSrc}
                                    alt={clinicName}
                                    className="max-h-20 w-auto object-contain"
                                    onError={() => setLogoLoadFailed(true)}
                                />
                            ) : (
                                <LogoFallback title={clinicName} />
                            )}
                        </div>

                        <div className="mx-auto max-w-3xl text-center">
                            <h2 className="pr-24 text-[13px] font-bold leading-tight text-slate-950 md:pr-0">
                                {clinicName || t('Company Name')}
                            </h2>
                            {clinicAddressParts.length > 0 && (
                                <p className="mt-2 text-[11px] font-normal text-slate-600">
                                    {clinicAddressParts.join(', ')}
                                </p>
                            )}
                            <div className="mt-2 flex flex-wrap items-center justify-center gap-x-5 gap-y-1 text-[11px] text-slate-700">
                                {getCompanySetting('company_email') && (
                                    <p className="break-all"><strong>{t('Email')}:</strong> {getCompanySetting('company_email')}</p>
                                )}
                                {getCompanySetting('company_telephone') && (
                                    <p className="break-words"><strong>{t('Phone')}:</strong> {getCompanySetting('company_telephone')}</p>
                                )}
                            </div>
                        </div>
                    </div>

                </div>

                <div className="mb-3">
                    <ReportSection title={t('Patient Details')}>
                        <DetailGrid items={patientDetails} columns={3} />
                    </ReportSection>
                </div>

                <div className="mb-3 border-b border-slate-300 pb-2 text-center">
                    <h3 className="text-[12px] font-bold uppercase leading-tight tracking-[0.12em] text-slate-950">
                        {t('Eye Test Prescription')}
                    </h3>
                </div>

                <div className="mb-4">
                    <ReportSection title={t('Complaints')}>
                        {complaintSummary ? (
                            <p className="text-[11px] leading-[1.5] text-slate-800">{complaintSummary}</p>
                        ) : (
                            <EmptyState label={t('No complaints recorded.')} />
                        )}
                    </ReportSection>
                </div>

                {hasStructuredData ? (
                    <>
                        <div className="grid gap-4 xl:grid-cols-[0.95fr_1.4fr]">
                            <div className="space-y-4">
                                <ReportSection title={t('Visual Acuity')}>
                                    <PerEyeTable items={visualAcuityItems} emptyLabel={t('No visual acuity data recorded.')} />
                                </ReportSection>

                                <ReportSection title={t('Intraocular Pressure')}>
                                    {intraocularPressureItems.length > 0 ? (
                                        <DetailGrid items={intraocularPressureItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No IOP data recorded.')} />
                                    )}
                                </ReportSection>

                                <ReportSection title={t('Medical History')}>
                                    {medicalHistoryItems.length > 0 ? (
                                        <DetailGrid items={medicalHistoryItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No medical history recorded.')} />
                                    )}
                                </ReportSection>
                            </div>

                            <div className="space-y-4">
                                <ReportSection title={t('Eye Examination')}>
                                    <PerEyeTable items={eyeExaminationItems} emptyLabel={t('No eye examination data recorded.')} />
                                </ReportSection>

                                <ReportSection title={t('Diagnosis')}>
                                    {diagnosisItems.length > 0 ? (
                                        <DetailGrid items={diagnosisItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No structured diagnosis recorded.')} />
                                    )}
                                </ReportSection>

                                <ReportSection title={t('Medicines')}>
                                    {medicineItems.length > 0 ? (
                                        <ul className="space-y-0 text-sm text-slate-800">
                                            {medicineItems.map((item) => (
                                                <li key={item} className="px-1 py-1.5">
                                                    {item}
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        <EmptyState label={t('No medicines recorded.')} />
                                    )}
                                </ReportSection>
                            </div>
                        </div>

                        <div className="mt-4 grid gap-4">
                            <ReportSection title={t('Refraction')}>
                                <PerEyeTable items={refractionItems} emptyLabel={t('No refraction data recorded.')} />
                            </ReportSection>

                            <ReportSection title={t('Glasses Prescription')}>
                                <PerEyeTable items={glassesItems} emptyLabel={t('No glasses prescription recorded.')} />
                            </ReportSection>

                            <div className="grid gap-4 xl:grid-cols-2">
                                <ReportSection title={t('Eye Diagram')}>
                                    {eyeDiagramItems.length > 0 ? (
                                        <DetailGrid items={eyeDiagramItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No eye diagram notes recorded.')} />
                                    )}
                                </ReportSection>

                                <ReportSection title={t('Clinical Notes')}>
                                    {eyetestprescription.notes ? (
                                        <p className="whitespace-pre-wrap text-sm leading-7 text-slate-800">
                                            {eyetestprescription.notes}
                                        </p>
                                    ) : (
                                        <EmptyState label={t('No clinical notes recorded.')} />
                                    )}
                                </ReportSection>
                            </div>
                        </div>
                    </>
                ) : (
                    <ReportSection title={t('Legacy Clinical Record')}>
                        <div className="space-y-4">
                            {eyetestprescription.test_results && (
                                <TextBlock title={t('Test Results')} value={eyetestprescription.test_results} />
                            )}
                            {eyetestprescription.prescription_details && (
                                <TextBlock title={t('Prescription Details')} value={eyetestprescription.prescription_details} />
                            )}
                            {eyetestprescription.notes && (
                                <TextBlock title={t('Notes')} value={eyetestprescription.notes} />
                            )}
                        </div>
                    </ReportSection>
                )}

                <div className="mt-5 grid gap-4 pt-3 md:grid-cols-[1fr_220px]">
                    <div className="text-xs text-slate-600">
                        <p>{t('Generated on')}: {formatDate(new Date().toISOString())}</p>
                        <p className="mt-1">{t('Page 1 of 1')}</p>
                    </div>

                    <div className="text-center">
                        <div className="flex h-16 items-end justify-center">
                            {signatureSrc ? (
                                <img
                                    src={signatureSrc}
                                    alt={t('Doctor Signature')}
                                    className="max-h-14 max-w-[180px] object-contain"
                                />
                            ) : (
                                <div className="w-full border-b border-slate-400" />
                            )}
                        </div>
                        <p className="mt-2 text-[11px] font-bold text-slate-900">{doctorDisplayName || '-'}</p>
                        <p className="text-[10px] uppercase tracking-[0.14em] text-slate-500">{t('Doctor Signature')}</p>
                    </div>
                </div>
            </div>

            <style>{`
                @page {
                    size: A4;
                    margin: 0.5in;
                }

                body {
                    -webkit-print-color-adjust: exact;
                    color-adjust: exact;
                }
                @media print {
                    body {
                        background: white;
                    }

                    .print-hidden {
                        display: none !important;
                    }

                    .prescription-print-container {
                        padding: 0;
                    }
                }
            `}</style>
        </div>
    );
}

function getAgeFromDob(dob?: string | null): string {
    if (!dob) {
        return '';
    }

    const dateOfBirth = new Date(dob);

    if (Number.isNaN(dateOfBirth.getTime())) {
        return '';
    }

    const today = new Date();
    let age = today.getFullYear() - dateOfBirth.getFullYear();
    const hasHadBirthdayThisYear = (
        today.getMonth() > dateOfBirth.getMonth()
        || (today.getMonth() === dateOfBirth.getMonth() && today.getDate() >= dateOfBirth.getDate())
    );

    if (!hasHadBirthdayThisYear) {
        age -= 1;
    }

    return age >= 0 ? String(age) : '';
}

function formatFileDate(dateValue?: string | null): string {
    if (!dateValue) {
        return 'date';
    }

    const normalizedDate = new Date(dateValue);

    if (Number.isNaN(normalizedDate.getTime())) {
        return 'date';
    }

    const day = String(normalizedDate.getDate()).padStart(2, '0');
    const month = String(normalizedDate.getMonth() + 1).padStart(2, '0');
    const year = String(normalizedDate.getFullYear()).slice(-2);

    return `${day}-${month}-${year}`;
}

function formatGender(gender?: string | number | null): string {
    if (gender === null || gender === undefined || gender === '') {
        return '-';
    }

    const normalized = String(gender).trim().toLowerCase();

    if (normalized === '1' || normalized === 'male' || normalized === 'm') {
        return 'Male';
    }

    if (normalized === '0' || normalized === 'female' || normalized === 'f') {
        return 'Female';
    }

    if (normalized === '2' || normalized === 'other') {
        return 'Other';
    }

    return String(gender);
}

function LogoFallback({ title }: { title: string }) {
    const initials = title
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || '')
        .join('');

    return (
        <div className="flex h-20 w-20 items-center justify-center rounded-full border-2 border-sky-300 bg-white text-lg font-bold tracking-[0.25em] text-sky-700">
            {initials || 'EC'}
        </div>
    );
}

function ReportSection({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <section className="break-inside-avoid">
            <div className="px-0 py-2">
                <h3 className="text-[11px] font-bold text-slate-900">{title}</h3>
            </div>
            <div className="py-3">
                {children}
            </div>
        </section>
    );
}

function DetailGrid({
    items,
    columns = 2,
}: {
    items: DisplayItem[];
    columns?: 2 | 3;
}) {
    const columnClassName = columns === 3 ? 'md:grid-cols-3' : 'md:grid-cols-2';

    return (
        <div className={`grid grid-cols-1 gap-x-6 gap-y-0 ${columnClassName}`}>
            {items.map((item) => (
                <div key={`${item.label}-${item.value}`} className="py-2">
                    <p className="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-600">{item.label}</p>
                    <p className="mt-0.5 text-[11px] text-slate-900">{item.value || '-'}</p>
                </div>
            ))}
        </div>
    );
}

function PerEyeTable({
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

    const allLabels = Array.from(
        new Set([
            ...items.right.map((item) => item.label),
            ...items.left.map((item) => item.label),
        ]),
    );
    const rightLookup = new Map(items.right.map((item) => [item.label, item.value]));
    const leftLookup = new Map(items.left.map((item) => [item.label, item.value]));

    return (
        <div className="overflow-hidden">
            <div className="grid grid-cols-[1.1fr_1fr_1fr] text-[11px] font-bold text-slate-900">
                <div className="px-3 py-2">{'Investigation'}</div>
                <div className="px-3 py-2 text-center">{'Right Eye'}</div>
                <div className="px-3 py-2 text-center">{'Left Eye'}</div>
            </div>
            {allLabels.map((label) => (
                <div key={label} className="grid grid-cols-[1.1fr_1fr_1fr] text-[11px] text-slate-800">
                    <div className="px-3 py-1.5 font-medium text-slate-700">{label}</div>
                    <div className="px-3 py-1.5 text-center">{rightLookup.get(label) || '-'}</div>
                    <div className="px-3 py-1.5 text-center">{leftLookup.get(label) || '-'}</div>
                </div>
            ))}
        </div>
    );
}

function TextBlock({
    title,
    value,
}: {
    title: string;
    value: string;
}) {
    return (
        <div className="space-y-2">
            <p className="text-[11px] font-bold text-slate-700">{title}</p>
            <p className="whitespace-pre-wrap text-[11px] text-slate-900">{value}</p>
        </div>
    );
}

function EmptyState({ label }: { label: string }) {
    return <p className="text-[11px] text-slate-500">{label}</p>;
}
