import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface PortfolioCategory {
    id: number;
    name: string;
    description?: string;
    is_active: boolean;
    created_at: string;
}

export interface CreatePortfolioCategoryFormData {
    name: string;
    description: string;
    is_active: boolean;
}

export interface EditPortfolioCategoryFormData {
    name: string;
    description: string;
    is_active: boolean;
}

export interface PortfolioCategoryFilters {
    name: string;
    is_active: string;
}

export type PaginatedPortfolioCategories = PaginatedData<PortfolioCategory>;
export type PortfolioCategoryModalState = ModalState<PortfolioCategory>;

export interface PortfolioCategoriesIndexProps {
    portfoliocategories: PaginatedPortfolioCategories;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreatePortfolioCategoryProps {
    onSuccess: () => void;
}

export interface EditPortfolioCategoryProps {
    portfoliocategory: PortfolioCategory;
    onSuccess: () => void;
}

export interface PortfolioCategoryShowProps {
    portfoliocategory: PortfolioCategory;
    [key: string]: unknown;
}
