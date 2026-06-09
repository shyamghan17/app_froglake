export interface CustomPage {
    id: number;
    title: string;
    slug: string;
    description: string;
    contents: string;
    is_editable: boolean;
    enable_page_footer: string;
}

export interface CustomPagesProps {
    customPages: CustomPage[];
    auth: any;
}

export interface ModalState {
    isOpen: boolean;
    mode: 'add' | 'edit' | '';
    data: CustomPage | null;
}

export interface CreateProps {
    onSuccess: () => void;
}

export interface EditProps {
    customPage: CustomPage;
    onSuccess: () => void;
}
