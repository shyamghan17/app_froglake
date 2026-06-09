import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Training {
    id: number;
    name: string;
}

export interface Certification {
    id: number;
    employee_name: string;
    certificate_name: string;
    issued_date: string;
    expiry_date?: string;
    training_id?: number;
    training?: Training;
    created_at: string;
}

export interface CreateCertificationFormData {
    employee_name: string;
    certificate_name: string;
    issued_date: string;
    expiry_date: string;
    training_id: string;
}

export interface EditCertificationFormData {
    employee_name: string;
    certificate_name: string;
    issued_date: string;
    expiry_date: string;
    training_id: string;
}

export interface CertificationFilters {
    employee_name: string;
    certificate_name: string;
}

export type PaginatedCertifications = PaginatedData<Certification>;
export type CertificationModalState = ModalState<Certification>;

export interface CertificationsIndexProps {
    certifications: PaginatedCertifications;
    auth: AuthContext;
    trainings: any[];
    [key: string]: unknown;
}

export interface CreateCertificationProps {
    onSuccess: () => void;
}

export interface EditCertificationProps {
    certification: Certification;
    onSuccess: () => void;
}

export interface CertificationShowProps {
    certification: Certification;
    [key: string]: unknown;
}