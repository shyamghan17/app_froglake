import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface Notice {
    id: number;
    title: string;
    description: string;
    start_date: string;
    expiry_date?: string;
    is_pinned: boolean;
    priority: 'normal' | 'urgent' | 'critical';
    require_acknowledgment: boolean;
    target_type: 'all' | 'department' | 'role' | 'specific_users';
    target_ids?: number[];
    target_names?: string[];
    allow_comments: boolean;
    status: 'draft' | 'published' | 'deactivated';
    created_at: string;
    creator_id: number;
    creator?: { name: string };
    attachments?: string[];
    comments?: { id: number; comment: string; created_at: string; user?: { name: string } }[];
    reads_count?: number;
    comments_count?: number;
}

export interface CreateNoticeFormData {
    title: string;
    description: string;
    attachments: string[];
    start_date: string;
    expiry_date: string;
    priority: string;
    require_acknowledgment: boolean;
    target_type: 'all' | 'department' | 'role' | 'specific_users';
    target_ids: number[];
    allow_comments: boolean;
}

export interface EditNoticeFormData {
    title: string;
    description: string;
    attachments: string[];
    start_date: string;
    expiry_date: string;
    priority: string;
    require_acknowledgment: boolean;
    target_type: 'all' | 'department' | 'role' | 'specific_users';
    target_ids: number[];
    allow_comments: boolean;
}

export interface NoticeFilters {
    title: string;
    priority: string;
    target_type: string;
    status: string;
}

export type PaginatedNotices = PaginatedData<Notice>;
export type NoticeModalState = ModalState<Notice>;

export interface NoticesIndexProps {
    notices: PaginatedNotices;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateNoticeProps {
    onSuccess: () => void;
}

export interface EditNoticeProps {
    notice: Notice;
    onSuccess: () => void;
}

export interface NoticeShowProps {
    notice: Notice;
    readStats: {
        read: { user: { id: number; name: string }; read_at: string; acknowledged_at: string | null }[];
        unread: { id: number; name: string }[];
    } | null;
    comments: NoticeComment[];
    userAcknowledged: boolean;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface NoticeBoardProps {
    notices: Notice[];
    draftNotices: Notice[];
    readNoticeIds: number[];
    acknowledgedNoticeIds: number[];
    auth: AuthContext;
    [key: string]: unknown;
}

export interface NoticeComment {
    id: number;
    notice_id: number;
    user_id: number;
    creator_id: number;
    parent_id: number | null;
    comment: string;
    created_at: string;
    user?: { id: number; name: string };
    replies?: NoticeComment[];
}
