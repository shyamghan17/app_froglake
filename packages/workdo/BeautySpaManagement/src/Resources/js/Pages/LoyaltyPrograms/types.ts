import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface BeautyLoyaltyProgram {
    id: number;
    customer_name: string;
    points_earned: number;
    points_redeemed?: number;
    last_updated: string;
    created_at: string;
}

export interface CreateBeautyLoyaltyProgramFormData {
    customer_name: string;
    points_earned: string;
    points_redeemed: string;
    last_updated: string;
}

export interface EditBeautyLoyaltyProgramFormData {
    customer_name: string;
    points_earned: string;
    points_redeemed: string;
    last_updated: string;
}

export interface BeautyLoyaltyProgramFilters {
    customer_name: string;
}

export type PaginatedBeautyLoyaltyPrograms = PaginatedData<BeautyLoyaltyProgram>;
export type BeautyLoyaltyProgramModalState = ModalState<BeautyLoyaltyProgram>;

export interface BeautyLoyaltyProgramsIndexProps {
    beautyloyaltyprograms: PaginatedBeautyLoyaltyPrograms;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateBeautyLoyaltyProgramProps {
    onSuccess: () => void;
}

export interface EditBeautyLoyaltyProgramProps {
    beautyloyaltyprogram: BeautyLoyaltyProgram;
    onSuccess: () => void;
}

export interface BeautyLoyaltyProgramShowProps {
    beautyloyaltyprogram: BeautyLoyaltyProgram;
    [key: string]: unknown;
}