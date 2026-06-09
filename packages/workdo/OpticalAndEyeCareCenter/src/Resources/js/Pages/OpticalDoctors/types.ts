import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface OpticalSpecialization {
    id: number;
    name: string;
}

export interface User {
    id: number;
    name: string;
    email?: string;
    mobile_no?: string;
    avatar?: string;
    is_disable?: number;
}

export interface OpticalDoctor {
    id: number;
    doctor_code: string;
    license_number: string;
    gender: string;
    years_of_experience: number;
    consultation_fee: number;
    qualifications?: string;
    status: string;
    user_id?: number;
    user?: User;
    hospital_specialization_id?: number;
    hospital_specialization?: OpticalSpecialization;
    created_at: string;
}

export interface CreateOpticalDoctorFormData {
    doctor_code: string;
    license_number: string;
    gender: string;
    years_of_experience: string;
    consultation_fee: string;
    qualifications: string;
    status: string;
    user_id: string;
    hospital_specialization_id: string;
}

export interface EditOpticalDoctorFormData {
    doctor_code: string;
    license_number: string;
    gender: string;
    years_of_experience: string;
    consultation_fee: string;
    qualifications: string;
    status: string;
    user_id: string;
    hospital_specialization_id: string;
}

export interface OpticalDoctorFilters {
    doctor_code: string;
    status: string;
    gender: string;
    hospital_specialization_id: string;
}

export type PaginatedOpticalDoctors = PaginatedData<OpticalDoctor>;
export type OpticalDoctorModalState = ModalState<OpticalDoctor>;

export interface OpticalDoctorsIndexProps {
    opticaldoctors: PaginatedOpticalDoctors;
    auth: AuthContext;
    users: User[];
    opticalspecializations: OpticalSpecialization[];
    [key: string]: unknown;
}

export interface CreateOpticalDoctorProps {
    onSuccess: () => void;
}

export interface EditOpticalDoctorProps {
    opticaldoctor: OpticalDoctor;
    onSuccess: () => void;
}

export interface OpticalDoctorShowProps {
    opticaldoctor: OpticalDoctor;
    [key: string]: unknown;
}
