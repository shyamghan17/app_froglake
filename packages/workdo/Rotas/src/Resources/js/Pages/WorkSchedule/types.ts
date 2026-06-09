export interface Employee {
    id: number;
    name: string;
    work_schedule?: WorkScheduleData[];
}

export interface WorkScheduleData {
    day: string;
    is_working: boolean;
}

export interface WorkScheduleIndexProps {
    employees: Employee[];
    auth: {
        user: {
            permissions: string[];
        };
    };
}