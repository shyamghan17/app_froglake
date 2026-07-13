import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface SuggestionBoxSuggestion {
    id: number;
    name: string;
}

export interface User {
    id: number;
    name: string;
}

export interface SuggestionStatusHistory {
    id: number;
    old_status: boolean;
    new_status: boolean;
    comment?: string;
    suggestion_id?: number;
    suggestion?: SuggestionBoxSuggestion;
    changed_by?: number;
    changedBy?: User;
    created_at: string;
}

export interface CreateSuggestionStatusHistoryFormData {
    old_status: boolean;
    new_status: boolean;
    comment: string;
    suggestion_id: string;
    changed_by: string;
}

export interface EditSuggestionStatusHistoryFormData {
    old_status: boolean;
    new_status: boolean;
    comment: string;
    suggestion_id: string;
    changed_by: string;
}

export interface SuggestionStatusHistoryFilters {
    comment: string;
    suggestion_id: string;
    changed_by: string;
    old_status: string;
    new_status: string;
}

export type PaginatedSuggestionStatusHistories = PaginatedData<SuggestionStatusHistory>;
export type SuggestionStatusHistoryModalState = ModalState<SuggestionStatusHistory>;

export interface SuggestionStatusHistoriesIndexProps {
    suggestionstatushistories: PaginatedSuggestionStatusHistories;
    auth: AuthContext;
    suggestionboxsuggestions: any[];
    users: any[];
    [key: string]: unknown;
}

export interface CreateSuggestionStatusHistoryProps {
    onSuccess: () => void;
}

export interface EditSuggestionStatusHistoryProps {
    suggestionstatushistory: SuggestionStatusHistory;
    onSuccess: () => void;
}

export interface SuggestionStatusHistoryShowProps {
    suggestionstatushistory: SuggestionStatusHistory;
    [key: string]: unknown;
}