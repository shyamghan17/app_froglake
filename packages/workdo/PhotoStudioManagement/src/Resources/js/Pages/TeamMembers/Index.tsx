import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, User as UserIcon, Mail, Phone, Clock, DollarSign, Wrench } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { ListGridToggle } from '@/components/ui/list-grid-toggle';
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Create from './Create';
import Edit from './Edit';
import View from './View';
import NoRecordsFound from '@/components/no-records-found';
import { PhotoStudioTeamMember, TeamMembersIndexProps, TeamMemberFilters, TeamMemberModalState } from './types';
import { formatCurrency, getImagePath } from '@/utils/helpers';

export default function Index() {
    const { t } = useTranslation();
    const { teamMembers, auth, users } = usePage<TeamMembersIndexProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [filters, setFilters] = useState<TeamMemberFilters>({
        search: urlParams.get('search') || '',
        is_active: urlParams.get('is_active') || '',
    });

    const [perPage] = useState(urlParams.get('per_page') || '10');
    const [sortField, setSortField] = useState(urlParams.get('sort') || '');
    const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
    const [viewMode, setViewMode] = useState<'list' | 'grid'>(urlParams.get('view') as 'list' | 'grid' || 'list');
    const [showFilters, setShowFilters] = useState(false);
    const [modalState, setModalState] = useState<TeamMemberModalState>({ isOpen: false, mode: '', data: null });
    const [viewingItem, setViewingItem] = useState<PhotoStudioTeamMember | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'photo-studio-management.team-members.destroy',
        defaultMessage: t('Are you sure you want to delete this team member?'),
    });

    const handleFilter = () => {
        router.get(route('photo-studio-management.team-members.index'), { ...filters, per_page: perPage, sort: sortField, direction: sortDirection, view: viewMode }, { preserveState: true, replace: true });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
        router.get(route('photo-studio-management.team-members.index'), { ...filters, per_page: perPage, sort: field, direction, view: viewMode }, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setFilters({ search: '', is_active: '' });
        router.get(route('photo-studio-management.team-members.index'), { per_page: perPage, view: viewMode });
    };

    const openModal = (mode: 'add' | 'edit', data: PhotoStudioTeamMember | null = null) => setModalState({ isOpen: true, mode, data });
    const closeModal = () => setModalState({ isOpen: false, mode: '', data: null });

    const tableColumns = [
        {
            key: 'user',
            header: t('Member'),
            render: (_: any, member: PhotoStudioTeamMember) => (
                <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-lg overflow-hidden bg-gray-100 border flex items-center justify-center">
                        {member.user?.avatar ? (
                            <img src={getImagePath(member.user.avatar)} alt="Avatar" className="w-full h-full object-cover" />
                        ) : (
                            <UserIcon className="w-4 h-4 text-gray-400" />
                        )}
                    </div>
                    <span className="text-sm">{member.user?.name || '-'}</span>
                </div>
            ),
        },
        { key: 'email', header: t('Email'), render: (_: any, m: PhotoStudioTeamMember) => m.user?.email || '-' },
        { key: 'mobile', header: t('Mobile'), render: (_: any, m: PhotoStudioTeamMember) => m.user?.mobile_no || '-' },
        { key: 'designation', header: t('Designation'), render: (_: any, m: PhotoStudioTeamMember) => m.designation || '-' },
        { key: 'experience_year', header: t('Experience'), render: (_: any, m: PhotoStudioTeamMember) => `${m.experience_year} ${t('yrs')}` },
        { key: 'skills', header: t('Skills'), render: (_: any, m: PhotoStudioTeamMember) => m.skills || '-' },
        { key: 'rate_per_hour', header: t('Rate/Hour'), render: (_: any, m: PhotoStudioTeamMember) => m.rate_per_hour ? formatCurrency(m.rate_per_hour) : '-' },
        {
            key: 'is_active',
            header: t('Status'),
            render: (_: any, m: PhotoStudioTeamMember) => (
                <span className={`px-2 py-1 rounded-full text-sm  ${m.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'}`}>
                    {m.is_active ? t('Active') : t('Inactive')}
                </span>
            ),
        },
        ...(auth.user?.permissions?.some((p: string) => ['view-photo-studio-team-members', 'edit-photo-studio-team-members', 'delete-photo-studio-team-members'].includes(p)) ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, member: PhotoStudioTeamMember) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-photo-studio-team-members') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => setViewingItem(member)} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('edit-photo-studio-team-members') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openModal('edit', member)} className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700">
                                        <EditIcon className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-photo-studio-team-members') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(member.id)} className="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            ),
        }] : []),
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Photo Studio Management'), url: route('photo-studio-management.index') },
                { label: t('Team Members') },
            ]}
            pageTitle={t('Manage Team Members')}
            pageActions={
                <div className="flex gap-2">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('create-photo-studio-team-members') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => openModal('add')}><Plus className="h-4 w-4" /></Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Create')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            }
        >
            <Head title={t('Team Members')} />

            <Card className="shadow-sm">
                <CardContent className="p-6 border-b bg-gray-50/50">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex-1 max-w-md">
                            <SearchInput
                                value={filters.search}
                                onChange={(value) => setFilters({ ...filters, search: value })}
                                onSearch={handleFilter}
                                placeholder={t('Search Team Members...')}
                            />
                        </div>
                        <div className="flex items-center gap-3">
                            <ListGridToggle currentView={viewMode} routeName="photo-studio-management.team-members.index" filters={{ ...filters, per_page: perPage }} />
                            <PerPageSelector routeName="photo-studio-management.team-members.index" filters={{ ...filters, view: viewMode }} />
                            <div className="relative">
                                <FilterButton showFilters={showFilters} onToggle={() => setShowFilters(!showFilters)} />
                                {filters.is_active !== '' && (
                                    <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">1</span>
                                )}
                            </div>
                        </div>
                    </div>
                </CardContent>

                {showFilters && (
                    <CardContent className="p-6 bg-blue-50/30 border-b">
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                                <Select value={filters.is_active} onValueChange={(value) => setFilters({ ...filters, is_active: value })}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Filter by Status')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">{t('Active')}</SelectItem>
                                        <SelectItem value="0">{t('Inactive')}</SelectItem>
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
                    {viewMode === 'list' ? (
                        <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                            <div className="min-w-[800px]">
                                <DataTable
                                    data={teamMembers?.data || []}
                                    columns={tableColumns}
                                    onSort={handleSort}
                                    sortKey={sortField}
                                    sortDirection={sortDirection as 'asc' | 'desc'}
                                    className="rounded-none"
                                    emptyState={
                                        <NoRecordsFound
                                            icon={UserIcon}
                                            title={t('No Team Members found')}
                                            description={t('Get started by adding your first team member.')}
                                            hasFilters={!!(filters.search || filters.is_active)}
                                            onClearFilters={clearFilters}
                                            createPermission="create-photo-studio-team-members"
                                            onCreateClick={() => openModal('add')}
                                            createButtonText={t('Add Team Member')}
                                            className="h-auto"
                                        />
                                    }
                                />
                            </div>
                        </div>
                    ) : (
                        <div className="overflow-auto max-h-[70vh] p-6">
                            {teamMembers?.data?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                    {teamMembers.data.map((member) => (
                                        <Card key={member.id} className="p-0 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-gray-200 rounded-xl">
                                            {/* Top accent bar */}

                                            {/* Avatar + identity */}
                                            <div className="flex flex-col items-center pt-5 pb-4 px-4 border-b border-dashed border-gray-200">
                                                <div className="relative mb-3">
                                                    <div className="w-16 h-16 rounded-full ring-2 ring-primary/30 ring-offset-2 overflow-hidden bg-gray-100 flex items-center justify-center shadow-sm">
                                                        {member.user?.avatar ? (
                                                            <img src={getImagePath(member.user.avatar)} alt="Avatar" className="w-full h-full object-cover" />
                                                        ) : (
                                                            <UserIcon className="w-7 h-7 text-gray-400" />
                                                        )}
                                                    </div>
                                                    <span className={`absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white ${member.is_active ? 'bg-green-500' : 'bg-red-400'}`} />
                                                </div>
                                                <h3 className="font-semibold text-sm text-gray-900 text-center leading-tight truncate w-full text-center">{member.user?.name || '-'}</h3>
                                                <p className="text-xs text-primary/80 font-medium mt-0.5 truncate w-full text-center">{member.designation || '-'}</p>
                                            </div>

                                            {/* Info rows */}
                                            <div className="px-4 py-3 flex-1 space-y-2">
                                                <div className="flex items-center gap-2 text-xs text-gray-600 min-w-0">
                                                    <Mail className="w-3.5 h-3.5 text-primary/60 shrink-0" />
                                                    <span className="truncate">{member.user?.email || '-'}</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-600">
                                                    <Phone className="w-3.5 h-3.5 text-primary/60 shrink-0" />
                                                    <span>{member.user?.mobile_no || '-'}</span>
                                                </div>
                                                <div className="grid grid-cols-2 gap-x-3 gap-y-2 pt-1 border-t border-gray-100">
                                                    <div className="flex items-center gap-1.5 text-xs text-gray-600">
                                                        <Clock className="w-3.5 h-3.5 text-primary/60 shrink-0" />
                                                        <span>{member.experience_year} {t('yrs')}</span>
                                                    </div>
                                                    <div className="flex items-center gap-1.5 text-xs text-gray-600">
                                                        <DollarSign className="w-3.5 h-3.5 text-primary/60 shrink-0" />
                                                        <span>{member.rate_per_hour ? formatCurrency(member.rate_per_hour) : '-'}</span>
                                                    </div>
                                                </div>
                                                <div className="flex items-start gap-2 text-xs text-gray-600 min-w-0">
                                                    <Wrench className="w-3.5 h-3.5 text-primary/60 shrink-0 mt-0.5" />
                                                    <span className="truncate">{member.skills || '-'}</span>
                                                </div>
                                            </div>

                                            <div className="flex justify-end gap-2 p-3 border-t bg-gray-50/50 mt-auto">
                                                <TooltipProvider>
                                                    {auth.user?.permissions?.includes('view-photo-studio-team-members') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => setViewingItem(member)} className="h-9 w-9 p-0 text-green-600 hover:text-green-700">
                                                                    <Eye className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('edit-photo-studio-team-members') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => openModal('edit', member)} className="h-9 w-9 p-0 text-blue-600 hover:text-blue-700">
                                                                    <EditIcon className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('delete-photo-studio-team-members') && (
                                                        <Tooltip delayDuration={300}>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="ghost" size="sm" onClick={() => openDeleteDialog(member.id)} className="h-9 w-9 p-0 text-red-600 hover:text-red-700">
                                                                    <Trash2 className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                </TooltipProvider>
                                            </div>
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <NoRecordsFound
                                    icon={UserIcon}
                                    title={t('No Team Members found')}
                                    description={t('Get started by adding your first team member.')}
                                    hasFilters={!!(filters.search || filters.is_active)}
                                    onClearFilters={clearFilters}
                                    createPermission="create-photo-studio-team-members"
                                    onCreateClick={() => openModal('add')}
                                    createButtonText={t('Add Team Member')}
                                />
                            )}
                        </div>
                    )}
                </CardContent>

                <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                    <Pagination
                        data={teamMembers || { data: [], links: [], meta: {} }}
                        routeName="photo-studio-management.team-members.index"
                        filters={{ ...filters, per_page: perPage, view: viewMode }}
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Team Member')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            <Dialog open={modalState.isOpen && modalState.mode === 'add'} onOpenChange={closeModal}>
                <Create key={modalState.isOpen ? 'create' : 'closed'} onClose={closeModal} users={users} />
            </Dialog>

            <Dialog open={modalState.isOpen && modalState.mode === 'edit'} onOpenChange={closeModal}>
                {modalState.data && (
                    <Edit
                        teamMember={modalState.data}
                        onClose={closeModal}
                        users={[...users, ...(modalState.data.user ? [modalState.data.user] : [])].filter((u, i, self) => self.findIndex(x => x.id === u.id) === i)}
                    />
                )}
            </Dialog>

            <Dialog open={!!viewingItem} onOpenChange={() => setViewingItem(null)}>
                {viewingItem && <View teamMember={viewingItem} onClose={() => setViewingItem(null)} />}
            </Dialog>
        </AuthenticatedLayout>
    );
}
