import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface Lead {
    id: number;
    name: string;
    company_name?: string;
    email: any;
    subject: string;
    user_id?: number;
    pipeline_id?: number;
    stage_id?: number;
    category?: string;
    address?: string;
    district?: string;
    province?: string;
    remarks?: string;
    is_live?: boolean;
    company_pan?: string;
    lead_status?: string;
    sources?: string[] | string;
    products?: string[] | string;
    notes?: string;
    labels?: string;
    order?: number;
    phone?: string;
    website?: string;
    is_active: boolean;
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
    company_name?: string;
    email?: string;
    phone?: string;
    date?: string;
    website?: string;
    category?: string;
    address?: string;
    district?: string;
    province?: string;
    remarks?: string;
    is_live?: boolean;
    company_pan?: string;
    lead_status?: string;
    pipeline_id?: string;
    stage_id?: string;
    sources?: string[];
    products?: string[];
    notes?: string;
}

export interface CreateLeadFormData {
    subject: string;
    user_id: string;
    name: string;
    company_name?: string;
    email: string;
    phone: string;
    date: string;
    website?: string;
    category?: string;
    address?: string;
    district?: string;
    province?: string;
    remarks?: string;
    is_live?: boolean;
    company_pan?: string;
    lead_status?: string;
}

export interface EditLeadFormData {
    subject: string;
    user_id: string;
    name: string;
    company_name?: string;
    email: string;
    phone: string;
    date: string;
    website?: string;
    category?: string;
    address?: string;
    district?: string;
    province?: string;
    remarks?: string;
    is_live?: boolean;
    company_pan?: string;
    lead_status?: string;
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
    category: string;
    lead_status: string;
    is_live: string;
    user_id: string;
    pipeline_id: string;
    stage_id: string;
}

export type PaginatedLeads = PaginatedData<Lead>;
export type LeadModalState = ModalState<Lead>;

export interface LeadsIndexProps {
    leads: PaginatedLeads;
    auth: AuthContext;
    users: any[];
    filterCategories?: string[];
    filterLeadStatuses?: string[];
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
