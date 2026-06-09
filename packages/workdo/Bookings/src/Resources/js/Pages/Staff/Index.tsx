import { useState, useMemo } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit, Trash2, Users, User } from "lucide-react";
import { getImagePath } from '@/utils/helpers';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";

import { PerPageSelector } from '@/components/ui/per-page-selector';
import { FilterButton } from '@/components/ui/filter-button';
import NoRecordsFound from '@/components/no-records-found';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogDescription } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import InputError from '@/components/ui/input-error';
import { formatDate } from '@/utils/helpers';
import { useForm } from '@inertiajs/react';

interface Staff {
    id: number;
    staff_id: number;
    item_ids: string;
    created_at: string;
    staff: {
        name: string;
        email: string;
        avatar?: string;
        mobile_no?: string;
    };
    item_names?: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Item {
    id: number;
    name: string;
}

interface StaffIndexProps {
    staff: {
        data: Staff[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    users: User[];
    items: Item[];
    auth: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Index() {
    const { t } = useTranslation();
    const { staff, users, items, auth } = usePage<StaffIndexProps>().props;
    const urlParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [filters, setFilters] = useState({
        name: urlParams.get('name') || '',
        staff_id: urlParams.get('staff_id') || ''
    });
    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');

    const [showFilters, setShowFilters] = useState(false);
    const [showCreateDialog, setShowCreateDialog] = useState(false);
    const [showEditDialog, setShowEditDialog] = useState(false);
    const [editingStaff, setEditingStaff] = useState<Staff | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        staff_id: '',
        item_ids: [],
    });

    const { data: editData, setData: setEditData, put, processing: editProcessing, errors: editErrors, reset: resetEdit } = useForm({
        staff_id: '',
        item_ids: [],
    });

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'bookings.staff.destroy',
        defaultMessage: t('Are you sure you want to delete this staff assignment?')
    });

