import { AuthContext, PaginatedData } from '@/types/common';

export interface PettyCashReconciliation {
    id: number;
    period_start: string;
    period_end: string;
    opening_balance: string;
    additions_total: string;
    expenses_total: string;
    expected_closing: string;
    counted_cash: string;
    variance: string;
    locked: boolean;
    created_at: string;
}

export interface PettyCashReconciliationFilters {
    period_start: string;
    period_end: string;
    locked: string;
}

export interface ReconciliationsIndexProps {
    auth: AuthContext;
    reconciliations: PaginatedData<PettyCashReconciliation>;
    filters: PettyCashReconciliationFilters;
    [key: string]: unknown;
}

export interface ReconciliationShowProps {
    auth: AuthContext;
    reconciliation: PettyCashReconciliation;
    [key: string]: unknown;
}

export interface ReconciliationCreateFormData {
    period_start: string;
    period_end: string;
    counted_cash: string;
    locked: boolean;
}

