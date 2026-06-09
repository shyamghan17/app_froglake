import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface BulkSmsContact {
    id: number;
    name: string;
    email: any;
    mobile_no: string;
    city: string;
    state: string;
    zip_code: string;
    created_at: string;
}

export interface CreateBulkSmsContactFormData {
    name: string;
    email: any;
    mobile_no: string;
    city: string;
    state: string;
    zip_code: string;
}

export interface EditBulkSmsContactFormData {
    name: string;
    email: any;
    mobile_no: string;
    city: string;
    state: string;
    zip_code: string;
}

export interface BulkSmsContactFilters {
    name: string;
    email: string;
    mobile_no: string;
}

export type PaginatedBulkSmsContacts = PaginatedData<BulkSmsContact>;
export type BulkSmsContactModalState = ModalState<BulkSmsContact>;

export interface BulkSmsContactsIndexProps {
    bulksmscontacts: PaginatedBulkSmsContacts;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateBulkSmsContactProps {
    onSuccess: () => void;
}

export interface EditBulkSmsContactProps {
    bulksmscontact: BulkSmsContact;
    onSuccess: () => void;
}

export interface BulkSmsContactShowProps {
    bulksmscontact: BulkSmsContact;
    [key: string]: unknown;
}