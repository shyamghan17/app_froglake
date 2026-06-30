import {
    EyeTestPrescription,
    EyeExaminationEyeSection,
    MedicalHistorySection,
    MedicineRow,
    RefractionEyeSection,
    VisualAcuityEyeSection,
} from './types';

export type DisplayItem = {
    label: string;
    value: string;
};

type EyeKey = 'right' | 'left';

const STRUCTURED_SECTION_KEYS: Array<keyof EyeTestPrescription> = [
    'complaints',
    'visual_acuity',
    'refraction',
    'eye_examination',
    'intraocular_pressure',
    'medical_history',
    'diagnosis',
    'medicines',
    'glasses_prescription',
    'eye_diagram',
    'examiner_details',
];

const visualAcuityLabels: Record<keyof VisualAcuityEyeSection, string> = {
    distance: 'Distance',
    near: 'Near',
    pinhole: 'Pinhole',
    with_glasses: 'With Glasses',
    without_glasses: 'Without Glasses',
};

const refractionLabels: Record<keyof RefractionEyeSection, string> = {
    sphere: 'SPH',
    cylinder: 'CYL',
    axis: 'Axis',
    vision: 'Vision',
    near_vision: 'Near Vision',
    add: 'ADD',
};

const examinationLabels: Record<keyof EyeExaminationEyeSection, string> = {
    lid: 'Lid',
    conjunctiva: 'Conjunctiva',
    cornea: 'Cornea',
    anterior_chamber: 'Anterior Chamber',
    iris: 'Iris',
    pupil: 'Pupil',
    lens: 'Lens',
    vitreous: 'Vitreous',
    fundus: 'Fundus',
    colour_vision: 'Colour Vision',
};

const medicalHistoryLabels: Record<keyof MedicalHistorySection, string> = {
    tbut: 'TBUT',
    blood_pressure: 'Blood Pressure',
    past_history: 'Past History',
    other_diseases: 'Other Diseases',
};

const lateralityLabels: Record<string, string> = {
    right: 'Right Eye',
    left: 'Left Eye',
    both: 'Both Eyes',
    na: 'Not Specified',
    systemic: 'Systemic',
};

function isMeaningfulValue(value: unknown): boolean {
    if (value == null) {
        return false;
    }

    if (typeof value === 'string') {
        return value.trim().length > 0;
    }

    if (typeof value === 'number') {
        return !Number.isNaN(value);
    }

    if (typeof value === 'boolean') {
        return value;
    }

    if (Array.isArray(value)) {
        return value.some((item) => isMeaningfulValue(item));
    }

    if (typeof value === 'object') {
        return Object.values(value as Record<string, unknown>).some((item) => isMeaningfulValue(item));
    }

    return true;
}

function normalizeText(value: unknown): string {
    if (value == null) {
        return '';
    }

    return String(value).trim();
}

function collapseWhitespace(value: string): string {
    return value.replace(/\s+/g, ' ').trim();
}

function truncate(value: string, maxLength = 110): string {
    if (value.length <= maxLength) {
        return value;
    }

    return `${value.slice(0, maxLength - 1).trimEnd()}...`;
}

function buildPerEyeItems<T extends Record<string, unknown>>(
    section: { right?: T | null; left?: T | null } | null | undefined,
    labels: Record<string, string>,
): Record<EyeKey, DisplayItem[]> {
    const buildItems = (eye: T | null | undefined) => {
        if (!eye || typeof eye !== 'object') {
            return [];
        }

        return Object.entries(labels)
            .filter(([field]) => isMeaningfulValue(eye[field]))
            .map(([field, label]) => ({
                label,
                value: normalizeText(eye[field]),
            }));
    };

    return {
        right: buildItems(section?.right),
        left: buildItems(section?.left),
    };
}

export function hasStructuredClinicalData(prescription: EyeTestPrescription): boolean {
    if ((prescription.clinical_schema_version ?? 0) >= 2) {
        return true;
    }

    return STRUCTURED_SECTION_KEYS.some((key) => isMeaningfulValue(prescription[key]));
}

export function getPrescriptionRecordType(prescription: EyeTestPrescription): 'Structured' | 'Legacy' {
    return hasStructuredClinicalData(prescription) ? 'Structured' : 'Legacy';
}