    const handleFilter = () => {
        router.get(route('bookings.staff.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
            preserveState: true,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('bookings.staff.index'), {...filters, per_page: perPage, sort: field, direction}, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({ name: '', staff_id: '' });
        router.get(route('bookings.staff.index'), {per_page: perPage});
    };

    const tableColumns = [
        {
            key: 'avatar',
            header: t('Avatar'),
            render: (value: string, item: Staff) => {
                const avatar = item.staff?.avatar;
                return (
                    <div className="h-10 w-10 rounded-full border-2 border-white overflow-hidden bg-gray-100 flex items-center justify-center">
                        {avatar ? (
                            <img
                                src={getImagePath(avatar)}
                                alt={item.staff?.name || 'Staff'}
                                className="h-full w-full object-cover"
                            />
                        ) : (
                            <User className="h-5 w-5 text-gray-400" />
                        )}
                    </div>
                );
            }
        },
        {
            key: 'staff_id',
            header: t('Staff Name'),
            sortable: true,
            render: (value: string, item: Staff) => item.staff?.name || '-'
        },
        {
            key: 'staff.email',
            header: t('Email'),
            render: (value: string, item: Staff) => item.staff?.email || '-'
        },
        {
            key: 'mobile_no',
            header: t('Mobile Number'),
            render: (value: string, item: Staff) => item.staff?.mobile_no || '-'
        },
        {
            key: 'item_names',
            header: t('Items'),
            render: (value: string, item: Staff) => {
                if (!item.item_names) return '-';
                const items = item.item_names.split(',').map(name => name.trim());
                const visibleItems = items.slice(0, 3);
                const remainingCount = items.length - 3;
                
                return (
                    <div className="flex flex-wrap gap-1">
                        {visibleItems.map((itemName, index) => (
                            <span key={index} className="capitalize px-2 py-1 bg-gray-100 rounded-full text-sm">
                                {itemName}
                            </span>
                        ))}
                        {remainingCount > 0 && (
                            <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                +{remainingCount}
                            </span>
                        )}
                    </div>
                );
            }
        },
        ...(auth.user?.permissions?.some((p: string) => ['edit-booking-staff', 'delete-booking-staff'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, item: Staff) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('edit-booking-staff') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => {
                                        setEditingStaff(item);
                                        setEditData({
                                            staff_id: item.staff_id.toString(),
                                            item_ids: item.item_ids.split(','),
                                        });
                                        setShowEditDialog(true);
                                    }} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Edit')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-booking-staff') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openDeleteDialog(item.id)}
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
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('Staff')}
                ]}
                pageTitle={t('Manage Staff')}
                pageActions={
                    <div className="flex gap-2">
                        {auth.user?.permissions?.includes('create-booking-staff') && (
                            <Dialog open={showCreateDialog} onOpenChange={setShowCreateDialog}>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <DialogTrigger asChild>
                                            <Button size="sm">
                                                <Plus className="h-4 w-4" />
                                            </Button>
                                        </DialogTrigger>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Create')}</p>
                                    </TooltipContent>
                                </Tooltip>
                                <DialogContent className="sm:max-w-md">
                                    <DialogHeader>
                                        <DialogTitle>{t('Assign Staff to Items')}</DialogTitle>
                                    </DialogHeader>
                                    <form onSubmit={(e) => {
                                        e.preventDefault();
                                        post(route('bookings.staff.store'), {
                                            onSuccess: () => {
                                                reset();
                                                setShowCreateDialog(false);
                                            }
                                        });
                                    }} className="space-y-4">
                                        <div>
                                            <Label htmlFor="staff_id" required>{t('Staff Member')}</Label>
                                            <Select value={data.staff_id} onValueChange={(value) => setData('staff_id', value)}>
                                                <SelectTrigger>
                                                    <SelectValue placeholder={t('Select staff member')} />
                                                </SelectTrigger>
                                                <SelectContent searchable={true}>
                                                    {users.map((user) => (
                                                        <SelectItem key={user.id} value={user.id.toString()}>
                                                            {user.name} ({user.email})
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors.staff_id} />
                                        </div>
                                        <div>
                                            <Label htmlFor="item_ids" required>{t('Booking Items')}</Label>
                                            <MultiSelectEnhanced
                                                options={items.map(item => ({
                                                    value: item.id.toString(),
                                                    label: item.name
                                                }))}
                                                value={data.item_ids}
                                                onValueChange={(value) => setData('item_ids', value)}
                                                placeholder={t('Select booking items')}
                                            />
                                            <InputError message={errors.item_ids} />
                                        </div>
                                        <div className="flex justify-end gap-2 pt-4">
                                            <Button type="button" variant="outline" onClick={() => {
                                                reset();
                                                setShowCreateDialog(false);
                                            }}>
                                                {t('Cancel')}
                                            </Button>
                                            <Button type="submit" disabled={processing}>
                                                {processing ? t('Assigning...') : t('Assign')}
                                            </Button>
                                        </div>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        )}
                    </div>
                }
            >
            <Head title={t('Staff')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.name}
                                onChange={(value) => setFilters({...filters, name: value})}
                                onSearch={handleFilter}
                                placeholder={t('Search staff...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <PerPageSelector
                                routeName="bookings.staff.index"
                                filters={filters}
                            />
                            <div className="relative">
                                <FilterButton
                                    showFilters={showFilters}
                                    onToggle={() => setShowFilters(!showFilters)}
                                />
                                {(() => {
                                    const activeFilters = [filters.staff_id].filter(Boolean).length;
                                    return activeFilters > 0 && (
                                        <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            {activeFilters}
                                        </span>
                                    );
                                })()}
                            </div>
                        </div>
                    </div>
                </CardContent>

                {showFilters && (
                    <CardContent className="p-6 bg-blue-50/30 border-b">
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Staff Member')}</label>
                                <Select value={filters.staff_id} onValueChange={(value) => setFilters({...filters, staff_id: value})}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by staff')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {users.map((user) => (
                                            <SelectItem key={user.id} value={user.id.toString()}>
                                                {user.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex items-end gap-2">
                                <Button onClick={handleFilter} size="sm">{t('Apply')}</Button>
                                <Button variant="outline" onClick={clearFilters} size="sm">{t('Clear')}</Button>
                            </div>
                        </div>
                    </CardContent>
                )}

                <CardContent className="p-0">
                    <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                        <div className="min-w-[800px]">
                            <DataTable
                                data={staff.data}
                                columns={tableColumns}
                                onSort={handleSort}
                                sortKey={sortField}
                                sortDirection={sortDirection as 'asc' | 'desc'}
                                className="rounded-none"
                                emptyState={
                                    <NoRecordsFound
                                        icon={Users}
                                        title={t('No staff found')}
                                        description={t('Get started by assigning your first staff member.')}
                                        hasFilters={!!(filters.name || filters.staff_id)}
                                        onClearFilters={clearFilters}
                                        createPermission="create-booking-staff"
                                        onCreateClick={() => setShowCreateDialog(true)}
                                        createButtonText={t('Assign Staff')}
                                        className="h-auto"
                                    />
                                }
                            />
                        </div>
                    </div>
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={staff}
                        routeName="bookings.staff.index"
                        filters={{...filters, per_page: perPage, sort: sortField, direction: sortDirection}}
                    />
                </CardContent>
            </Card>



            <Dialog open={showEditDialog} onOpenChange={(open) => {
                if (!open) {
                    resetEdit();
                    setEditingStaff(null);
                }
                setShowEditDialog(open);
            }}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>{t('Edit Staff Assignment')}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={(e) => {
                        e.preventDefault();
                        if (editingStaff) {
                            put(route('bookings.staff.update', editingStaff.id), {
                                onSuccess: () => {
                                    resetEdit();
                                    setEditingStaff(null);
                                    setShowEditDialog(false);
                                }
                            });
                        }
                    }} className="space-y-4">
                        <div>
                            <Label htmlFor="edit_staff_id" required>{t('Staff Member')}</Label>
                            <Select value={editData.staff_id} onValueChange={(value) => setEditData('staff_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select staff member')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {users.map((user) => (
                                        <SelectItem key={user.id} value={user.id.toString()}>
                                            {user.name} ({user.email})
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={editErrors.staff_id} />
                        </div>
                        <div>
                            <Label htmlFor="edit_item_ids" required>{t('Booking Items')}</Label>
                            <MultiSelectEnhanced
                                options={items.map(item => ({
                                    value: item.id.toString(),
                                    label: item.name
                                }))}
                                value={editData.item_ids}
                                onValueChange={(value) => setEditData('item_ids', value)}
                                placeholder={t('Select booking items')}
                            />
                            <InputError message={editErrors.item_ids} />
                        </div>
                        <div className="flex justify-end gap-2 pt-4">
                            <Button type="button" variant="outline" onClick={() => {
                                resetEdit();
                                setEditingStaff(null);
                                setShowEditDialog(false);
                            }}>
                                {t('Cancel')}
                            </Button>
                            <Button type="submit" disabled={editProcessing}>
                                {editProcessing ? t('Updating...') : t('Update')}
                            </Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Staff Assignment')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            </AuthenticatedLayout>
        </TooltipProvider>
    );
}