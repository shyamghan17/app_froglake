import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface PhotoStudioTeamMember {
    id: number;
    user_id: number;
    user?: { id: number; name: string; email?: string; mobile_no?: string; avatar?: string };
    designation: string;
    experience_year: number;
    skills?: string;
    rate_per_hour?: number;
    is_active: boolean;
    bio?: string;
    created_at: string;
}

export interface CreateTeamMemberFormData {
    user_id: string;
    designation: string;
    experience_year: string;
    skills: string;
    rate_per_hour: string;
    is_active: boolean;
    bio: string;
}

export interface EditTeamMemberFormData {
    user_id: string;
    designation: string;
    experience_year: string;
    skills: string;
    rate_per_hour: string;
    is_active: boolean;
    bio: string;
}

export interface TeamMemberFilters {
    search: string;
    is_active: string;
}

export type PaginatedTeamMembers = PaginatedData<PhotoStudioTeamMember>;
export type TeamMemberModalState = ModalState<PhotoStudioTeamMember>;

export interface TeamMembersIndexProps {
    teamMembers: PaginatedTeamMembers;
    auth: AuthContext;
    users: Array<{ id: number; name: string; email?: string }>;
    [key: string]: unknown;
}

export interface CreateTeamMemberProps {
    onClose: () => void;
    users: Array<{ id: number; name: string; email?: string }>;
}

export interface EditTeamMemberProps {
    teamMember: PhotoStudioTeamMember;
    onClose: () => void;
    users: Array<{ id: number; name: string; email?: string }>;
}

export interface TeamMemberViewProps {
    teamMember: PhotoStudioTeamMember;
    onClose: () => void;
}
