import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface BeautyBooking {
    id: number;
    name: string;
    email: any;
    service: number;
    date: string;
    time_slot: string;
    person: number;
    service_price: number;
    price: number;
    phone_number: string;
    gender: string;
    reference: boolean;
    additional_notes: string;
    payment_option: string;
    created_at: string;
}

export interface CreateBeautyBookingFormData {
    name: string;
    email: any;
    service: string;
    date: string;
    time_slot: string;
    person: string;
    service_price: string;
    phone_number: string;
    gender: string;
    reference: boolean;
    additional_notes: string;
}

export interface EditBeautyBookingFormData {
    name: string;
    email: any;
    service: string;
    date: string;
    time_slot: string;
    person: string;
    service_price: string;
    phone_number: string;
    gender: string;
    reference: boolean;
    additional_notes: string;
}

export interface BeautyBookingFilters {
    name: string;
    email: string;
    phone_number: string;
    service: string;
    gender: string;
    reference: string;
}

export type PaginatedBeautyBookings = PaginatedData<BeautyBooking>;
export type BeautyBookingModalState = ModalState<BeautyBooking>;

export interface BeautyBookingsIndexProps {
    beautybookings: PaginatedBeautyBookings;
    auth: AuthContext;
    beautyservices: any[];
    [key: string]: unknown;
}

export interface CreateBeautyBookingProps {
    onSuccess: () => void;
}

export interface EditBeautyBookingProps {
    beautybooking: BeautyBooking;
    onSuccess: () => void;
}

export interface BeautyBookingShowProps {
    beautybooking: BeautyBooking;
    [key: string]: unknown;
}