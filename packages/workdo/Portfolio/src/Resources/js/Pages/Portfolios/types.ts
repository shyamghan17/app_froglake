import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface PortfolioCategory {
    id: number;
    name: string;
}

export interface Portfolio {
    id: number;
    slug: string;

    // Personal Information
    name?: string;
    email?: string;
    role?: string;
    experience_years?: string;
    photo?: string;
    education?: string;

    // Work Details
    title: string;
    description?: string;
    category_id?: string;
    client?: string;
    live_url?: string;
    repository_url?: string;
    skills?: any;
    duration?: string;
    team_size?: number;
    start_date?: string;
    end_date?: string;
    budget?: string;
    industry?: string;

    // Overview
    show_overview: boolean;
    overview?: string;

    // Gallery
    show_gallery: boolean;
    images?: any;
    video_link?: string;

    // Contact Section
    show_contact: boolean;
    contact_heading?: string;
    contact_message?: string;

    creator_id?: number;
    created_by?: number;
    created_at: string;
    updated_at?: string;
}

export interface CreatePortfolioFormData {
    // Personal Information
    photo: string;
    name: string;
    role: string;
    experience_years: string;
    email: string;
    education: string;

    // Work Details
    category_id: string;
    title: string;
    description: string;
    live_url: string;
    repository_url: string;
    skills: any;
    client: string;
    duration: string;
    team_size: string;
    start_date: string;
    end_date: string;
    budget: string;
    industry: string;

    // Work Overview
    overview: string;
    show_overview: boolean;

    // Gallery
    images: string[];
    video_link: string;
    show_gallery: boolean;

    // Contact Section
    contact_heading: string;
    contact_message: string;
    show_contact: boolean;

    // Custom Sections
    custom_sections: any[];
}

export interface EditPortfolioFormData {
    // Personal Information
    photo: string;
    name: string;
    role: string;
    experience_years: string;
    email: string;
    education: string;

    // Work Details
    category_id: string;
    title: string;
    description: string;
    live_url: string;
    repository_url: string;
    skills: any;
    client: string;
    duration: string;
    team_size: string;
    start_date: string;
    end_date: string;
    budget: string;
    industry: string;

    // Work Overview
    overview: string;
    show_overview: boolean;

    // Gallery
    images: string[];
    video_link: string;
    show_gallery: boolean;

    // Contact Section
    contact_heading: string;
    contact_message: string;
    show_contact: boolean;

    // Custom Sections
    custom_sections: any[];
}

export interface PortfolioFilters {
    title: string;
    category_id: string;
}

export type PaginatedPortfolios = PaginatedData<Portfolio>;
export type PortfolioModalState = ModalState<Portfolio>;

export interface PortfoliosIndexProps {
    portfolios: PaginatedPortfolios;
    auth: AuthContext;
    portfoliocategories: any[];
    [key: string]: unknown;
}

export interface CreatePortfolioProps {
    onSuccess: () => void;
}

export interface EditPortfolioProps {
    portfolio: Portfolio;
    onSuccess: () => void;
}

export interface PortfolioShowProps {
    portfolio: Portfolio;
    [key: string]: unknown;
}
