import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface RepairOrderRequest {
    id: number;
    name: string;
}

export interface RepairPart {
    id: number;
    name: string;
}

export interface RepairWarranty {
    id: number;
    warranty_number: string;
    warranty_period?: string;
    warranty_terms?: string;
    claim_status: string;
    repair_order_id?: number;
    repair_order?: RepairOrderRequest;
    part_id?: number;
    part?: RepairPart;
    created_at: string;
}

export interface CreateRepairWarrantyFormData {
    warranty_number: string;
    warranty_period: string;
    warranty_terms: string;
    claim_status: string;
    repair_order_id: string;
    part_id: string;
}

export interface EditRepairWarrantyFormData {
    warranty_number: string;
    warranty_period: string;
    warranty_terms: string;
    claim_status: string;
    repair_order_id: string;
    part_id: string;
}

export interface RepairWarrantyFilters {
    warranty_number: string;
    warranty_terms: string;
    repair_order_id: string;
    part_id: string;
    claim_status: string;
    date_range: string;
}

export type PaginatedRepairWarranties = PaginatedData<RepairWarranty>;
export type RepairWarrantyModalState = ModalState<RepairWarranty>;

export interface RepairWarrantiesIndexProps {
    repairwarranties: PaginatedRepairWarranties;
    auth: AuthContext;
    repairorderrequests: any[];
    repairparts: any[];
    [key: string]: unknown;
}

export interface CreateRepairWarrantyProps {
    onSuccess: () => void;
}

export interface EditRepairWarrantyProps {
    repairwarranty: RepairWarranty;
    repairorderrequests: any[];
    repairparts: any[];
    onSuccess: () => void;
}

