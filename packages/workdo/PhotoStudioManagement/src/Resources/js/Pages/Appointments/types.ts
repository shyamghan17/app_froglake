import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface PhotoStudioService {
    id: number;
    name: string;
    price: number;
}

export interface PhotoStudioTeamMember {
    id: number;
    user_id: number;
    user?: { id: number; name: string; avatar?: string };
}

export interface PhotoStudioAppointment {
    id: number;
    appointment_number: string;
    name: string;
    email: string;
    mobile_no: string;
    team_member_ids: string[];
    booking_start_date: string;
    booking_end_date: string;
    service_id: number;
    price: number;
    status: 'pending' | 'scheduled' | 'completed' | 'cancelled';
    payment_status: 'pending' | 'confirmed';
    service?: PhotoStudioService;
    created_at: string;
}

export interface AppointmentFormData {
    name: string;
    email: string;
    mobile_no: string;
    booking_start_date: string;
    booking_end_date: string;
    service_id: string;
    price: string;
}

export interface AppointmentFilters {
    search: string;
    status: string;
    payment_status: string;
    service_id: string;
}

export type PaginatedAppointments = PaginatedData<PhotoStudioAppointment>;
export type AppointmentModalState = ModalState<PhotoStudioAppointment>;

export interface AppointmentsIndexProps {
    appointments: PaginatedAppointments;
    services: PhotoStudioService[];
    teamMembers: PhotoStudioTeamMember[];
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateAppointmentProps {
    onClose: () => void;
    services: PhotoStudioService[];
    teamMembers: PhotoStudioTeamMember[];
}

export interface EditAppointmentProps {
    appointment: PhotoStudioAppointment;
    onClose: () => void;
    services: PhotoStudioService[];
    teamMembers: PhotoStudioTeamMember[];
}

export interface ViewAppointmentProps {
    appointment: PhotoStudioAppointment;
    teamMembers: PhotoStudioTeamMember[];
    onClose: () => void;
}

export interface AssignTeamMemberProps {
    appointment: PhotoStudioAppointment;
    teamMembers: PhotoStudioTeamMember[];
    onClose: () => void;
}
