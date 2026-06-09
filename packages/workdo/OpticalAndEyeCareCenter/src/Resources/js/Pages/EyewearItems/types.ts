import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface EyewearItem {
    id: number;
    product_id?: number;
    customization_details?: string;
    created_at: string;
}

export interface CreateEyewearItemFormData {
    product_id: string;
    customization_details: string;
}

export interface EditEyewearItemFormData {
    product_id: string;
    customization_details: string;
}

export interface EyewearItemFilters {

}

export type PaginatedEyewearItems = PaginatedData<EyewearItem>;
export type EyewearItemModalState = ModalState<EyewearItem>;

export interface EyewearItemsIndexProps {
    eyewearitems: PaginatedEyewearItems;
    auth: AuthContext;
    products: any[];
    [key: string]: unknown;
}

export interface CreateEyewearItemProps {
    products: any[];
}

export interface EditEyewearItemProps {
    eyewearitem: EyewearItem;
    onSuccess: () => void;
}

export interface EyewearItemShowProps {
    eyewearitem: EyewearItem;
    [key: string]: unknown;
}