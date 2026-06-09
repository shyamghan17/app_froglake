import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Pagination } from "@/components/ui/pagination";
import { formatCurrency, formatDate } from '@/utils/helpers';
import { CreditCard, CheckCircle, Trash2 } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Button } from '@/components/ui/button';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import NoRecordsFound from '@/components/no-records-found';

interface Payment {
    id: number;
    booking_id: number;
    payment_amount: number;
    description: string;
    payment_method: string;
    payment_date: string;
    customer_name: string;
    reference_number: string;
    service: string;
    beauty_service?: {
        id: number;
        name: string;
    };
    booking: {
        id: number;
        name: string;
        payment_status: string;
    };
}

interface PaymentsIndexProps {
    payments: {
        data: Payment[];
        links: any[];
        meta: any;
    };
    auth: any;
}

export default function Index() {
    const { t } = useTranslation();
    const { payments, auth } = usePage<PaymentsIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [search, setSearch] = useState(urlParams.get('search') || '');
    const [perPage] = useState(urlParams.get('per_page') || '10');

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'beauty-spa-management.beauty-bookings.payments.destroy',
        defaultMessage: t('Are you sure you want to delete this payment?')
    });

    const handleFilter = () => {
        // Add filter logic here if needed
    };

    const tableColumns = [
        {
            key: 'booking.name',
            header: t('Customer Name'),
            render: (_: any, payment: Payment) => payment.booking?.name || payment.customer_name || '-'
        },
        {
            key: 'service',
            header: t('Service'),
            render: (_: any, payment: Payment) => payment.beauty_service?.name || '-'
        },
        {
            key: 'total_person',
            header: t('Total Person'),
        },
        {
            key: 'payment_amount',
            header: t('Payment Amount'),
            render: (value: number) => formatCurrency(value)
        },
        
        {
            key: 'payment_date',
            header: t('Payment Date'),
            render: (value: string) => formatDate(value)
        },
        {
            key: 'reference_number',
            header: t('Reference Number'),
        },
        {
            key: 'description',
            header: t('Description'),
            render: (value: string) => value || '-'
        },
        ...(auth.user?.permissions?.some((p: string) => ['beauty-bookings-payments-paid', 'delete-beauty-bookings-payment'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, payment: Payment) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {payment.booking?.payment_status !== 'paid' && auth.user?.permissions?.includes('beauty-bookings-payments-paid') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => router.post(route('beauty-spa-management.beauty-bookings.payments.mark-paid', payment.id))}
                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                    >
                                        <CheckCircle className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Mark as Paid')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-beauty-bookings-payment') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(payment.id)}
                                        className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Delete')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            )
        }] : [])
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Beauty Spa Management'), url: route('beauty-spa-management.index')},
                {label: t('Payments')}
            ]}
            pageTitle={t('Manage Payments')}
        >
            <Head title={t('Payments')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={search}
                                onChange={(value) => setSearch(value)}
                                onSearch={handleFilter}
                                placeholder={t('Search payments...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="beauty-spa-management.beauty-bookings.payments.index"
                                filters={{search}}
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={payments?.data || []}
                                columns={tableColumns}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={CreditCard}
                                        title={t('No payments found')}
                                        description={t('No booking payments have been recorded yet.')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={payments || { data: [], links: [], meta: {} }}
                        routeName="beauty-spa-management.beauty-bookings.payments.index"
                        filters={{search, per_page: perPage}}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Payment')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}