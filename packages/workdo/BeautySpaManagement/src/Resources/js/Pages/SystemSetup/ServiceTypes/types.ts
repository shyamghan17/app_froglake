import { PaginatedData, ModalState, AuthContext, CreateProps, EditProps } from '@/types/common';

export interface ServiceType {
    id: number;
    name: string;
    created_at: string;
}

export interface ServiceTypeFormData {
    name: string;
}

export interface CreateServiceTypeProps extends CreateProps {
}

export interface EditServiceTypeProps extends EditProps<ServiceType> {
}

export type PaginatedServiceTypes = PaginatedData<ServiceType>;
export type ServiceTypeModalState = ModalState<ServiceType>;

export interface ServiceTypesIndexProps {
    servicetypes: PaginatedServiceTypes;
    auth: AuthContext;
    [key: string]: unknown;
}