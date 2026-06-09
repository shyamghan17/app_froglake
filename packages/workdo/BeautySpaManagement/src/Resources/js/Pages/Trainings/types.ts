import { PaginatedData, ModalState, AuthContext } from '@/types/common';



export interface Training {
    id: number;
    training_name: string;
    trainer: string;
    date: string;
    duration: string;
    location: string;
    description?: string;
    created_at: string;
}

export interface CreateTrainingFormData {
    training_name: string;
    trainer: string;
    date: string;
    duration: string;
    location: string;
    description: string;
}

export interface EditTrainingFormData {
    training_name: string;
    trainer: string;
    date: string;
    duration: string;
    location: string;
    description: string;
}

export interface TrainingFilters {
    training_name: string;
    trainer: string;
    location: string;
}

export type PaginatedTrainings = PaginatedData<Training>;
export type TrainingModalState = ModalState<Training>;

export interface TrainingsIndexProps {
    trainings: PaginatedTrainings;
    auth: AuthContext;
    [key: string]: unknown;
}

export interface CreateTrainingProps {
    onSuccess: () => void;
}

export interface EditTrainingProps {
    training: Training;
    onSuccess: () => void;
}

export interface TrainingShowProps {
    training: Training;
    [key: string]: unknown;
}