import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface GiftCard {
    id: number;
    card_code: string;
    customer: number;
    balance: number;
    expiry_date: string;
    status: boolean;
    created_at: string;
}

export interface CreateGiftCardFormData {
    card_code: string;
    customer: string;
    balance: string;
    expiry_date: string;
    status: boolean;
}

export interface EditGiftCardFormData {
    card_code: string;
    customer: string;
    balance: string;
    expiry_date: string;
    status: boolean;
}

export interface GiftCardFilters {
    card_code: string;
    status: string;
}

export type PaginatedGiftCards = PaginatedData<GiftCard>;
export type GiftCardModalState = ModalState<GiftCard>;

export interface GiftCardsIndexProps {
    giftcards: PaginatedGiftCards;
    auth: AuthContext;
    users: any[];
    [key: string]: unknown;
}

export interface CreateGiftCardProps {
    onSuccess: () => void;
}

export interface EditGiftCardProps {
    giftcard: GiftCard;
    onSuccess: () => void;
}

export interface GiftCardShowProps {
    giftcard: GiftCard;
    [key: string]: unknown;
}