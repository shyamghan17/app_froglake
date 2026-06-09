export interface Package {
    id: number;
    name: string;
    item_id: number;
    services: string;
    delivery_time: string;
    delivery_period: string;
    price: number;
    created_at: string;
    item: {
        name: string;
        sale_price: number;
        tax_ids: string;
    };
}

export interface Item {
    id: number;
    name: string;
    sale_price: number;
    tax_ids: string;
}

export interface ExtraService {
    id: number;
    name: string;
    price: number;
    description?: string;
}

export interface PackageFilters {
    name: string;
    item_id: string;
}

export interface PaginatedPackages {
    data: Package[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

export interface PackageFormData {
    name: string;
    item_id: string;
    services: number[];
    delivery_time: string;
    delivery_period: string;
    price: string;
}

export interface PackageModalState {
    isOpen: boolean;
    mode: 'create' | 'edit';
    data: Package | null;
}

export interface PackagesIndexProps {
    packages: PaginatedPackages;
    items: Item[];
    extraServices: ExtraService[];
    auth: {
        user?: {
            permissions?: string[];
        };
    };
}
