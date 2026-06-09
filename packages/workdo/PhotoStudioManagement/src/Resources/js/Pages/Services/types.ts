import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface PhotoStudioService {
    id: number;
    name: string;
    service_category_ids: string[];
    description?: string;
    image?: string;
    price: number;
    status: boolean;
    camera_kit_ids: string[];
    creator_id?: number;
    created_by: number;
    created_at: string;
    updated_at: string;
}

export interface ServiceFormData {
    name: string;
    service_category_ids: string[];
    description: string;
    image: string;
    price: string;
    status: boolean;
    camera_kit_ids: string[];
}

export type PaginatedServices = PaginatedData<PhotoStudioService>;
export type ServiceModalState = ModalState<PhotoStudioService>;

export interface ServiceFilters {
    search: string;
    status: string;
    category_id: string;
}

export interface ServicesIndexProps {
    services: PaginatedServices;
    serviceCategories: Array<{ id: number; name: string }>;
    cameraKits: Array<{ id: number; name: string }>;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateServiceProps {
    onClose: () => void;
    serviceCategories: Array<{ id: number; name: string }>;
    cameraKits: Array<{ id: number; name: string }>;
}

export interface EditServiceProps {
    service: PhotoStudioService;
    onClose: () => void;
    serviceCategories: Array<{ id: number; name: string }>;
    cameraKits: Array<{ id: number; name: string }>;
}

export interface ViewServiceProps {
    service: PhotoStudioService;
    serviceCategories: Array<{ id: number; name: string }>;
    cameraKits: Array<{ id: number; name: string }>;
    onClose: () => void;
}
