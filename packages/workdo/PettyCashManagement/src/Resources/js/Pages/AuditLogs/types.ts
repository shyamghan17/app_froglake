import { AuthContext, PaginatedData } from '@/types/common';

export interface PettyCashAuditLogActor {
    id: number;
    name: string;
}

export interface PettyCashAuditLog {
    id: number;
    action: string;
    subject_type: string | null;
    subject_id: number | null;
    actor: PettyCashAuditLogActor | null;
    meta: Record<string, unknown> | null;
    created_at: string;
}

export interface PettyCashAuditLogFilters {
    action: string;
    subject_type: string;
    subject_id: string;
}

export interface PettyCashAuditLogsIndexProps {
    auth: AuthContext;
    auditLogs: PaginatedData<PettyCashAuditLog>;
    subjectTypes: string[];
    filters: PettyCashAuditLogFilters;
    [key: string]: unknown;
}

