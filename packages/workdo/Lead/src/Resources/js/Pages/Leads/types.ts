import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface LeadUserOption {
    id: number;
    name: string;
}

export interface LeadSourceOption {
    id: number;
    name: string;
}
export interface Lead {
    id: number;
    name: string;
    email: any;
    subject: string;
    user_id?: number;
    pipeline_id?: number;
    stage_id?: number;
    sources?: string[] | string;
    products?: string[] | string;
    notes?: string;
    labels?: string;
    order?: number;
    phone?: string;
    designation?: string;
    company_name?: string;
    pan_vat_number?: string;
    organization_type?: string;
    whatsapp_same_as_phone?: boolean;
    whatsapp_viber_number?: string;
    address_line_1?: string;
    address_line_2?: string;
    city?: string;
    state?: string;
    country?: string;
    postal_code?: string;
    is_active: boolean;
    is_converted?: number;
    date?: string;
    creator_id?: number;
    created_at: string;
    additional_images?: string[] | string;
    stage?: any;
    user?: any;
    user_leads?: any[];
    tasks?: any[];
    emails?: any[];
    discussions?: any[];
    calls?: any[];
    activities?: any[];
}

export interface LeadFormData {
    subject?: string;
    user_id?: string;
    name?: string;
    email?: string;
    phone?: string;
    date?: string;
    pipeline_id?: string;
    stage_id?: string;
    sources?: string[];
    products?: string[];
    notes?: string;
    designation?: string;
    company_name?: string;
    pan_vat_number?: string;
    organization_type?: string;
    whatsapp_same_as_phone?: boolean;
    whatsapp_viber_number?: string;
    address_line_1?: string;
    address_line_2?: string;
    city?: string;
    state?: string;
    country?: string;
    postal_code?: string;
}

export interface CreateLeadFormData {
    subject: string;
    user_id: string;
    name: string;
    email: string;
    phone: string;
    designation: string;
    company_name: string;
    pan_vat_number: string;
    organization_type: string;
    whatsapp_same_as_phone: boolean;
    whatsapp_viber_number: string;
    address_line_1: string;
    address_line_2: string;
    city: string;
    state: string;
    country: string;
    postal_code: string;
    sources: string[];
    date: string;
}

export interface EditLeadFormData {
    subject: string;
    user_id: string;
    name: string;
    email: string;
    phone: string;
    date: string;
    pipeline_id: string;
    stage_id: string;
    sources: string[];
    products: string[];
    notes: string;
}

export interface LeadFilters {
    name: string;
    email: string;
    subject: string;
    is_active: string;
    user_id: string;
    pipeline_id: string;
    stage_id: string;
    date_range: string;
}

export type PaginatedLeads = PaginatedData<Lead>;
export type LeadModalState = ModalState<Lead>;

export interface LeadsIndexProps {
    leads: PaginatedLeads;
    auth: AuthContext;
    users: LeadUserOption[];
    sources: LeadSourceOption[];
    currentPipelineId?: number | null;
    [key: string]: unknown;
}

export interface CreateLeadProps {
    onSuccess: () => void;
}

export interface EditLeadProps {
    lead: Lead;
    onSuccess: () => void;
}

export interface LeadShowProps {
    lead: Lead;
    [key: string]: unknown;
}
