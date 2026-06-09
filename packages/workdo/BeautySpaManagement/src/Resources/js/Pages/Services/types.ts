import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface BeautyServiceType {
    id: number;
    name: string;
}

export interface Service {
    id: number;
    name: string;
    max_bookable_persons: number;
    price: number;
    time: any;
    description?: string;
    service_image?: string;
    service_type_id?: number;
    staff_id?: number;
    service_type?: BeautyServiceType;
    staff?: any;
    included_services?: string[];
    created_at: string;
}

export interface CreateServiceFormData {
    name: string;
    service_type_id: string;
    price: string;
    max_bookable_persons: string;
    time: any;
    staff_id: string;
    service_image: string;
    description: string;
    included_services: string[];
}

export interface EditServiceFormData {
    name: string;
    service_type_id: string;
    price: string;
    max_bookable_persons: string;
    time: any;
    staff_id: string;
    service_image: string;
    description: string;
    included_services: string[];
}

export interface ServiceFilters {
    name: string;
    description: string;
    service_type_id: string;
}

export type PaginatedServices = PaginatedData<Service>;
export type ServiceModalState = ModalState<Service>;

export interface ServicesIndexProps {
    services: PaginatedServices;
    auth: AuthContext;
    beautyservicetypes: any[];
    staff: any[];
    [key: string]: unknown;
}

export interface CreateServiceProps {
    onSuccess: () => void;
}

export interface EditServiceProps {
    service: Service;
    onSuccess: () => void;
}

export interface ServiceShowProps {
    service: Service;
    [key: string]: unknown;
}