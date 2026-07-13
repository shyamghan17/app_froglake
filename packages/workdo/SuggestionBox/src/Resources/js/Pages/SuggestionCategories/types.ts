import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface SuggestionCategory {
    id: number;
    name: string;
    color: any;
    description?: string;
    is_active: boolean;
    display_order?: number;
    created_at: string;
}

export interface CreateSuggestionCategoryFormData {
    name: string;
    color: any;
    description: string;
    is_active: boolean;
    display_order: string;
}

export interface EditSuggestionCategoryFormData {
    name: string;
    color: any;
    description: string;
    is_active: boolean;
    display_order: string;
}

export interface SuggestionCategoryFilters {
    name: string;
    description: string;
    is_active: string;
}

export type PaginatedSuggestionCategories = PaginatedData<SuggestionCategory>;
export type SuggestionCategoryModalState = ModalState<SuggestionCategory>;

export interface SuggestionCategoriesIndexProps {
    suggestioncategories: PaginatedSuggestionCategories;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateSuggestionCategoryProps {
    onSuccess: () => void;
}

export interface EditSuggestionCategoryProps {
    suggestioncategory: SuggestionCategory;
    onSuccess: () => void;
}