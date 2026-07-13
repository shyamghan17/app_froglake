export interface Suggestion {
    id: number;
    title: string;
    description: string;
    category?: { 
        id: number; 
        name: string; 
        color: string; 
    };
    user?: { 
        id: number; 
        name: string; 
        avatar?: string; 
    };
    status: string;
    votes_count: number;
    views_count: number;
    is_anonymous: boolean;
    created_at: string;
    admin_response?: string;
    responded_by?: { name: string };
    responded_at?: string;
    has_voted?: boolean;
    voters?: string[];
    viewers?: string[];
}

export interface Category {
    id: number;
    name: string;
    color: string;
    description?: string;
    is_active: boolean;
    display_order: number;
}

export interface SuggestionFilters {
    name: string;
    status: string;
    category_id: string;
    date_range: string;
}

import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export type PaginatedSuggestions = PaginatedData<Suggestion>;
export type SuggestionModalState = ModalState<Suggestion>;

export interface SuggestionsIndexProps {
    suggestions: PaginatedSuggestions;
    categories: Category[];
    users: any[];
    auth: AuthContext;
    stats?: {
        total: number;
        new: number;
        under_review: number;
        accepted: number;
        rejected: number;
    };
    [key: string]: unknown;
}



export interface CreateSuggestionFormData {
    title: string;
    category_id: string;
    description: string;
    is_anonymous: boolean;
}

export interface CreateSuggestionProps {
    onSuccess: () => void;
}

export interface EditSuggestionProps {
    onSuccess: () => void;
    suggestion: Suggestion;
}
