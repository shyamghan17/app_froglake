import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface BulksmsGroup {
    id: number;
    name: string;
    created_at: string;
}

export interface CreateBulksmsGroupFormData {
    name: string;
}

export interface BulksmsGroupFilters {
    search: string;
}

export type PaginatedBulksmsGroup = PaginatedData<BulksmsGroup>;
export type BulksmsGroupModalState = ModalState<BulksmsGroup>;

export interface BulksmsGroupIndexProps {
    bulksmsgroups: PaginatedBulksmsGroup;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateBulksmsGroupProps {
    onSuccess: () => void;
}

export interface BulksmsGroupShowProps {
    bulksmsgroup: BulksmsGroup;
    [key: string]: unknown;
}