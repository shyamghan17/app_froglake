import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Appointment {
    id: number;
    appointment_number: string;
    date: string;
    item_id?: number;
    package_id?: number;
    staff_id: number;
    customer_id: number;
    start_time: string;
    end_time: string;
    status: string;
    payment: string;
    payment_status: string;
    created_at: string;
    has_payment_entry?: boolean;
    item?: { name: string };
    package?: { name: string };
    staff?: { name: string };
    customer?: { first_name: string; last_name: string };
}

export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

export interface Item {
    id: number;
    name: string;
}

export interface Package {
    id: number;
    name: string;
    item_id?: number;
}

export interface AppointmentFormData {
    date: string;
    item_id: string;
    package_id: string;
    staff_id: string;
    customer_id: string;
    start_time: string;
    end_time: string;
    range_start_time: string;
    range_end_time: string;
    status: string;
    payment_status: string;
}

export interface AppointmentFilters {
    search: string;
    status: string;
    payment_status: string;
}

export type PaginatedAppointments = PaginatedData<Appointment>;
export type AppointmentModalState = ModalState<Appointment>;

export interface AppointmentsIndexProps {
    appointments: PaginatedAppointments;
    items: Item[];
    packages: Package[];
    users: User[];
    customers: Customer[];
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateAppointmentProps {
    items: Item[];
    packages: Package[];
    users: User[];
    customers: Customer[];
    onSuccess: () => void;
}

export interface EditAppointmentProps {
    appointment: Appointment;
    items: Item[];
    packages: Package[];
    users: User[];
    customers: Customer[];
    onSuccess: () => void;
}
