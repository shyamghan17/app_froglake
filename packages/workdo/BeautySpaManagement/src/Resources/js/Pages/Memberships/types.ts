import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface MembershipType {
    id: number;
    name: string;
}

export interface BeautyService {
    id: number;
    name: string;
}

export interface BeautyMembership {
    id: number;
    name: string;
    duration: number;
    benefits?: string;
    price: number;
    description?: string;
    included_services_id?: number;
    included_services?: BeautyService;
    created_at: string;
}

export interface CreateBeautyMembershipFormData {
    name: string;
    duration: string;
    benefits: string;
    price: string;
    description: string;
    included_services_id: string;
}

export interface EditBeautyMembershipFormData {
    name: string;
    duration: string;
    benefits: string;
    price: string;
    description: string;
    included_services_id: string;
}

export interface BeautyMembershipFilters {
    name: string;
    description: string;
}

export type PaginatedBeautyMemberships = PaginatedData<BeautyMembership>;
export type BeautyMembershipModalState = ModalState<BeautyMembership>;

export interface BeautyMembershipsIndexProps {
    beautymemberships: PaginatedBeautyMemberships;
    auth: AuthContext;
    beautyservices: any[];
    [key: string]: unknown;
}

export interface CreateBeautyMembershipProps {
    onSuccess: () => void;
}

export interface EditBeautyMembershipProps {
    beautymembership: BeautyMembership;
    onSuccess: () => void;
}

export interface BeautyMembershipShowProps {
    beautymembership: BeautyMembership;
    [key: string]: unknown;
}