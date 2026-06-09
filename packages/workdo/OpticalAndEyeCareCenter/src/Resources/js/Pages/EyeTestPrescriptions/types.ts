import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface EyePatient {
    id: number;
    name: string;
}

export interface EyeTestPrescription {
    id: number;
    doctor_name: string;
    test_date: string;
    test_results?: string;
    prescription_details?: string;
    prescription_expiry_date?: string;
    notes?: string;
    patient_id?: number;
    patient?: EyePatient;
    created_at: string;
}

export interface CreateEyeTestPrescriptionFormData {
    doctor_name: string;
    test_date: string;
    test_results: string;
    prescription_details: string;
    prescription_expiry_date: string;
    notes: string;
    patient_id: string;
}

export interface EditEyeTestPrescriptionFormData {
    doctor_name: string;
    test_date: string;
    test_results: string;
    prescription_details: string;
    prescription_expiry_date: string;
    notes: string;
    patient_id: string;
}

export interface EyeTestPrescriptionFilters {
    doctor_name: string;
}

export type PaginatedEyeTestPrescriptions = PaginatedData<EyeTestPrescription>;
export type EyeTestPrescriptionModalState = ModalState<EyeTestPrescription>;

export interface EyeTestPrescriptionsIndexProps {
    eyetestprescriptions: PaginatedEyeTestPrescriptions;
    auth: AuthContext;
    eyepatients: any[];
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