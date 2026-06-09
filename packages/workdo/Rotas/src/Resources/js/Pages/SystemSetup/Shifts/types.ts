import { PaginatedData, ModalState, AuthContext, CreateProps, EditProps } from '@/types/common';

export interface Shift {
    id: number;
    shift_name: string;
    start_time: string;
    end_time: string;
    break_start_time: string;
    break_end_time: string;
    is_night_shift: boolean;
    creator_id: number;
    created_by: number;
    created_at: string;
    creator?: {
        id: number;
        name: string;
    };
}

export interface ShiftFormData {
    shift_name: string;
    start_time: string;
    end_time: string;
    break_start_time: string;
    break_end_time: string;
    is_night_shift: boolean;
}

export interface CreateShiftProps extends CreateProps {
}

export interface EditShiftProps extends EditProps<Shift> {
}

export type PaginatedShifts = PaginatedData<Shift>;
export type ShiftModalState = ModalState<Shift>;

export interface ShiftsIndexProps {
    shifts: PaginatedShifts;
    users: Array<{ id: number; name: string }>;
    auth: AuthContext;
    [key: string]: unknown;
}