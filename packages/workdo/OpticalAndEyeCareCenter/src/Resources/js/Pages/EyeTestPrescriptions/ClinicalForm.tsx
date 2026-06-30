import type { ChangeEvent, FormEvent, ReactNode } from 'react';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { DatePicker } from '@/components/ui/date-picker';
import { useTranslation } from 'react-i18next';
import {
    CreateEyeTestPrescriptionFormData,
    DiagnosisSection,
    EditEyeTestPrescriptionFormData,
    EyeDiagramSection,
    EyeExaminationEyeSection,
    EyePatient,
    EyeTestPrescription,
    EyeTestPrescriptionFormData,
    MedicalHistorySection,
    MedicineRow,
    OpticalDoctor,
    RefractionEyeSection,
    VisualAcuityEyeSection,
} from './types';

type FormData = CreateEyeTestPrescriptionFormData | EditEyeTestPrescriptionFormData;

type ClinicalFormProps = {
    title: string;
    submitLabel: string;
    submittingLabel: string;
    data: FormData;
    setData: (updater: FormData | ((previousData: FormData) => FormData)) => void;
    errors: Record<string, string>;
    processing: boolean;
    eyepatients: EyePatient[];
    opticaldoctors: OpticalDoctor[];
    onCancel: () => void;
    onSubmit: (event: FormEvent<HTMLFormElement>) => void;
};

const complaintOptions = [
    'Blurred vision',
    'Headache',
    'Eye pain',
    'Watering',
    'Redness',
    'Itching',
    'Discharge',
    'Foreign body sensation',
];

const examinationRows: Array<keyof EyeExaminationEyeSection> = [
    'lid',
    'conjunctiva',
    'cornea',
    'anterior_chamber',
    'iris',
    'pupil',
    'lens',
    'vitreous',
    'fundus',
    'colour_vision',
];

const medicalHistoryRows: Array<keyof MedicalHistorySection> = [
    'tbut',
    'blood_pressure',
    'past_history',
    'other_diseases',
];

const refractionFields: Array<keyof RefractionEyeSection> = [
    'sphere',
    'cylinder',
    'axis',
    'vision',
    'near_vision',
    'add',
];

const visualAcuityFields: Array<keyof VisualAcuityEyeSection> = [
    'distance',
    'near',
    'pinhole',
    'with_glasses',
    'without_glasses',
];

const emptyVisualAcuityEye = (): VisualAcuityEyeSection => ({
    distance: '',
    near: '',
    pinhole: '',
    with_glasses: '',
    without_glasses: '',
});

const emptyRefractionEye = (): RefractionEyeSection => ({
    sphere: '',
    cylinder: '',
    axis: '',
    vision: '',
    near_vision: '',
    add: '',
});

const emptyExaminationEye = (): EyeExaminationEyeSection => ({
    lid: '',
    conjunctiva: '',
    cornea: '',
    anterior_chamber: '',
    iris: '',
    pupil: '',
    lens: '',
    vitreous: '',
    fundus: '',
    colour_vision: '',
});

const emptyMedicine = (): MedicineRow => ({
    medicine: '',
    eye: '',
    frequency: '',
    duration: '',
    instructions: '',
});

const emptyMedicalHistoryEntry = () => ({
    checked: false,
    notes: '',
});

const emptyMedicalHistory = (): MedicalHistorySection => ({
    tbut: emptyMedicalHistoryEntry(),
    blood_pressure: emptyMedicalHistoryEntry(),
    past_history: emptyMedicalHistoryEntry(),
    other_diseases: emptyMedicalHistoryEntry(),
});

const normalizeString = (value: unknown) => (typeof value === 'string' ? value : value == null ? '' : String(value));

const normalizeVisualAcuityEye = (value: unknown): VisualAcuityEyeSection => ({
    ...emptyVisualAcuityEye(),
    ...(typeof value === 'object' && value !== null ? value as Partial<VisualAcuityEyeSection> : {}),
});

const normalizeRefractionEye = (value: unknown): RefractionEyeSection => ({
    ...emptyRefractionEye(),
    ...(typeof value === 'object' && value !== null ? value as Partial<RefractionEyeSection> : {}),
});

const normalizeExaminationEye = (value: unknown): EyeExaminationEyeSection => ({
    ...emptyExaminationEye(),
    ...(typeof value === 'object' && value !== null ? value as Partial<EyeExaminationEyeSection> : {}),
});

