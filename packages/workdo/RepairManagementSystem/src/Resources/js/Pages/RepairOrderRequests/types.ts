import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface RepairOrderRequest {
    id: number;
    product_name?: string;
    product_quantity?: number;
    customer_name?: string;
    customer_email?: any;
    customer_mobile_no?: string;
    date?: string;
    expiry_date?: string;
    repair_technician?: number;
    location?: string;
    status: number;
    created_at: string;
}

export interface CreateRepairOrderRequestFormData {
    product_name: string;
    product_quantity: string;
    customer_name: string;
    customer_email: any;
    customer_mobile_no: string;
    date: string;
    expiry_date: string;
    repair_technician: string;
}

export interface EditRepairOrderRequestFormData {
    product_name: string;
    product_quantity: string;
    customer_name: string;
    customer_email: any;
    customer_mobile_no: string;
    date: string;
    expiry_date: string;
    repair_technician: string;
}

export interface RepairOrderRequestFilters {
    product_name: string;
    customer_name: string;
    customer_email: string;
    repair_technician: string;
    status: string;
}

export type PaginatedRepairOrderRequests = PaginatedData<RepairOrderRequest>;
export type RepairOrderRequestModalState = ModalState<RepairOrderRequest>;

export interface RepairOrderRequestsIndexProps {
    repairorderrequests: PaginatedRepairOrderRequests;
    auth: AuthContext;
    repairtechnicians: any[];
    repairstatuses: any[];
    [key: string]: unknown;
}

export interface CreateRepairOrderRequestProps {
    onSuccess: () => void;
}

export interface EditRepairOrderRequestProps {
    repairorderrequest: RepairOrderRequest;
    onSuccess: () => void;
}

