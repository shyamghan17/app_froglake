import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Specification {
    field_name: string;
    description: string;
}

export interface PhotoStudioCameraKit {
    id: number;
    name: string;
    image: string;
    description: string;
    tags: string[];
    specifications: Specification[];
    equipment_type_id: number;
    equipment_type?: { id: number; name: string };
    status: 'available' | 'unavailable';
    creator_id?: number;
    created_by: number;
    created_at: string;
    updated_at: string;
}

export interface CameraKitFormData {
    name: string;
    image: string;
    description: string;
    tags: string[];
    specifications: Specification[];
    equipment_type_id: string;
    status: 'available' | 'unavailable';
}

export type PaginatedCameraKits = PaginatedData<PhotoStudioCameraKit>;
export type CameraKitModalState = ModalState<PhotoStudioCameraKit>;

export interface CameraKitFilters {
    search: string;
    status: string;
    equipment_type_id: string;
}

export interface CameraKitsIndexProps {
    cameraKits: PaginatedCameraKits;
    equipmentTags: Array<{ id: number; name: string }>;
    equipmentTypes: Array<{ id: number; name: string }>;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateCameraKitProps {
    onClose: () => void;
    equipmentTags: Array<{ id: number; name: string }>;
    equipmentTypes: Array<{ id: number; name: string }>;
}

export interface EditCameraKitProps {
    cameraKit: PhotoStudioCameraKit;
    onClose: () => void;
    equipmentTags: Array<{ id: number; name: string }>;
    equipmentTypes: Array<{ id: number; name: string }>;
}
