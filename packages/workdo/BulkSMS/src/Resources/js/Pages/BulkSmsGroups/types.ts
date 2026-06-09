import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface BulkSmsGroup {
    id: number;
    name: string;
    contacts?: string[];
    created_at: string;
}

export interface CreateBulkSmsGroupFormData {
    name: string;
    contacts: string[];
}

export interface EditBulkSmsGroupFormData {
    name: string;
    contacts: string[];
}

export interface BulkSmsGroupFilters {
    name: string;
}

export type PaginatedBulkSmsGroups = PaginatedData<BulkSmsGroup>;
export type BulkSmsGroupModalState = ModalState<BulkSmsGroup>;

export interface BulkSmsGroupsIndexProps {
    bulksmsgroups: PaginatedBulkSmsGroups;
    auth: AuthContext;
    bulksmscontacts: any[];
    [key: string]: unknown;
}

export interface CreateBulkSmsGroupProps {
    onSuccess: () => void;
}

export interface EditBulkSmsGroupProps {
    bulksmsgroup: BulkSmsGroup;
    onSuccess: () => void;
}

export interface BulkSmsGroupShowProps {
    bulksmsgroup: BulkSmsGroup;
    [key: string]: unknown;
}