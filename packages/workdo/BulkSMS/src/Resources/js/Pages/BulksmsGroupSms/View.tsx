import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { ArrowLeft, MessageSquare, Trash2 } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import NoRecordsFound from '@/components/no-records-found';

interface Message {
    id: number;
    name: string;
    group_id: number;
    mobile_no: string;
    sms: string;
    status: string;
    created_at: string;
}

interface MessagesProps {
    messages: {
        data: Message[];
        links: any[];
        meta: any;
    };
    bulksmsgroupsms: any;
    bulksmsgroups: any[];
    auth: any;
}

export default function Messages() {
    const { t } = useTranslation();
    const { messages, bulksmsgroupsms, bulksmsgroups, auth } = usePage<MessagesProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    
    const [filters, setFilters] = useState({
        search: urlParams.get('search') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bulk-s-m-s.bulksms-send-messages.destroy',
        defaultMessage: t('Are you sure you want to delete this message?')
    });

    const group = bulksmsgroups?.find((item: any) => item.id === bulksmsgroupsms.group_id);

    const handleFilter = () => {
        router.get(route('bulk-s-m-s.bulksms-group-sms.show', bulksmsgroupsms.id), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bulk-s-m-s.bulksms-group-sms.show', bulksmsgroupsms.id), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            search: '',
        });
        router.get(route('bulk-s-m-s.bulksms-group-sms.show', bulksmsgroupsms.id), {per_page: perPage});
    };

    const tableColumns = [
        {
            key: 'name',
            header: t('Contact Name'),
            sortable: true
        },
        {
            key: 'mobile_no',
            header: t('Mobile Number'),
            sortable: false
        },
        {
            key: 'status',
            header: t('Status'),
            sortable: false,
            render: (value: string) => {
                const statusOptions: any = {"0":"pending","1":"sent","2":"failed"};
                const displayValue = statusOptions[value] || value || '-';
                const statusColors = {
                    delivered: 'bg-green-100 text-green-800',
                    failed: 'bg-red-100 text-red-800'
                };
                const colorClass = statusColors[displayValue as keyof typeof statusColors] || 'bg-gray-100 text-gray-800';
                return (
                    <span className={`px-2 py-1 rounded-full text-sm ${colorClass}`}>
                        {t(displayValue)}
                    </span>
                );
            }
        },
        ...(auth.user?.permissions?.includes('delete-bulk-sms-groups-send') ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, message: Message) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => openDeleteDialog(message.id)}
                                    className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                >
                                    <Trash2 className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Delete')}</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            )
        }] : [])
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {label: t('Bulk SMS')},
                {label: t('Send Bulk SMS'), url: route('bulk-s-m-s.bulksms-group-sms.index')},
                {label: t('Messages')}
            ]}
            pageTitle={t('Bulk Sms Messages')}
            pageActions={
                <Button size="sm" variant="outline" onClick={() => router.get(route('bulk-s-m-s.bulksms-group-sms.index'))}>
                    <ArrowLeft className="h-4 w-4 mr-2" />
                    {t('Back')}
                </Button>
            }
        >
            <Head title={t('Bulk Sms Messages')} />

            <Card className="shadow-sm mb-4">
                <CardContent className="p-6">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-2 bg-primary/10 rounded-lg">
                            <MessageSquare className="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold">{group?.name || t('Group')}</h3>
                            <p className="text-sm text-muted-foreground">{t('SMS sent to group contacts')}</p>
                        </div>
                    </div>
                    <div className="bg-gray-50 rounded-lg p-4 border">
                        <p className="text-sm text-gray-900 whitespace-pre-wrap">
                            <span className="font-medium text-gray-700">{t('SMS :')} </span>
                            {bulksmsgroupsms.sms}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({...filters, search: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search names and mobile numbers...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="bulk-s-m-s.bulksms-group-sms.show"
                                filters={{...filters, id: bulksmsgroupsms.id}}
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                        <DataTable
                            data={messages?.data || []}
                            columns={tableColumns}
                            onSort={handleSort}
                            sortKey={sortField}
                            sortDirection={sortDirection as 'asc' | 'desc'}
                            className="rounded-none"
                            emptyState={
                                <NoRecordsFound
                                    icon={MessageSquare}
                                    title={t('No Messages found')}
                                    description={t('No messages were sent to this group.')}
                                    hasFilters={!!(filters.search)}
                                    onClearFilters={clearFilters}
                                    className="h-auto"
                                />
                            }
                        />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={messages || { data: [], links: [], meta: {} }}
                        routeName="bulk-s-m-s.bulksms-group-sms.show"
                        filters={{...filters, per_page: perPage, id: bulksmsgroupsms.id}}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Message')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}