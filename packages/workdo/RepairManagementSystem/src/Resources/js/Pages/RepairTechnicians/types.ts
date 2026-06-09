import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface RepairTechnician {
    id: number;
    name: string;
    email: string;
    mobile_no: string;
    created_at: string;
}

export interface CreateRepairTechnicianFormData {
    name: string;
    email: string;
    mobile_no: string;
}

export interface EditRepairTechnicianFormData {
    name: string;
    email: string;
    mobile_no: string;
}

export interface RepairTechnicianFilters {
    search: string;
}

export type PaginatedRepairTechnicians = PaginatedData<RepairTechnician>;
export type RepairTechnicianModalState = ModalState<RepairTechnician>;

export interface RepairTechniciansIndexProps {
    repairtechnicians: PaginatedRepairTechnicians;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateRepairTechnicianProps {
    onSuccess: () => void;
}

export interface EditRepairTechnicianProps {
    repairtechnician: RepairTechnician;
    onSuccess: () => void;
}

