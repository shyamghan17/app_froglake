import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface EyePatient {
    id: number;
    patient_name: string;
    preferred_doctor?: number | string | null;
    dob?: string | null;
    gender?: string | null;
    contact_no?: string | null;
    address?: string | null;
}

export interface OpticalDoctor {
    id: number;
    name: string;
    email?: string;
}

export type EyeLaterality = 'right' | 'left' | 'both' | 'na';
export type MedicineEyeLaterality = EyeLaterality | 'systemic';

export interface ComplaintSection {
    items: string[];
    custom: string;
    affected_eye: EyeLaterality;
}

export interface VisualAcuityEyeSection {
    distance: string;
    near: string;
    pinhole: string;
    with_glasses: string;
    without_glasses: string;
}

export interface RefractionEyeSection {
    sphere: string;
    cylinder: string;
    axis: string;
    vision: string;
    near_vision: string;
    add: string;
}

export interface EyeExaminationEyeSection {
    lid: string;
    conjunctiva: string;
    cornea: string;
    anterior_chamber: string;
    iris: string;
    pupil: string;
    lens: string;
    vitreous: string;
    fundus: string;
    colour_vision: string;
}

export interface IntraocularPressureSection {
    right: string;
    left: string;
    unit: string;
}

export interface MedicalHistoryEntry {
    checked: boolean;
    notes: string;
}

export interface MedicalHistorySection {
    tbut: MedicalHistoryEntry;
    blood_pressure: MedicalHistoryEntry;
    past_history: MedicalHistoryEntry;
    other_diseases: MedicalHistoryEntry;
}

export interface DiagnosisSection {
    primary: string;
    secondary: string[];
    summary: string;
}

export interface MedicineRow {
    medicine: string;
    eye: MedicineEyeLaterality | '';
    frequency: string;
    duration: string;
    instructions: string;
}

export interface EyeDiagramSection {
    image_path: string;
    annotations: string[];
    notes: string;
}

export interface ExaminerDetailsSection {
    examiner_name: string;
    examiner_role: string;
    license_number: string;
    signature_path: string;
    examiner_user_id: string;
}

export interface LegacyTextArtifact {
    test_results?: string | null;
    prescription_details?: string | null;
    notes?: string | null;
    source: 'compatibility-fallback' | 'legacy-record';
}

export interface EyewearOrderPrefillArtifact {
    prescription_id: number;
    patient_id?: number | null;
    doctor_user_id?: number | null;
    examiner_user_id?: number | null;
    prescription_expiry_date?: string | null;
    right: Partial<RefractionEyeSection>;
    left: Partial<RefractionEyeSection>;
    notes?: string | null;
    legacy_prescription_details?: string | null;
    summary?: string | null;
}

export interface EyewearOrderIntegrationArtifact {
    ready: boolean;
    source: 'structured' | 'legacy-text' | 'none';
    order_prefill: EyewearOrderPrefillArtifact;
}

export interface PrintLayoutSectionArtifact {
    key: string;
    label: string;
    has_content: boolean;
}

export interface PrintLayoutArtifact {
    status: 'planned' | 'available';
    layout_key: string;
    page_component: string;
    sections: PrintLayoutSectionArtifact[];
}

export interface PrescriptionIntegrationArtifacts {
    legacy_text: LegacyTextArtifact;
    eyewear_order: EyewearOrderIntegrationArtifact;
    print_layout: PrintLayoutArtifact;
}

export interface EyeTestPrescription {
    id: number;
    doctor_name: string | number;
    test_date: string;
    follow_up_date?: string;
    test_results?: string;
    prescription_details?: string;
    prescription_expiry_date?: string;
    notes?: string;
    patient_id?: number;
    patient?: EyePatient;
    doctor?: OpticalDoctor;
    clinical_schema_version?: number | null;
    complaints?: ComplaintSection | null;
    visual_acuity?: {
        right: VisualAcuityEyeSection;
        left: VisualAcuityEyeSection;
    } | null;
    refraction?: {
        right: RefractionEyeSection;
        left: RefractionEyeSection;
    } | null;
    eye_examination?: {
        right: EyeExaminationEyeSection;
        left: EyeExaminationEyeSection;
    } | null;
    intraocular_pressure?: IntraocularPressureSection | null;
    medical_history?: MedicalHistorySection | null;
    diagnosis?: DiagnosisSection | null;
    medicines?: MedicineRow[] | null;
    glasses_prescription?: {
        right: RefractionEyeSection;
        left: RefractionEyeSection;
        notes: string;
    } | null;
    eye_diagram?: EyeDiagramSection | null;
    examiner_details?: ExaminerDetailsSection | null;
    integration_artifacts?: PrescriptionIntegrationArtifacts;
    created_at: string;
}

export interface EyeTestPrescriptionFormData {
    patient_id: string;
    doctor_name: string;
    test_date: string;
    follow_up_date: string;
    prescription_expiry_date: string;
    clinical_schema_version: number | null;
    complaints: ComplaintSection;
    visual_acuity: {
        right: VisualAcuityEyeSection;
        left: VisualAcuityEyeSection;
    };
    refraction: {
        right: RefractionEyeSection;
        left: RefractionEyeSection;
    };
    eye_examination: {
        right: EyeExaminationEyeSection;
        left: EyeExaminationEyeSection;
    };
    intraocular_pressure: IntraocularPressureSection;
    medical_history: MedicalHistorySection;
    diagnosis: DiagnosisSection;
    medicines: MedicineRow[];
    glasses_prescription: {
        right: RefractionEyeSection;
        left: RefractionEyeSection;
        notes: string;
    };
    eye_diagram: EyeDiagramSection;
    examiner_details: ExaminerDetailsSection;
    test_results: string;
    prescription_details: string;
    notes: string;
}

export type CreateEyeTestPrescriptionFormData = EyeTestPrescriptionFormData;
export type EditEyeTestPrescriptionFormData = EyeTestPrescriptionFormData;

export interface EyeTestPrescriptionFilters {
    search: string;
    patient_id: string;
    doctor_name: string;
    test_date: string;
}

export type PaginatedEyeTestPrescriptions = PaginatedData<EyeTestPrescription>;
export type EyeTestPrescriptionModalState = ModalState<EyeTestPrescription>;

export interface EyeTestPrescriptionsIndexProps {
    eyetestprescriptions: PaginatedEyeTestPrescriptions;
    auth: AuthContext;
    eyepatients: EyePatient[];
    opticaldoctors: OpticalDoctor[];
    [key: string]: unknown;
}

export interface CreateEyeTestPrescriptionProps {
    onSuccess: () => void;
}

export interface EditEyeTestPrescriptionProps {
    eyetestprescription: EyeTestPrescription;
    onSuccess: () => void;
}

export interface EyeTestPrescriptionShowProps {
    eyetestprescription: EyeTestPrescription;
    [key: string]: unknown;
}
