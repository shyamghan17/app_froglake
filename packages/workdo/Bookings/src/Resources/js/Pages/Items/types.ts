import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Item {
    id: number;
    name: string;
    sku?: string;
    category_id?: number;
    description?: string;
    sale_price?: number;
    purchase_price?: number;
    unit?: number;
    image?: string;
    created_at: string;
    category?: {
        name: string;
    };
    unit_relation?: {
        unit_name: string;
    };
    type?: string;
    total_quantity?: number;
}

export interface Category {
    id: number;
    name: string;
    type?: string;
}

export interface ItemFilters {
    name: string;
    type: string;
    category_id: string;
}

export type PaginatedItems = PaginatedData<Item>;
export type ItemModalState = ModalState<Item>;

export interface ItemsIndexProps {
    items: PaginatedItems;
    categories: Category[];
    auth: AuthContext;
    [key: string]: unknown;
}
