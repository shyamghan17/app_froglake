import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface EyewearOrderItem {
    id?: number;
    product_id: number;
    item_type: 'standard' | 'custom';
    quantity: number;
    unit_price: number;
    discount_percentage: number;
    discount_amount: number;
    tax_percentage: number;
    tax_amount: number;
    total_amount: number;
    taxes?: EyewearOrderItemTax[];
    product?: {
        id: number;
        name: string;
        sku: string;
    };
}

export interface EyewearOrderItemTax {
    id?: number;
    tax_name: string;
    tax_rate: number;
}

export interface EyewearOrder {
    id: number;
    order_number: string;
    order_date: string;
    patient_id: number;
    warehouse_id?: number;
    subtotal: number;
    tax_amount: number;
    discount_amount: number;
    total_amount: number;
    paid_amount: number;
    balance_amount: number;
    payment_status: 'draft' | 'paid';
    payment_method?: string;
    delivery_date?: string;
    delivered_at?: string;
    prescription_details?: string;
    special_notes?: string;
    created_at: string;
    patient?: {
        id: number;
        patient_name: string;
        contact_no: string;
    };
    items?: EyewearOrderItem[];
}

export interface CreateEyewearOrderFormData {
    order_date: string;
    patient_id: number;
    warehouse_id?: number;
    delivery_date?: string;
    payment_method?: string;
    prescription_details?: string;
    special_notes?: string;
    items: EyewearOrderItem[];
}

export interface UpdateEyewearOrderFormData extends CreateEyewearOrderFormData {}

export interface EyewearOrderFilters {
    patient_id: string;
    payment_status: string;
    search: string;
    date_range: string;
}

export type PaginatedEyewearOrders = PaginatedData<EyewearOrder>;
export type EyewearOrderModalState = ModalState<EyewearOrder>;

export interface EyewearOrdersIndexProps {
    orders: PaginatedEyewearOrders;
    patients: Array<{ id: number; patient_name: string; contact_no: string }>;
    warehouses: Array<{ id: number; name: string }>;
    filters: Partial<EyewearOrderFilters>;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateEyewearOrderProps {
    patients: Array<{ id: number; patient_name: string; contact_no: string }>;
    warehouses: Array<{ id: number; name: string; address?: string }>;
}

export interface EditEyewearOrderProps {
    order: EyewearOrder;
    patients: Array<{ id: number; patient_name: string; contact_no: string }>;
    warehouses: Array<{ id: number; name: string; address?: string }>;
}

export interface EyewearOrderShowProps {
    order: EyewearOrder;
    [key: string]: unknown;
}