export function getDoctorDisplayName(prescription: EyeTestPrescription): string {
    if (prescription.doctor?.name) {
        return prescription.doctor.name;
    }

    if (typeof prescription.doctor_name === 'string' && prescription.doctor_name.trim()) {
        return prescription.doctor_name;
    }

    return '-';
}

export function getAffectedEyeLabel(value?: string | null): string {
    if (!value) {
        return '';
    }

    return lateralityLabels[value] ?? value;
}

export function getComplaintSummary(prescription: EyeTestPrescription): string {
    const complaints = prescription.complaints;

    if (!complaints) {
        return '';
    }

    const summaryParts = [
        ...(Array.isArray(complaints.items) ? complaints.items.filter((item) => item?.trim()) : []),
        normalizeText(complaints.custom),
    ].filter(Boolean);

    const summary = summaryParts.join(', ');
    const affectedEye = complaints.affected_eye && complaints.affected_eye !== 'na'
        ? getAffectedEyeLabel(complaints.affected_eye)
        : '';

    return [summary, affectedEye].filter(Boolean).join(' | ');
}

export function getDiagnosisSummary(prescription: EyeTestPrescription): string {
    const diagnosis = prescription.diagnosis;

    if (!diagnosis) {
        return '';
    }

    const secondary = Array.isArray(diagnosis.secondary)
        ? diagnosis.secondary.filter((item) => item?.trim()).join(', ')
        : '';

    return [
        normalizeText(diagnosis.primary),
        secondary,
        normalizeText(diagnosis.summary),
    ].filter(Boolean).join(' | ');
}

function summarizeRefractionEye(eye: RefractionEyeSection | undefined | null): string {
    if (!eye) {
        return '';
    }

    const parts = [
        eye.sphere ? `SPH ${eye.sphere}` : '',
        eye.cylinder ? `CYL ${eye.cylinder}` : '',
        eye.axis ? `Axis ${eye.axis}` : '',
        eye.vision ? `Vision ${eye.vision}` : '',
        eye.near_vision ? `Near ${eye.near_vision}` : '',
        eye.add ? `ADD ${eye.add}` : '',
    ].filter(Boolean);

    return parts.join(', ');
}

export function getGlassesSummary(prescription: EyeTestPrescription): string {
    const section = prescription.glasses_prescription;

    if (!section) {
        return '';
    }

    const right = summarizeRefractionEye(section.right);
    const left = summarizeRefractionEye(section.left);

    return [
        right ? `OD ${right}` : '',
        left ? `OS ${left}` : '',
        normalizeText(section.notes),
    ].filter(Boolean).join(' | ');
}

export function getMedicineCount(prescription: EyeTestPrescription): number {
    if (!Array.isArray(prescription.medicines)) {
        return 0;
    }

    return prescription.medicines.filter((medicine) =>
        isMeaningfulValue(medicine?.medicine) ||
        isMeaningfulValue(medicine?.frequency) ||
        isMeaningfulValue(medicine?.duration) ||
        isMeaningfulValue(medicine?.instructions),
    ).length;
}

export function getLegacyExcerpt(value?: string | null, maxLength = 110): string {
    const normalized = collapseWhitespace(normalizeText(value));

    return normalized ? truncate(normalized, maxLength) : '';
}

export function getClinicalSummaryLines(prescription: EyeTestPrescription): string[] {
    const complaintSummary = getComplaintSummary(prescription);
    const diagnosisSummary = getDiagnosisSummary(prescription);

    if (complaintSummary || diagnosisSummary) {
        return [complaintSummary, diagnosisSummary].filter(Boolean);
    }

    return [
        getLegacyExcerpt(prescription.test_results),
        getLegacyExcerpt(prescription.notes),
    ].filter(Boolean);
}

export function getPrescriptionSummaryLines(prescription: EyeTestPrescription): string[] {
    const glassesSummary = getGlassesSummary(prescription);
    const medicineCount = getMedicineCount(prescription);

    const lines = [
        glassesSummary,
        medicineCount > 0 ? `${medicineCount} medicine${medicineCount === 1 ? '' : 's'} prescribed` : '',
    ].filter(Boolean);

    if (lines.length > 0) {
        return lines;
    }

    return [
        getLegacyExcerpt(prescription.prescription_details),
    ].filter(Boolean);
}

export function getVisualAcuityItems(prescription: EyeTestPrescription): Record<EyeKey, DisplayItem[]> {
    return buildPerEyeItems(prescription.visual_acuity, visualAcuityLabels);
}

