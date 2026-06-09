import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile_number: string;
    description?: string;
    created_at: string;
    full_name?: string;
}

export interface CreateCustomerFormData {
    first_name: string;
    last_name: string;
    email: string;
    mobile_number: string;
    description: string;
}

export interface EditCustomerFormData {
    first_name: string;
    last_name: string;
    email: string;
    mobile_number: string;
    description: string;
}

export interface CustomerFilters {
    search: string;
    date_from: string;
    date_to: string;
}

export type PaginatedCustomers = PaginatedData<Customer>;
export type CustomerModalState = ModalState<Customer>;

export interface CustomersIndexProps {
    customers: PaginatedCustomers;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateCustomerProps {
    onSuccess: () => void;
}

export interface EditCustomerProps {
    customer: Customer;
    onSuccess: () => void;
}