const normalizeMedicalHistory = (value: unknown): MedicalHistorySection => ({
    ...emptyMedicalHistory(),
    ...(typeof value === 'object' && value !== null ? value as Partial<MedicalHistorySection> : {}),
    tbut: {
        ...emptyMedicalHistoryEntry(),
        ...(typeof value === 'object' && value !== null && 'tbut' in value ? (value as MedicalHistorySection).tbut : {}),
    },
    blood_pressure: {
        ...emptyMedicalHistoryEntry(),
        ...(typeof value === 'object' && value !== null && 'blood_pressure' in value ? (value as MedicalHistorySection).blood_pressure : {}),
    },
    past_history: {
        ...emptyMedicalHistoryEntry(),
        ...(typeof value === 'object' && value !== null && 'past_history' in value ? (value as MedicalHistorySection).past_history : {}),
    },
    other_diseases: {
        ...emptyMedicalHistoryEntry(),
        ...(typeof value === 'object' && value !== null && 'other_diseases' in value ? (value as MedicalHistorySection).other_diseases : {}),
    },
});

const normalizeDiagnosis = (value: unknown): DiagnosisSection => {
    const diagnosis = typeof value === 'object' && value !== null ? value as Partial<DiagnosisSection> : {};

    return {
        primary: normalizeString(diagnosis.primary),
        secondary: Array.isArray(diagnosis.secondary) && diagnosis.secondary.length > 0
            ? diagnosis.secondary.map((item) => normalizeString(item))
            : [''],
        summary: normalizeString(diagnosis.summary),
    };
};

const normalizeEyeDiagram = (value: unknown): EyeDiagramSection => {
    const diagram = typeof value === 'object' && value !== null ? value as Partial<EyeDiagramSection> : {};

    return {
        image_path: normalizeString(diagram.image_path),
        annotations: Array.isArray(diagram.annotations) && diagram.annotations.length > 0
            ? diagram.annotations.map((item) => normalizeString(item))
            : [''],
        notes: normalizeString(diagram.notes),
    };
};

const normalizeMedicines = (value: unknown): MedicineRow[] => {
    if (!Array.isArray(value) || value.length === 0) {
        return [emptyMedicine()];
    }

    return value.map((item) => ({
        ...emptyMedicine(),
        ...(typeof item === 'object' && item !== null ? item as Partial<MedicineRow> : {}),
    }));
};

export const createEyeTestPrescriptionFormData = (
    eyetestprescription?: Partial<EyeTestPrescription>,
): EyeTestPrescriptionFormData => ({
    patient_id: normalizeString(eyetestprescription?.patient_id),
    doctor_name: normalizeString(eyetestprescription?.doctor_name),
    test_date: normalizeString(eyetestprescription?.test_date),
    follow_up_date: normalizeString(eyetestprescription?.follow_up_date),
    prescription_expiry_date: normalizeString(eyetestprescription?.prescription_expiry_date),
    clinical_schema_version: eyetestprescription?.clinical_schema_version ?? 2,
    complaints: {
        items: Array.isArray(eyetestprescription?.complaints?.items) ? eyetestprescription!.complaints!.items : [],
        custom: normalizeString(eyetestprescription?.complaints?.custom),
        affected_eye: eyetestprescription?.complaints?.affected_eye ?? 'na',
    },
    visual_acuity: {
        right: normalizeVisualAcuityEye(eyetestprescription?.visual_acuity?.right),
        left: normalizeVisualAcuityEye(eyetestprescription?.visual_acuity?.left),
    },
    refraction: {
        right: normalizeRefractionEye(eyetestprescription?.refraction?.right),
        left: normalizeRefractionEye(eyetestprescription?.refraction?.left),
    },
    eye_examination: {
        right: normalizeExaminationEye(eyetestprescription?.eye_examination?.right),
        left: normalizeExaminationEye(eyetestprescription?.eye_examination?.left),
    },
    intraocular_pressure: {
        right: normalizeString(eyetestprescription?.intraocular_pressure?.right),
        left: normalizeString(eyetestprescription?.intraocular_pressure?.left),
        unit: normalizeString(eyetestprescription?.intraocular_pressure?.unit) || 'mmHg',
    },
    medical_history: normalizeMedicalHistory(eyetestprescription?.medical_history),
    diagnosis: normalizeDiagnosis(eyetestprescription?.diagnosis),
    medicines: normalizeMedicines(eyetestprescription?.medicines),
    glasses_prescription: {
        right: normalizeRefractionEye(eyetestprescription?.glasses_prescription?.right),
        left: normalizeRefractionEye(eyetestprescription?.glasses_prescription?.left),
        notes: normalizeString(eyetestprescription?.glasses_prescription?.notes),
    },
    eye_diagram: normalizeEyeDiagram(eyetestprescription?.eye_diagram),
    examiner_details: {
        examiner_name: '',
        examiner_role: '',
        license_number: '',
        signature_path: '',
        examiner_user_id: '',
    },
    test_results: normalizeString(eyetestprescription?.test_results),
    prescription_details: normalizeString(eyetestprescription?.prescription_details),
    notes: normalizeString(eyetestprescription?.notes),
});

