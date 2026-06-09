export interface CustomPage {
    id: number;
    title: string;
    slug: string;
    page_header?: string;
    page_header_description?: string;
    content: string;
    is_active: boolean;
    is_editable: boolean;
    created_at: string;
    updated_at: string;
}
