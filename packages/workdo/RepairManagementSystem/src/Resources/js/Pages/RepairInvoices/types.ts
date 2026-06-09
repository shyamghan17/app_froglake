import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface RepairOrderRequest {
    id: number;
    name: string;
}

export interface RepairInvoice {
    id: number;
    invoice_id: number;
    repair_id?: number;
    repair_charge: number;
    total_amount: number;
    status: string;
    repair_order?: RepairOrderRequest;
    created_at: string;
}

export interface CreateRepairInvoiceFormData {
    invoice_id: string;
    repair_id: string;
    repair_charge: string;
    total_amount: string;
    status: string;
}

export interface EditRepairInvoiceFormData {
    invoice_id: string;
    repair_id: string;
    repair_charge: string;
    total_amount: string;
    status: string;
}

export interface RepairInvoiceFilters {
    invoice_id: string;
    status: string;
}

export type PaginatedRepairInvoices = PaginatedData<RepairInvoice>;
export type RepairInvoiceModalState = ModalState<RepairInvoice>;

export interface RepairInvoicesIndexProps {
    repairinvoices: PaginatedRepairInvoices;
    auth: AuthContext;
    repairorderrequests: any[];
    [key: string]: unknown;
}

export interface CreateRepairInvoiceProps {
    onSuccess: () => void;
}

export interface EditRepairInvoiceProps {
    repairinvoice: RepairInvoice;
    onSuccess: () => void;
}

export interface RepairInvoiceShowProps {
    repairinvoice: RepairInvoice;
    [key: string]: unknown;
}