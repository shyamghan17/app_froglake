import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface AppointmentForPayment {
    id: number;
    appointment_number: string;
    name: string;
    price: number;
    service?: { id: number; name: string };
}

export interface PhotoStudioAppointmentPayment {
    id: number;
    appointment_id: number;
    appointment_number: string;
    customer_name: string;
    service_name: string;
    payment_date: string;
    amount: number;
    payment_status: 'pending' | 'cleared';
    payment_type: string;
    description?: string;
    appointment?: AppointmentForPayment;
    created_at: string;
}

export interface CreatePaymentFormData {
    appointment_id: string;
    payment_date: string;
    description: string;
}

export interface PaymentFilters {
    search: string;
    payment_status: string;
    service_id: string;
    date_range: string;
}

export type PaginatedPayments = PaginatedData<PhotoStudioAppointmentPayment>;
export type PaymentModalState = ModalState<PhotoStudioAppointmentPayment>;

export interface PaymentsIndexProps {
    payments: PaginatedPayments;
    services: Array<{ id: number; name: string }>;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreatePaymentProps {
    appointment: AppointmentForPayment;
    onSuccess: () => void;
}
