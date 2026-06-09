export interface Availability {
    id: number;
    employee_id: number;
    name: string;
    start_date: string;
    end_date: string;
    availability: any[];
    creator_id: number;
    created_by: number;
    created_at: string;
    updated_at: string;
    employee?: {
        id: number;
        name: string;
    };
    creator?: {
        id: number;
        name: string;
    };
}

export interface AvailabilitiesIndexProps {
    availabilities: {
        data: Availability[];
        links: any[];
        meta: any;
    };
    employees: Array<{
        id: number;
        name: string;
    }>;
    currentUserEmployee?: {
        id: number;
        name: string;
    } | null;
    isNotEmpType: boolean;
    auth: {
        user: {
            permissions: string[];
        };
    };
}

export interface AvailabilityFilters {
    search: string;
    employee_id: string;
}

export interface AvailabilityModalState {
    isOpen: boolean;
    mode: string;
    data: Availability | null;
}

export interface CreateAvailabilityProps {
    onSuccess: () => void;
}

export interface EditAvailabilityProps {
    availability: Availability;
    onSuccess: () => void;
}

export interface CreateAvailabilityFormData {
    employee_id: string;
    name: string;
    start_date: string;
    end_date: string;
    availability: any[];
}

export interface UpdateAvailabilityFormData extends CreateAvailabilityFormData {}