export function getRefractionItems(prescription: EyeTestPrescription): Record<EyeKey, DisplayItem[]> {
    return buildPerEyeItems(prescription.refraction, refractionLabels);
}

export function getEyeExaminationItems(prescription: EyeTestPrescription): Record<EyeKey, DisplayItem[]> {
    return buildPerEyeItems(prescription.eye_examination, examinationLabels);
}

export function getGlassesItems(prescription: EyeTestPrescription): Record<EyeKey, DisplayItem[]> {
    return buildPerEyeItems(prescription.glasses_prescription, refractionLabels);
}

export function getMedicalHistoryItems(prescription: EyeTestPrescription): DisplayItem[] {
    const section = prescription.medical_history;

    if (!section) {
        return [];
    }

    return Object.entries(medicalHistoryLabels)
        .map(([field, label]) => {
            const entry = section[field as keyof MedicalHistorySection];

            if (!entry || (!entry.checked && !normalizeText(entry.notes))) {
                return null;
            }

            return {
                label,
                value: [
                    entry.checked ? 'Yes' : '',
                    normalizeText(entry.notes),
                ].filter(Boolean).join(' | '),
            };
        })
        .filter((item): item is DisplayItem => item !== null);
}

export function getDiagnosisItems(prescription: EyeTestPrescription): DisplayItem[] {
    const diagnosis = prescription.diagnosis;

    if (!diagnosis) {
        return [];
    }

    const items: DisplayItem[] = [];

    if (normalizeText(diagnosis.primary)) {
        items.push({ label: 'Primary', value: normalizeText(diagnosis.primary) });
    }

    if (Array.isArray(diagnosis.secondary) && diagnosis.secondary.some((item) => item?.trim())) {
        items.push({
            label: 'Secondary',
            value: diagnosis.secondary.filter((item) => item?.trim()).join(', '),
        });
    }

    if (normalizeText(diagnosis.summary)) {
        items.push({ label: 'Summary', value: normalizeText(diagnosis.summary) });
    }

    return items;
}

export function getMedicineItems(prescription: EyeTestPrescription): string[] {
    if (!Array.isArray(prescription.medicines)) {
        return [];
    }

    return prescription.medicines
        .map((medicine: MedicineRow) => {
            const medicineName = normalizeText(medicine.medicine);

            if (!medicineName) {
                return '';
            }

            return [
                medicineName,
                medicine.eye ? getAffectedEyeLabel(medicine.eye) : '',
                normalizeText(medicine.frequency),
                normalizeText(medicine.duration),
                normalizeText(medicine.instructions),
            ].filter(Boolean).join(' | ');
        })
        .filter(Boolean);
}

export function getEyeDiagramItems(prescription: EyeTestPrescription): DisplayItem[] {
    const diagram = prescription.eye_diagram;

    if (!diagram) {
        return [];
    }

    const items: DisplayItem[] = [];

    if (normalizeText(diagram.image_path)) {
        items.push({ label: 'Reference', value: normalizeText(diagram.image_path) });
    }

    if (Array.isArray(diagram.annotations) && diagram.annotations.some((item) => item?.trim())) {
        items.push({
            label: 'Annotations',
            value: diagram.annotations.filter((item) => item?.trim()).join(', '),
        });
    }

    if (normalizeText(diagram.notes)) {
        items.push({ label: 'Notes', value: normalizeText(diagram.notes) });
    }

    return items;
}

export function getExaminerItems(prescription: EyeTestPrescription): DisplayItem[] {
    const details = prescription.examiner_details;

    if (!details) {
        return [];
    }

    return [
        { label: 'Examiner', value: normalizeText(details.examiner_name) },
        { label: 'Role', value: normalizeText(details.examiner_role) },
        { label: 'License Number', value: normalizeText(details.license_number) },
        { label: 'Signature Reference', value: normalizeText(details.signature_path) },
    ].filter((item) => item.value);
}

export function getIntraocularPressureItems(prescription: EyeTestPrescription): DisplayItem[] {
    const section = prescription.intraocular_pressure;

    if (!section) {
        return [];
    }

    const unit = normalizeText(section.unit) || 'mmHg';

    return [
        section.right ? { label: 'Right Eye', value: `${section.right} ${unit}` } : null,
        section.left ? { label: 'Left Eye', value: `${section.left} ${unit}` } : null,
    ].filter((item): item is DisplayItem => item !== null);
}