function SectionCard({
    title,
    description,
    children,
}: {
    title: string;
    description: string;
    children: ReactNode;
}) {
    return (
        <Card>
            <CardHeader className="pb-4">
                <CardTitle className="text-lg">{title}</CardTitle>
                <CardDescription>{description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                {children}
            </CardContent>
        </Card>
    );
}

function PerEyeRefractionGrid({
    title,
    description,
    section,
    setSection,
}: {
    title: string;
    description: string;
    section: {
        right: RefractionEyeSection;
        left: RefractionEyeSection;
    };
    setSection: (side: 'right' | 'left', field: keyof RefractionEyeSection, value: string) => void;
}) {
    const { t } = useTranslation();

    return (
        <SectionCard title={title} description={description}>
            <div className="grid grid-cols-[140px_1fr_1fr] gap-3 text-sm">
                <div className="font-medium text-muted-foreground">{t('Measurement')}</div>
                <div className="font-medium text-muted-foreground">{t('Right Eye')}</div>
                <div className="font-medium text-muted-foreground">{t('Left Eye')}</div>
                {refractionFields.map((field) => (
                    <FragmentRow
                        key={field}
                        label={t(field.replace(/_/g, ' '))}
                        rightValue={section.right[field]}
                        leftValue={section.left[field]}
                        onRightChange={(value) => setSection('right', field, value)}
                        onLeftChange={(value) => setSection('left', field, value)}
                    />
                ))}
            </div>
        </SectionCard>
    );
}

function PerEyeVisualAcuityGrid({
    section,
    setSection,
}: {
    section: {
        right: VisualAcuityEyeSection;
        left: VisualAcuityEyeSection;
    };
    setSection: (side: 'right' | 'left', field: keyof VisualAcuityEyeSection, value: string) => void;
}) {
    const { t } = useTranslation();

    return (
        <SectionCard
            title={t('Visual Acuity')}
            description={t('Capture distance, near, pinhole, with-glasses, and without-glasses values for each eye.')}
        >
            <div className="grid grid-cols-[140px_1fr_1fr] gap-3 text-sm">
                <div className="font-medium text-muted-foreground">{t('Measurement')}</div>
                <div className="font-medium text-muted-foreground">{t('Right Eye')}</div>
                <div className="font-medium text-muted-foreground">{t('Left Eye')}</div>
                {visualAcuityFields.map((field) => (
                    <FragmentRow
                        key={field}
                        label={t(field.replace(/_/g, ' '))}
                        rightValue={section.right[field]}
                        leftValue={section.left[field]}
                        onRightChange={(value) => setSection('right', field, value)}
                        onLeftChange={(value) => setSection('left', field, value)}
                    />
                ))}
            </div>
        </SectionCard>
    );
}

function FragmentRow({
    label,
    rightValue,
    leftValue,
    onRightChange,
    onLeftChange,
}: {
    label: string;
    rightValue: string;
    leftValue: string;
    onRightChange: (value: string) => void;
    onLeftChange: (value: string) => void;
}) {
    const handleTextChange = (updater: (value: string) => void) => (
        event: ChangeEvent<HTMLInputElement>,
    ) => updater(event.target.value);

    return (
        <>
            <div className="flex items-center text-sm font-medium text-foreground">{label}</div>
            <Input value={rightValue} onChange={handleTextChange(onRightChange)} />
            <Input value={leftValue} onChange={handleTextChange(onLeftChange)} />
        </>
    );
}

export default function ClinicalForm({
    title,
    submitLabel,
    submittingLabel,
    data,
    setData,
    errors,
    processing,
    eyepatients,
    opticaldoctors,
    onCancel,
    onSubmit,
}: ClinicalFormProps) {
    const { t } = useTranslation();
    const handleTextChange = (updater: (value: string) => void) => (
        event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
    ) => updater(event.target.value);
    const handleCheckedChange = (updater: (checked: boolean) => void) => (checked: boolean | 'indeterminate') => {
        updater(!!checked);
    };
    const handleValueChange = (updater: (value: string) => void) => (value: string) => updater(value);

    const updateData = (updater: (previousData: FormData) => FormData) => {
        setData((previousData) => updater(previousData));
    };

    const updateComplaintSelection = (complaint: string, checked: boolean) => {
        updateData((previousData) => ({
            ...previousData,
            complaints: {
                ...previousData.complaints,
                items: checked
                    ? [...previousData.complaints.items, complaint]
                    : previousData.complaints.items.filter((item) => item !== complaint),
            },
        }));
    };

    const updateVisualAcuity = (side: 'right' | 'left', field: keyof VisualAcuityEyeSection, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            visual_acuity: {
                ...previousData.visual_acuity,
                [side]: {
                    ...previousData.visual_acuity[side],
                    [field]: value,
                },
            },
        }));
    };

    const updateRefraction = (side: 'right' | 'left', field: keyof RefractionEyeSection, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            refraction: {
                ...previousData.refraction,
                [side]: {
                    ...previousData.refraction[side],
                    [field]: value,
                },
            },
        }));
    };

    const updateGlassesPrescription = (side: 'right' | 'left', field: keyof RefractionEyeSection, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            glasses_prescription: {
                ...previousData.glasses_prescription,
                [side]: {
                    ...previousData.glasses_prescription[side],
                    [field]: value,
                },
            },
        }));
    };

    const updateEyeExamination = (side: 'right' | 'left', field: keyof EyeExaminationEyeSection, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            eye_examination: {
                ...previousData.eye_examination,
                [side]: {
                    ...previousData.eye_examination[side],
                    [field]: value,
                },
            },
        }));
    };

    const updateMedicalHistory = (
        field: keyof MedicalHistorySection,
        key: 'checked' | 'notes',
        value: boolean | string,
    ) => {
        updateData((previousData) => ({
            ...previousData,
            medical_history: {
                ...previousData.medical_history,
                [field]: {
                    ...previousData.medical_history[field],
                    [key]: value,
                },
            },
        }));
    };

    const updateMedicine = (index: number, field: keyof MedicineRow, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            medicines: previousData.medicines.map((medicine, itemIndex) => (
                itemIndex === index
                    ? { ...medicine, [field]: value }
                    : medicine
            )),
        }));
    };

    const addMedicine = () => {
        updateData((previousData) => ({
            ...previousData,
            medicines: [...previousData.medicines, emptyMedicine()],
        }));
    };

    const removeMedicine = (index: number) => {
        updateData((previousData) => ({
            ...previousData,
            medicines: previousData.medicines.length === 1
                ? [emptyMedicine()]
                : previousData.medicines.filter((_, itemIndex) => itemIndex !== index),
        }));
    };

    const updateSecondaryDiagnosis = (index: number, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            diagnosis: {
                ...previousData.diagnosis,
                secondary: previousData.diagnosis.secondary.map((item, itemIndex) => (
                    itemIndex === index ? value : item
                )),
            },
        }));
    };

    const addSecondaryDiagnosis = () => {
        updateData((previousData) => ({
            ...previousData,
            diagnosis: {
                ...previousData.diagnosis,
                secondary: [...previousData.diagnosis.secondary, ''],
            },
        }));
    };

    const removeSecondaryDiagnosis = (index: number) => {
        updateData((previousData) => ({
            ...previousData,
            diagnosis: {
                ...previousData.diagnosis,
                secondary: previousData.diagnosis.secondary.length === 1
                    ? ['']
                    : previousData.diagnosis.secondary.filter((_, itemIndex) => itemIndex !== index),
            },
        }));
    };

    const updateAnnotation = (index: number, value: string) => {
        updateData((previousData) => ({
            ...previousData,
            eye_diagram: {
                ...previousData.eye_diagram,
                annotations: previousData.eye_diagram.annotations.map((item, itemIndex) => (
                    itemIndex === index ? value : item
                )),
            },
        }));
    };

    const addAnnotation = () => {
        updateData((previousData) => ({
            ...previousData,
            eye_diagram: {
                ...previousData.eye_diagram,
                annotations: [...previousData.eye_diagram.annotations, ''],
            },
        }));
    };

    const removeAnnotation = (index: number) => {
        updateData((previousData) => ({
            ...previousData,
            eye_diagram: {
                ...previousData.eye_diagram,
                annotations: previousData.eye_diagram.annotations.length === 1
                    ? ['']
                    : previousData.eye_diagram.annotations.filter((_, itemIndex) => itemIndex !== index),
            },
        }));
    };

    return (
        <DialogContent className="h-[95vh] w-[95vw] max-w-6xl overflow-hidden p-0">
            <form onSubmit={onSubmit} className="flex h-full flex-col">
                <DialogHeader className="border-b px-6 py-4">
                    <DialogTitle className="text-xl">{title}</DialogTitle>
                </DialogHeader>

                <Tabs defaultValue="patient" className="flex min-h-0 flex-1 flex-col">
                    <div className="border-b px-6 py-3">
                        <TabsList className="grid h-auto w-full grid-cols-2 gap-2 bg-muted/50 md:grid-cols-3 xl:grid-cols-5">
                            <TabsTrigger value="patient">{t('Patient Info')}</TabsTrigger>
                            <TabsTrigger value="vision">{t('Vision & Refraction')}</TabsTrigger>
                            <TabsTrigger value="exam">{t('Examination')}</TabsTrigger>
                            <TabsTrigger value="assessment">{t('History & Diagnosis')}</TabsTrigger>
                            <TabsTrigger value="prescription">{t('Prescription & Notes')}</TabsTrigger>
                        </TabsList>
                    </div>

                    <div className="min-h-0 flex-1 overflow-y-auto px-6 py-6">
                        <TabsContent value="patient" className="mt-0 space-y-6">
                            <SectionCard
                                title={t('Patient Information')}
                                description={t('Record the patient, clinician, and visit dates for this eye test prescription.')}
                            >
                                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    <div className="space-y-2">
                                        <Label htmlFor="patient_id" required>{t('Patient')}</Label>
                                        <Select
                                            value={data.patient_id}
                                            onValueChange={handleValueChange((value) => updateData((previousData) => ({ ...previousData, patient_id: value })))}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder={t('Select Patient')} />
                                            </SelectTrigger>
                                            <SelectContent searchable>
                                                {eyepatients.map((patient) => (
                                                    <SelectItem key={patient.id} value={patient.id.toString()}>
                                                        {patient.patient_name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.patient_id} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="doctor_name" required>{t('Doctor / Optometrist')}</Label>
                                        <Select
                                            value={data.doctor_name}
                                            onValueChange={handleValueChange((value) => updateData((previousData) => ({ ...previousData, doctor_name: value })))}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder={t('Select Doctor')} />
                                            </SelectTrigger>
                                            <SelectContent searchable>
                                                {opticaldoctors.map((doctor) => (
                                                    <SelectItem key={doctor.id} value={doctor.id.toString()}>
                                                        {doctor.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.doctor_name} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label required>{t('Test Date')}</Label>
                                        <DatePicker
                                            value={data.test_date}
                                            onChange={handleValueChange((value) => updateData((previousData) => ({ ...previousData, test_date: value })))}
                                            placeholder={t('Select Test Date')}
                                        />
                                        <InputError message={errors.test_date} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label>{t('Follow-up Date')}</Label>
                                        <DatePicker
                                            value={data.follow_up_date}
                                            onChange={handleValueChange((value) => updateData((previousData) => ({ ...previousData, follow_up_date: value })))}
                                            placeholder={t('Select Follow-up Date')}
                                        />
                                        <InputError message={errors.follow_up_date} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label>{t('Prescription Expiry Date')}</Label>
                                        <DatePicker
                                            value={data.prescription_expiry_date}
                                            onChange={handleValueChange((value) => updateData((previousData) => ({ ...previousData, prescription_expiry_date: value })))}
                                            placeholder={t('Select Prescription Expiry Date')}
                                        />
                                        <InputError message={errors.prescription_expiry_date} />
                                    </div>

                                </div>
                            </SectionCard>
                            <SectionCard
                                title={t('Chief Complaints')}
                                description={t('Record the patient complaints and identify the affected eye.')}
                            >
                                <div className="grid gap-4 lg:grid-cols-[2fr_1fr]">
                                    <div className="space-y-4">
                                        <div className="grid gap-3 md:grid-cols-2">
                                            {complaintOptions.map((complaint) => (
                                                <label key={complaint} className="flex items-center gap-3 rounded-md border p-3 text-sm">
                                                    <Checkbox
                                                        checked={data.complaints.items.includes(complaint)}
                                                        onCheckedChange={handleCheckedChange((checked) => updateComplaintSelection(complaint, checked))}
                                                    />
                                                    <span>{t(complaint)}</span>
                                                </label>
                                            ))}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="custom_complaint">{t('Other Complaint')}</Label>
                                            <Textarea
                                                id="custom_complaint"
                                                value={data.complaints.custom}
                                                onChange={handleTextChange((value) => updateData((previousData) => ({
                                                    ...previousData,
                                                    complaints: { ...previousData.complaints, custom: value },
                                                })))}
                                                placeholder={t('Capture any complaint that is not covered by the predefined list.')}
                                                rows={3}
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-3">
                                        <Label>{t('Affected Eye')}</Label>
                                        <RadioGroup
                                            value={data.complaints.affected_eye}
                                            onValueChange={handleValueChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                complaints: {
                                                    ...previousData.complaints,
                                                    affected_eye: value as EyeTestPrescriptionFormData['complaints']['affected_eye'],
                                                },
                                            })))}
                                            className="space-y-3"
                                        >
                                            {[
                                                ['right', t('Right Eye')],
                                                ['left', t('Left Eye')],
                                                ['both', t('Both Eyes')],
                                                ['na', t('Not Specified')],
                                            ].map(([value, label]) => (
                                                <label key={value} className="flex items-center gap-3 rounded-md border p-3 text-sm">
                                                    <RadioGroupItem value={value} id={`affected_eye_${value}`} />
                                                    <span>{label}</span>
                                                </label>
                                            ))}
                                        </RadioGroup>
                                    </div>
                                </div>
                            </SectionCard>
                        </TabsContent>

                        <TabsContent value="vision" className="mt-0 space-y-6">
                            <PerEyeVisualAcuityGrid section={data.visual_acuity} setSection={updateVisualAcuity} />
                            <PerEyeRefractionGrid
                                title={t('Refraction')}
                                description={t('Capture sphere, cylinder, axis, vision, near vision, and add power separately for right and left eyes.')}
                                section={data.refraction}
                                setSection={updateRefraction}
                            />
                        </TabsContent>

                        <TabsContent value="exam" className="mt-0 space-y-6">
                            <SectionCard
                                title={t('Eye Examination')}
                                description={t('Keep the same right-eye and left-eye review order as the paper examination table.')}
                            >
                                <div className="overflow-x-auto">
                                    <div className="grid min-w-[760px] grid-cols-[180px_1fr_1fr] gap-3 text-sm">
                                        <div className="font-medium text-muted-foreground">{t('Examination')}</div>
                                        <div className="font-medium text-muted-foreground">{t('Right Eye')}</div>
                                        <div className="font-medium text-muted-foreground">{t('Left Eye')}</div>
                                        {examinationRows.map((row) => (
                                            <FragmentRow
                                                key={row}
                                                label={t(row.replace(/_/g, ' '))}
                                                rightValue={data.eye_examination.right[row]}
                                                leftValue={data.eye_examination.left[row]}
                                                onRightChange={(value) => updateEyeExamination('right', row, value)}
                                                onLeftChange={(value) => updateEyeExamination('left', row, value)}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </SectionCard>

                            <SectionCard
                                title={t('Intraocular Pressure')}
                                description={t('Capture IOP for both eyes in the same visit summary.')}
                            >
                                <div className="grid gap-4 md:grid-cols-3">
                                    <div className="space-y-2">
                                        <Label htmlFor="iop_right">{t('Right Eye')}</Label>
                                        <Input
                                            id="iop_right"
                                            type="number"
                                            step="0.1"
                                            value={data.intraocular_pressure.right}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                intraocular_pressure: {
                                                    ...previousData.intraocular_pressure,
                                                    right: value,
                                                },
                                            })))}
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="iop_left">{t('Left Eye')}</Label>
                                        <Input
                                            id="iop_left"
                                            type="number"
                                            step="0.1"
                                            value={data.intraocular_pressure.left}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                intraocular_pressure: {
                                                    ...previousData.intraocular_pressure,
                                                    left: value,
                                                },
                                            })))}
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="iop_unit">{t('Unit')}</Label>
                                        <Input
                                            id="iop_unit"
                                            value={data.intraocular_pressure.unit}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                intraocular_pressure: {
                                                    ...previousData.intraocular_pressure,
                                                    unit: value,
                                                },
                                            })))}
                                        />
                                    </div>
                                </div>
                            </SectionCard>
                        </TabsContent>

                        <TabsContent value="assessment" className="mt-0 space-y-6">
                            <SectionCard
                                title={t('Medical History')}
                                description={t('Capture history checkboxes with supporting notes so the form remains structured and clinically readable.')}
                            >
                                <div className="space-y-4">
                                    {medicalHistoryRows.map((field) => (
                                        <div key={field} className="grid gap-3 rounded-lg border p-4 lg:grid-cols-[220px_1fr]">
                                            <label className="flex items-center gap-3 text-sm font-medium">
                                                <Checkbox
                                                    checked={data.medical_history[field].checked}
                                                    onCheckedChange={handleCheckedChange((checked) => updateMedicalHistory(field, 'checked', checked))}
                                                />
                                                <span>{t(field.replace(/_/g, ' '))}</span>
                                            </label>
                                            <Textarea
                                                value={data.medical_history[field].notes}
                                                onChange={handleTextChange((value) => updateMedicalHistory(field, 'notes', value))}
                                                placeholder={t('Supporting notes')}
                                                rows={2}
                                            />
                                        </div>
                                    ))}
                                </div>
                            </SectionCard>

                            <SectionCard
                                title={t('Diagnosis')}
                                description={t('Keep the main diagnosis prominent and add as many secondary diagnoses as needed.')}
                            >
                                <div className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="primary_diagnosis">{t('Primary Diagnosis')}</Label>
                                        <Input
                                            id="primary_diagnosis"
                                            value={data.diagnosis.primary}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                diagnosis: { ...previousData.diagnosis, primary: value },
                                            })))}
                                        />
                                    </div>

                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between">
                                            <Label>{t('Secondary Diagnosis')}</Label>
                                            <Button type="button" variant="outline" size="sm" onClick={addSecondaryDiagnosis}>
                                                + {t('Add')}
                                            </Button>
                                        </div>
                                        {data.diagnosis.secondary.map((item, index) => (
                                            <div key={`secondary-${index}`} className="flex gap-3">
                                                <Input
                                                    value={item}
                                                    onChange={handleTextChange((value) => updateSecondaryDiagnosis(index, value))}
                                                    placeholder={t('Secondary diagnosis')}
                                                />
                                                <Button type="button" variant="outline" size="sm" onClick={() => removeSecondaryDiagnosis(index)}>
                                                    {t('Remove')}
                                                </Button>
                                            </div>
                                        ))}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="diagnosis_summary">{t('Diagnosis Summary')}</Label>
                                        <Textarea
                                            id="diagnosis_summary"
                                            value={data.diagnosis.summary}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                diagnosis: { ...previousData.diagnosis, summary: value },
                                            })))}
                                            rows={4}
                                        />
                                    </div>
                                </div>
                            </SectionCard>
                        </TabsContent>

                        <TabsContent value="prescription" className="mt-0 space-y-6">
                            <SectionCard
                                title={t('Medicine Prescription')}
                                description={t('Use repeatable rows so medicine, eye, frequency, duration, and instructions stay structured.')}
                            >
                                <div className="space-y-4">
                                    {data.medicines.map((medicine, index) => (
                                        <div key={`medicine-${index}`} className="rounded-lg border p-4">
                                            <div className="mb-4 flex items-center justify-between">
                                                <h4 className="font-medium">{t('Medicine Row')} {index + 1}</h4>
                                                <Button type="button" variant="outline" size="sm" onClick={() => removeMedicine(index)}>
                                                    {t('Remove')}
                                                </Button>
                                            </div>
                                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                                                <div className="space-y-2 xl:col-span-2">
                                                    <Label>{t('Medicine')}</Label>
                                                    <Input
                                                        value={medicine.medicine}
                                                        onChange={handleTextChange((value) => updateMedicine(index, 'medicine', value))}
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>{t('Eye')}</Label>
                                                    <Select value={medicine.eye} onValueChange={handleValueChange((value) => updateMedicine(index, 'eye', value))}>
                                                        <SelectTrigger>
                                                            <SelectValue placeholder={t('Select')} />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="right">{t('Right Eye')}</SelectItem>
                                                            <SelectItem value="left">{t('Left Eye')}</SelectItem>
                                                            <SelectItem value="both">{t('Both Eyes')}</SelectItem>
                                                            <SelectItem value="systemic">{t('Systemic')}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>{t('Frequency')}</Label>
                                                    <Input
                                                        value={medicine.frequency}
                                                        onChange={handleTextChange((value) => updateMedicine(index, 'frequency', value))}
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>{t('Duration')}</Label>
                                                    <Input
                                                        value={medicine.duration}
                                                        onChange={handleTextChange((value) => updateMedicine(index, 'duration', value))}
                                                    />
                                                </div>
                                            </div>
                                            <div className="mt-4 space-y-2">
                                                <Label>{t('Instructions')}</Label>
                                                <Textarea
                                                    value={medicine.instructions}
                                                    onChange={handleTextChange((value) => updateMedicine(index, 'instructions', value))}
                                                    rows={2}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                    <Button type="button" variant="outline" onClick={addMedicine}>
                                        + {t('Add Medicine')}
                                    </Button>
                                </div>
                            </SectionCard>

                            <PerEyeRefractionGrid
                                title={t('Glasses Prescription')}
                                description={t('Capture the dedicated optical prescription separately from clinical refraction notes.')}
                                section={data.glasses_prescription}
                                setSection={updateGlassesPrescription}
                            />

                            <SectionCard
                                title={t('Glasses Notes')}
                                description={t('Use this note field for lens recommendations, frame notes, or dispensing context.')}
                            >
                                <Textarea
                                    value={data.glasses_prescription.notes}
                                    onChange={handleTextChange((value) => updateData((previousData) => ({
                                        ...previousData,
                                        glasses_prescription: {
                                            ...previousData.glasses_prescription,
                                            notes: value,
                                        },
                                    })))}
                                    rows={3}
                                />
                            </SectionCard>

                            <SectionCard
                                title={t('Clinical Notes & Eye Diagram')}
                                description={t('Capture extra notes, diagram references, and any annotation details needed for later review or printing.')}
                            >
                                <div className="grid gap-4 lg:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label htmlFor="clinical_notes">{t('Clinical Notes')}</Label>
                                        <Textarea
                                            id="clinical_notes"
                                            value={data.notes}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({ ...previousData, notes: value })))}
                                            rows={5}
                                        />
                                        <InputError message={errors.notes} />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="eye_diagram_reference">{t('Eye Diagram Reference')}</Label>
                                        <Input
                                            id="eye_diagram_reference"
                                            value={data.eye_diagram.image_path}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                eye_diagram: { ...previousData.eye_diagram, image_path: value },
                                            })))}
                                            placeholder={t('Image path, scan reference, or storage identifier')}
                                        />
                                        <Textarea
                                            value={data.eye_diagram.notes}
                                            onChange={handleTextChange((value) => updateData((previousData) => ({
                                                ...previousData,
                                                eye_diagram: { ...previousData.eye_diagram, notes: value },
                                            })))}
                                            placeholder={t('Eye diagram notes')}
                                            rows={3}
                                        />
                                    </div>
                                </div>
                                <div className="space-y-3">
                                    <div className="flex items-center justify-between">
                                        <Label>{t('Eye Diagram Annotations')}</Label>
                                        <Button type="button" variant="outline" size="sm" onClick={addAnnotation}>
                                            + {t('Add')}
                                        </Button>
                                    </div>
                                    {data.eye_diagram.annotations.map((annotation, index) => (
                                        <div key={`annotation-${index}`} className="flex gap-3">
                                            <Input
                                                value={annotation}
                                                onChange={handleTextChange((value) => updateAnnotation(index, value))}
                                                placeholder={t('Annotation')}
                                            />
                                            <Button type="button" variant="outline" size="sm" onClick={() => removeAnnotation(index)}>
                                                {t('Remove')}
                                            </Button>
                                        </div>
                                    ))}
                                </div>
                            </SectionCard>

                        </TabsContent>
                    </div>
                </Tabs>

                <div className="flex items-center justify-between gap-3 border-t px-6 py-4">
                    <div className="flex gap-2">
                        <Button type="button" variant="outline" onClick={onCancel}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? submittingLabel : submitLabel}
                        </Button>
                    </div>
                </div>
            </form>
        </DialogContent>
    );
}
