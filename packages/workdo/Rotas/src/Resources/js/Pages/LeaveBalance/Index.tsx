import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Pagination } from '@/components/ui/pagination';
import { Users as UsersIcon, User as UserIcon } from "lucide-react";

interface LeaveBalanceData {
    employee_id: number;
    employee_name: string;
    leave_types: {
        leave_type_name: string;
        leave_type_color: string;
        total_days: number;
        used_days: number;
        available_days: number;
    }[];
}

interface LeaveBalanceIndexProps {
    leaveBalances: {
        data: LeaveBalanceData[];
        links: any[];
        meta: any;
    } | LeaveBalanceData[];
}

export default function Index() {
    const { t } = useTranslation();
    const { leaveBalances } = usePage<LeaveBalanceIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState({
        search: urlParams.get('search') || '',
    });

    useFlashMessages();

    const handleFilter = () => {
        router.get(route('rotas.leave-balance.index'), filters, {
            preserveState: true,
            replace: true
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Rotas'), url: route('rotas.dashboard.index') },
                { label: t('Leave Balance') }
            ]}
            pageTitle={t('Manage Leave Balance')}
        >
            <Head title={t('Leave Balance')} />

            {/* Main Content Card */}
            <Card className="shadow-sm">
                {/* Search & Controls Header */}
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search employees...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="rotas.leave-balance.index"
                                filters={{...filters}}
                            />
                        </div>
                    </div>
                </CardContent>

                {/* Grid Content */}
                <CardContent className="p-0">
                    <div className="overflow-auto max-h-[70vh] p-6">
                        {(leaveBalances?.data || leaveBalances)?.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                                {(leaveBalances?.data || leaveBalances)?.map((employee) => (
                                <Card key={employee.employee_id} className="hover:shadow-md transition-shadow">
                                    <CardHeader className="pb-4">
                                        <div className="flex items-center gap-3">
                                            <div className="p-2 bg-primary/10 rounded-lg flex-shrink-0">
                                                <UserIcon className="h-5 w-5 text-primary" />
                                            </div>
                                            <div className="min-w-0 flex-1">
                                                <h3 className="font-semibold text-base truncate">{employee.employee_name}</h3>
                                            </div>
                                        </div>
                                    </CardHeader>
                                    <CardContent className="pt-0">
                                        <div className="space-y-2">
                                            {/* Table Header */}
                                            <div className="grid grid-cols-[2fr_1fr_1fr_1fr] gap-2 pb-2 border-b border-gray-200">
                                                <div className="text-xs font-medium text-muted-foreground">{t('Type')}</div>
                                                <div className="text-xs font-medium text-muted-foreground text-center">{t('Total')}</div>
                                                <div className="text-xs font-medium text-muted-foreground text-center">{t('Used')}</div>
                                                <div className="text-xs font-medium text-muted-foreground text-center">{t('Available')}</div>
                                            </div>

                                            {/* Leave Type Rows */}
                                            {employee.leave_types.map((leaveType, index) => (
                                                <div key={index} className="grid grid-cols-[2fr_1fr_1fr_1fr] gap-2 py-2 px-2 bg-gray-50 rounded-lg items-center">
                                                    <div className="flex items-center gap-2 min-w-0">
                                                        <div
                                                            className="w-2 h-2 rounded-full flex-shrink-0"
                                                            style={{ backgroundColor: leaveType.leave_type_color || '#gray' }}
                                                        ></div>
                                                        <span className="text-xs font-medium break-words">{leaveType.leave_type_name}</span>
                                                    </div>
                                                    <div className="text-xs font-medium text-gray-800 text-center">{leaveType.total_days}</div>
                                                    <div className="text-xs font-medium text-red-600 text-center">{leaveType.used_days}</div>
                                                    <div className="text-xs font-medium text-green-600 text-center">{leaveType.available_days}</div>
                                                </div>
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12">
                                <UserIcon className="mx-auto h-12 w-12 text-gray-400" />
                                <h3 className="mt-2 text-sm font-medium text-gray-900">{t('No employees found')}</h3>
                                <p className="mt-1 text-sm text-gray-500">{t('No employee leave balances to display.')}</p>
                            </div>
                        )}
                    </div>
                </CardContent>

                {/* Pagination Footer */}
                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={leaveBalances}
                        routeName="rotas.leave-balance.index"
                        filters={{...filters}}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}