import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface SingleSms {
    id: number;
    contact_id: number;
    mobile_number: string;
    status: boolean;
    sms: string;
    created_at: string;
}

export interface CreateSingleSmsFormData {
    contact_id: string;
    mobile_number: string;
    status: boolean;
    sms: string;
}

export interface EditSingleSmsFormData {
    contact_id: string;
    mobile_number: string;
    status: boolean;
    sms: string;
}

export interface SingleSmsFilters {
    search: string;
    status: string;
    contact_id: string;
}

export type PaginatedSingleSms = PaginatedData<SingleSms>;
export type SingleSmsModalState = ModalState<SingleSms>;

export interface SingleSmsIndexProps {
    singlesms: PaginatedSingleSms;
    auth: AuthContext;
    bulksmscontacts: any[];
    [key: string]: unknown;
}

export interface CreateSingleSmsProps {
    onSuccess: () => void;
}

export interface EditSingleSmsProps {
    singlesms: SingleSms;
    onSuccess: () => void;
}

export interface SingleSmsShowProps {
    singlesms: SingleSms;
    [key: string]: unknown;
}