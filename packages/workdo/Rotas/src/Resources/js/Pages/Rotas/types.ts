import { PaginatedData, ModalState, AuthContext, CreateProps, EditProps } from '@/types/common';

export interface Rota {
    id: number;
    title: string;
    start_date: string;
    end_date: string;
    branch_id?: number;
    department_id?: number;
    designation_id?: number;
    is_published: boolean;
    notes?: string;
    schedule_data: any;
    creator_id: number;
    created_by: number;
    created_at: string;
    branch?: { id: number; branch_name: string };
    department?: { id: number; department_name: string };
    designation?: { id: number; designation_name: string };
    creator?: { id: number; name: string };
    total_working_hours?: number;
}

export interface RotaFormData {
    title: string;
    start_date: string;
    end_date: string;
    branch_id?: number;
    department_id?: number;
    designation_id?: number;
    notes?: string;
    schedule_data: any;
    is_published: boolean;
}

export interface CreateRotaProps extends CreateProps {
}

export interface EditRotaProps extends EditProps<Rota> {
}

export type PaginatedRotas = PaginatedData<Rota>;
export type RotaModalState = ModalState<Rota>;

export interface RotasIndexProps {
    branches: Array<{ id: number; branch_name: string }>;
    departments: Array<{ id: number; department_name: string }>;
    designations: Array<{ id: number; designation_name: string }>;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface Employee {
    id: number;
    user_id: number;
    user: { id: number; name: string; avatar?: string };
    branch_id?: number;
    department_id?: number;
    designation_id?: number;
    shift?: { id: number; shift_name: string; start_time: string; end_time: string };
    basic_salary?: number;
    rate_per_hour?: number;
    work_schedule?: Array<{ day: string; is_working: boolean }>;
    weekSchedule?: Array<{ date: string; shifts?: any[] }>;
}

export interface Shift {
    id: number;
    shift_name: string;
    start_time: string;
    end_time: string;
    break_start_time: string;
    break_end_time: string;
}