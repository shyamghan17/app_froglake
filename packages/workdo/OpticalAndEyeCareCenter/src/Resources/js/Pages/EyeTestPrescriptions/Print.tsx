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
    const clinicName = getCompanySetting('company_name') || t('Eye Care Clinic');
    const clinicSubtitle = getCompanySetting('company_tagline_en') || getCompanySetting('company_tagline') || t('Eye Test Prescription');
    const logoPath = getCompanySetting('logo_dark') || getCompanySetting('logo_light') || '';
    const logoSrc = logoPath ? getImagePath(logoPath) : '';
    const clinicAddressParts = [
        getCompanySetting('company_address'),
        getCompanySetting('company_city'),
        getCompanySetting('company_state'),
        getCompanySetting('company_zipcode'),
        getCompanySetting('company_country'),
    ].filter(Boolean);
    const clinicContactParts = [
        getCompanySetting('company_telephone'),
        getCompanySetting('company_email'),
        getCompanySetting('registration_number'),
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
        { label: t('Age'), value: patientAge || '-' },
        { label: t('Sex'), value: formatGender(eyetestprescription.patient?.gender) },
        { label: t('Date of Birth'), value: eyetestprescription.patient?.dob ? formatDate(eyetestprescription.patient.dob) : '-' },
        { label: t('Contact No'), value: eyetestprescription.patient?.contact_no || '-' },
        { label: t('Address'), value: eyetestprescription.patient?.address || '-' },
    ];

    const doctorDetails: DisplayItem[] = [
        { label: t('Doctor Name'), value: doctorDisplayName },
        { label: t('Test Date'), value: eyetestprescription.test_date ? formatDate(eyetestprescription.test_date) : '-' },
        { label: t('Follow Up Date'), value: eyetestprescription.follow_up_date ? formatDate(eyetestprescription.follow_up_date) : '-' },
        { label: t('Prescription Expiry Date'), value: eyetestprescription.prescription_expiry_date ? formatDate(eyetestprescription.prescription_expiry_date) : '-' },
    ];

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

            <div className="prescription-print-container mx-auto max-w-5xl bg-white px-8 py-8 shadow-sm print:max-w-none print:shadow-none">
                <div className="mb-6 overflow-hidden rounded-xl border border-sky-300">
                    <div className="relative border-b border-sky-300 bg-sky-50 px-6 py-5 text-center">
                        <div className="mx-auto max-w-3xl">
                            <h2 className="text-3xl font-extrabold tracking-wide text-sky-900">
                                {clinicName}
                            </h2>
                            <p className="mt-2 text-sm font-semibold uppercase tracking-[0.24em] text-sky-700">
                                {clinicSubtitle}
                            </p>
                            {clinicAddressParts.length > 0 && (
                                <p className="mt-2 text-sm font-medium text-slate-700">
                                    {clinicAddressParts.join(', ')}
                                </p>
                            )}
                        </div>
                        <div className="absolute right-6 top-5 flex h-20 w-20 items-center justify-center">
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
                    </div>

                    {clinicContactParts.length > 0 && (
                        <div className="grid grid-cols-1 gap-2 border-b border-sky-300 bg-white px-6 py-3 text-sm text-slate-700 md:grid-cols-3">
                            {getCompanySetting('company_telephone') && (
                                <div><strong>{t('Contact')}:</strong> {getCompanySetting('company_telephone')}</div>
                            )}
                            {getCompanySetting('company_email') && (
                                <div className="md:text-center"><strong>{t('Email')}:</strong> {getCompanySetting('company_email')}</div>
                            )}
                            {getCompanySetting('registration_number') && (
                                <div className="md:text-right"><strong>{t('Registration')}:</strong> {getCompanySetting('registration_number')}</div>
                            )}
                        </div>
                    )}

                    <div className="grid grid-cols-1 border-b border-sky-300 px-6 py-3 text-sm text-slate-700 md:grid-cols-[1.4fr_0.8fr_0.8fr]">
                        <div className="border-sky-200 md:border-r md:pr-4">
                            <strong>{t('Name')}:</strong> {eyetestprescription.patient?.patient_name || '-'}
                        </div>
                        <div className="border-sky-200 md:border-r md:px-4">
                            <strong>{t('Age/Sex')}:</strong> {[patientAge, formatGender(eyetestprescription.patient?.gender)].filter(Boolean).join(' / ') || '-'}
                        </div>
                        <div className="md:pl-4 md:text-right">
                            <strong>{t('Date')}:</strong> {eyetestprescription.test_date ? formatDate(eyetestprescription.test_date) : '-'}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 px-6 py-3 text-sm text-slate-700 md:grid-cols-[1.8fr_1fr]">
                        <div className="border-sky-200 md:border-r md:pr-4">
                            <strong>{t('Address')}:</strong> {eyetestprescription.patient?.address || '-'}
                        </div>
                        <div className="md:pl-4 md:text-right">
                            <strong>{t('Contact No')}:</strong> {eyetestprescription.patient?.contact_no || '-'}
                        </div>
                    </div>
                </div>

                <div className="mb-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
                    <PaperSection title={t('Patient Details')}>
                        <DetailGrid items={patientDetails} columns={2} />
                    </PaperSection>

                    <PaperSection title={t('Doctor Details')}>
                        <DetailGrid items={doctorDetails} columns={2} />
                    </PaperSection>
                </div>

                <div className="mb-6">
                    <PaperSection title={t('Chief Complaints')}>
                        {complaintSummary ? (
                            <p className="text-sm leading-7 text-slate-800">{complaintSummary}</p>
                        ) : (
                            <EmptyState label={t('No complaints recorded.')} />
                        )}
                    </PaperSection>
                </div>

                {hasStructuredData ? (
                    <>
                        <div className="grid gap-6 xl:grid-cols-[0.95fr_1.4fr]">
                            <div className="space-y-6">
                                <PaperSection title={t('Visual Acuity')}>
                                    <PerEyeGrid items={visualAcuityItems} emptyLabel={t('No visual acuity data recorded.')} />
                                </PaperSection>

                                <PaperSection title={t('Intraocular Pressure')}>
                                    {intraocularPressureItems.length > 0 ? (
                                        <DetailGrid items={intraocularPressureItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No IOP data recorded.')} />
                                    )}
                                </PaperSection>

                                <PaperSection title={t('Medical History')}>
                                    {medicalHistoryItems.length > 0 ? (
                                        <DetailGrid items={medicalHistoryItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No medical history recorded.')} />
                                    )}
                                </PaperSection>
                            </div>

                            <div className="space-y-6">
                                <PaperSection title={t('Eye Examination')}>
                                    <PerEyeGrid items={eyeExaminationItems} emptyLabel={t('No eye examination data recorded.')} />
                                </PaperSection>

                                <PaperSection title={t('Diagnosis')}>
                                    {diagnosisItems.length > 0 ? (
                                        <DetailGrid items={diagnosisItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No structured diagnosis recorded.')} />
                                    )}
                                </PaperSection>

                                <PaperSection title={t('Medicines')}>
                                    {medicineItems.length > 0 ? (
                                        <ul className="space-y-2 text-sm text-slate-800">
                                            {medicineItems.map((item) => (
                                                <li key={item} className="border-b border-sky-100 px-1 pb-2">
                                                    {item}
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        <EmptyState label={t('No medicines recorded.')} />
                                    )}
                                </PaperSection>
                            </div>
                        </div>

                        <div className="mt-6 grid gap-6">
                            <PaperSection title={t('Refraction')}>
                                <PerEyeGrid items={refractionItems} emptyLabel={t('No refraction data recorded.')} />
                            </PaperSection>

                            <PaperSection title={t('Glasses Prescription')}>
                                <PerEyeGrid items={glassesItems} emptyLabel={t('No glasses prescription recorded.')} />
                            </PaperSection>

                            <div className="grid gap-6 xl:grid-cols-2">
                                <PaperSection title={t('Eye Diagram')}>
                                    {eyeDiagramItems.length > 0 ? (
                                        <DetailGrid items={eyeDiagramItems} columns={2} />
                                    ) : (
                                        <EmptyState label={t('No eye diagram notes recorded.')} />
                                    )}
                                </PaperSection>

                                <PaperSection title={t('Clinical Notes')}>
                                    {eyetestprescription.notes ? (
                                        <p className="whitespace-pre-wrap text-sm leading-7 text-slate-800">
                                            {eyetestprescription.notes}
                                        </p>
                                    ) : (
                                        <EmptyState label={t('No clinical notes recorded.')} />
                                    )}
                                </PaperSection>
                            </div>
                        </div>
                    </>
                ) : (
                    <PaperSection title={t('Legacy Clinical Record')}>
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
                    </PaperSection>
                )}
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

function PaperSection({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <section className="break-inside-avoid overflow-hidden rounded-lg border border-sky-200 bg-white">
            <div className="border-b border-sky-200 bg-sky-50 px-4 py-2">
                <h3 className="text-sm font-semibold uppercase tracking-[0.22em] text-sky-800">{title}</h3>
            </div>
            <div className="p-4">
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
        <div className={`grid grid-cols-1 gap-4 ${columnClassName}`}>
            {items.map((item) => (
                <div key={`${item.label}-${item.value}`} className="rounded-md border border-sky-100 bg-slate-50/50 px-4 py-3">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">{item.label}</p>
                    <p className="mt-1 text-sm text-slate-900">{item.value || '-'}</p>
                </div>
            ))}
        </div>
    );
}

function PerEyeGrid({
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
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <EyePanel title="Right Eye" items={items.right} />
            <EyePanel title="Left Eye" items={items.left} />
        </div>
    );
}

function EyePanel({
    title,
    items,
}: {
    title: string;
    items: DisplayItem[];
}) {
    return (
        <div className="overflow-hidden rounded-md border border-sky-100">
            <div className="border-b border-sky-100 bg-slate-50 px-4 py-2">
                <p className="text-sm font-semibold text-slate-900">{title}</p>
            </div>
            {items.length > 0 ? (
                <div className="space-y-0">
                    {items.map((item) => (
                        <div key={`${title}-${item.label}-${item.value}`} className="grid grid-cols-[0.95fr_1.25fr] border-b border-sky-50 px-4 py-2 last:border-b-0">
                            <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">{item.label}</p>
                            <p className="text-sm text-slate-900">{item.value}</p>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="px-4 py-3 text-sm text-slate-500">-</p>
            )}
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
            <p className="text-sm font-semibold text-slate-700">{title}</p>
            <p className="whitespace-pre-wrap rounded-md border border-sky-100 bg-slate-50/50 px-4 py-3 text-sm text-slate-900">{value}</p>
        </div>
    );
}

function EmptyState({ label }: { label: string }) {
    return <p className="text-sm text-slate-500">{label}</p>;
}
