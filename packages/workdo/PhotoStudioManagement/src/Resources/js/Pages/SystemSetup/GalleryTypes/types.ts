export interface PhotoStudioGalleryType {
    id: number;
    name: string;
    description?: string;
    status: boolean;
    creator_id?: number;
    created_by: number;
    created_at: string;
    updated_at: string;
}

export interface GalleryTypesIndexProps {
    galleryTypes: {
        data: PhotoStudioGalleryType[];
        links: any[];
        meta: any;
    };
    auth: {
        user: {
            permissions: string[];
        };
    };
}
