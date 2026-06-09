export interface ExtraService {
    id: number;
    name: string;
    status: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface ExtraServicesIndexProps {
    extraservices: ExtraService[];
    auth: {
        user: {
            permissions?: string[];
        };
    };
}

export interface ExtraServiceModalState {
    isOpen: boolean;
    mode: 'add' | 'edit' | '';
    data: ExtraService | null;
}

export interface CreateExtraServiceFormData {
    name: string;
    status: boolean;
}

export interface EditExtraServiceFormData {
    name: string;
    status: boolean;
}

export interface CreateExtraServiceProps {
    onSuccess: () => void;
}

export interface EditExtraServiceProps {
    extraservice: ExtraService;
    onSuccess: () => void;
}
