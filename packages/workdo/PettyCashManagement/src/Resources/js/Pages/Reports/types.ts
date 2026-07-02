import { AuthContext, PaginatedData } from '@/types/common';

export type PettyCashReportExpense = any;

export interface PettyCashReportTotals {
    count: number;
    total_amount: string;
    pettycash_amount: string;
    reimbursement_amount: string;
}

export interface PettyCashReportFilters {
    start_date: string;
    end_date: string;
    user_id: string;
    category_id: string;
    type: string;
    status: string;
}

export interface PettyCashReportPageProps {
    auth: AuthContext;
    expenses: PaginatedData<PettyCashReportExpense>;
    totals: PettyCashReportTotals;
    users: Array<{ id: number; name: string }>;
    categories: Array<{ id: number; name: string }>;
    filters: PettyCashReportFilters;
    [key: string]: unknown;
}

export interface PettyCashReportPrintPageProps {
    auth: AuthContext;
    expenses: PettyCashReportExpense[];
    totals: PettyCashReportTotals;
    filters: PettyCashReportFilters;
    [key: string]: unknown;
}
