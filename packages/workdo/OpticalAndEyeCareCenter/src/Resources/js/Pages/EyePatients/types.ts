import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface EyePatient {
    id: number;
    patient_name: string;
    dob: string;
    gender: string;
    contact_no: string;
    address?: string;
    medical_history?: string;
    previous_prescriptions?: string;
    preferred_doctor?: string;
    created_at: string;
    doctor?: any;
}

export interface CreateEyePatientFormData {
    patient_name: string;
    dob: string;
    gender: string;
    contact_no: string;
    address: string;
    medical_history: string;
    previous_prescriptions: string;
    preferred_doctor: string;
}

export interface EditEyePatientFormData {
    patient_name: string;
    dob: string;
    gender: string;
    contact_no: string;
    address: string;
    medical_history: string;
    previous_prescriptions: string;
    preferred_doctor: string;
}

export interface EyePatientFilters {
    patient_name: string;
    contact_no: string;
    gender: string;
}

export type PaginatedEyePatients = PaginatedData<EyePatient>;
export type EyePatientModalState = ModalState<EyePatient>;

export interface EyePatientsIndexProps {
    eyepatients: PaginatedEyePatients;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateEyePatientProps {
    onSuccess: () => void;
}

export interface EditEyePatientProps {
    eyepatient: EyePatient;
    onSuccess: () => void;
}

export interface EyePatientShowProps {
    eyepatient: EyePatient;
    [key: string]: unknown;
}