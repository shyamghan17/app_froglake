import { PaginatedData, ModalState, AuthContext } from '@/types/common';

export interface BeautyService {
    id: number;
    name: string;
}

export interface BeautyServiceOffer {
    id: number;
    title: string;
    name: string;
    price: number;
    start_date: string;
    end_date: string;
    discount?: number;
    offer_price: number;
    description?: string;
    beauty_service_id?: number;
    service?: BeautyService;
    created_at: string;
}

export interface CreateBeautyServiceOfferFormData {
    title: string;
    name: string;
    price: string;
    start_date: string;
    end_date: string;
    discount: string;
    offer_price: string;
    description: string;
    beauty_service_id: string;
}

export interface EditBeautyServiceOfferFormData {
    title: string;
    name: string;
    price: string;
    start_date: string;
    end_date: string;
    discount: string;
    offer_price: string;
    description: string;
    beauty_service_id: string;
}

export interface BeautyServiceOfferFilters {
    title: string;
    name: string;
    description: string;
    beauty_service_id: string;
}

export type PaginatedBeautyServiceOffers = PaginatedData<BeautyServiceOffer>;
export type BeautyServiceOfferModalState = ModalState<BeautyServiceOffer>;

export interface BeautyServiceOffersIndexProps {
    beautyserviceoffers: PaginatedBeautyServiceOffers;
    auth: AuthContext;
    beautyservices: any[];
    [key: string]: unknown;
}

export interface CreateBeautyServiceOfferProps {
    onSuccess: () => void;
}

export interface EditBeautyServiceOfferProps {
    beautyserviceoffer: BeautyServiceOffer;
    onSuccess: () => void;
}

export interface BeautyServiceOfferShowProps {
    beautyserviceoffer: BeautyServiceOffer;
    [key: string]: unknown;
}