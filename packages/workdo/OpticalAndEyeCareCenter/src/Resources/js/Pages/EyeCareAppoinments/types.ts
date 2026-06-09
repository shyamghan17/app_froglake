import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface EyePatient {
    id: number;
    name: string;
}

export interface EyeCareAppoinment {
    id: number;
    doctor_name: string;
    appointment_datetime: any;
    status: string;
    appointment_type: string;
    notes?: string;
    patient_id?: number;
    patient?: EyePatient;
    created_at: string;
}

export interface CreateEyeCareAppoinmentFormData {
    doctor_name: string;
    appointment_datetime: any;
    status: string;
    appointment_type: string;
    notes: string;
    patient_id: string;
}

export interface EditEyeCareAppoinmentFormData {
    doctor_name: string;
    appointment_datetime: any;
    status: string;
    appointment_type: string;
    notes: string;
    patient_id: string;
}

export interface EyeCareAppoinmentFilters {
    doctor_name: string;
    status: string;
    appointment_type: string;
}

export type PaginatedEyeCareAppoinments = PaginatedData<EyeCareAppoinment>;
export type EyeCareAppoinmentModalState = ModalState<EyeCareAppoinment>;

export interface EyeCareAppoinmentsIndexProps {
    eyecareappoinments: PaginatedEyeCareAppoinments;
    auth: AuthContext;
    eyepatients: any[];
    [key: string]: unknown;
}

export interface CreateEyeCareAppoinmentProps {
    onSuccess: () => void;
}

export interface EditEyeCareAppoinmentProps {
    eyecareappoinment: EyeCareAppoinment;
    onSuccess: () => void;
}

export interface EyeCareAppoinmentShowProps {
    eyecareappoinment: EyeCareAppoinment;
    [key: string]: unknown;
}