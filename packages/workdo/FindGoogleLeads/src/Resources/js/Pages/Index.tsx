import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Dialog } from "@/components/ui/dialog";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { Plus, Edit as EditIcon, Trash2, Eye, MapPin, MoreVertical, Search } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { FilterButton } from '@/components/ui/filter-button';
import { Pagination } from "@/components/ui/pagination";
import { SearchInput } from "@/components/ui/search-input";
import { PerPageSelector } from '@/components/ui/per-page-selector';
import { Badge } from '@/components/ui/badge';
import NoRecordsFound from '@/components/no-records-found';
import Create from './Create';

interface FindGoogleLead {
  id: number;
  name: string;
  keywords: string;
  address: string;
  contact: number;
  created_at: string;
}

interface FindGoogleLeadsIndexProps {
  leads: {
    data: FindGoogleLead[];
    links: any[];
    meta: any;
  };
  auth: any;
  permissions: string[];
}

interface FindGoogleLeadFilters {
  name: string;
  keywords: string;
  address: string;
}

interface FindGoogleLeadModalState {
  isOpen: boolean;
  mode: string;
  data: FindGoogleLead | null;
}

export default function Index() {
  const { t } = useTranslation();
  const { leads, auth, permissions } = usePage<FindGoogleLeadsIndexProps>().props;
  const urlParams = new URLSearchParams(window.location.search);

  const [filters, setFilters] = useState<FindGoogleLeadFilters>({
    name: urlParams.get('name') || '',
    keywords: urlParams.get('keywords') || '',
    address: urlParams.get('address') || '',
  });

  const [perPage] = useState(urlParams.get('per_page') || '10');
  const [sortField, setSortField] = useState(urlParams.get('sort') || '');
  const [sortDirection, setSortDirection] = useState(urlParams.get('direction') || 'asc');
  const [modalState, setModalState] = useState<FindGoogleLeadModalState>({
    isOpen: false,
    mode: '',
    data: null
  });
  const [showFilters, setShowFilters] = useState(false);

  useFlashMessages();

  const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
    routeName: 'find-google-leads.destroy',
    defaultMessage: t('Are you sure you want to delete this google lead search?')
  });

  const handleFilter = () => {
    router.get(route('find-google-leads.index'), {...filters, per_page: perPage, sort: sortField, direction: sortDirection}, {
      preserveState: true,
      replace: true
    });
  };

  const handleSort = (field: string) => {
    const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
    setSortField(field);
    setSortDirection(direction);
    router.get(route('find-google-leads.index'), {...filters, per_page: perPage, sort: field, direction}, {
      preserveState: true,
      replace: true
    });
  };

  const clearFilters = () => {
    setFilters({
      name: '',
      keywords: '',
      address: '',
    });
    router.get(route('find-google-leads.index'), {per_page: perPage});
  };

  const openModal = (mode: 'add' | 'edit', data: FindGoogleLead | null = null) => {
    setModalState({ isOpen: true, mode, data });
  };

  const closeModal = () => {
    setModalState({ isOpen: false, mode: '', data: null });
  };

  const tableColumns = [
    {
      key: 'name',
      header: t('Title'),
      sortable: true
    },
    {
      key: 'keywords',
      header: t('Keywords'),
      sortable: true
    },
    {
      key: 'address',
      header: t('Address'),
      sortable: true
    },
    {
      key: 'contact',
      header: t('Contacts Found'),
      sortable: false,
      render: (value: number, row: any) => (
        <Badge variant="secondary">
          {row.contacts_count || 0}
        </Badge>
      )
    },
    ...(auth.user?.permissions?.some((p: string) => ['view-find-google-leads','edit-find-google-leads', 'delete-find-google-leads'].includes(p)) ? [{
      key: 'actions',
      header: t('Actions'),
      render: (_: any, lead: FindGoogleLead) => (
        <div className="flex gap-1">
          <TooltipProvider>
            {auth.user?.permissions?.includes('view-find-google-leads') && (
              <Tooltip delayDuration={0}>
                <TooltipTrigger asChild>
                  <Button variant="ghost" size="sm" onClick={() => router.get(route('find-google-leads.show',lead.id))} className="h-8 w-8 p-0 text-green-600 hover:text-green-700">
                    <Eye className="h-4 w-4" />
                  </Button>
                </TooltipTrigger>
                <TooltipContent>
                  <p>{t('View')}</p>
                </TooltipContent>
              </Tooltip>
            )}
            {auth.user?.permissions?.includes('delete-find-google-leads') && (
              <Tooltip delayDuration={0}>
                <TooltipTrigger asChild>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => openDeleteDialog(lead.id)}
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
        {label: t('Find Google Leads')}
      ]}
      pageTitle={t('Manage Find Google Leads')}
      pageActions={
        <div className="flex items-center gap-2">
          <TooltipProvider>
            {auth.user?.permissions?.includes('create-find-google-leads') && (
              <Tooltip delayDuration={0}>
                <TooltipTrigger asChild>
                  <Button size="sm" onClick={() => openModal('add')}>
                    <Search className="h-4 w-4" />
                  </Button>
                </TooltipTrigger>
                <TooltipContent>
                  <p>{t('Search')}</p>
                </TooltipContent>
              </Tooltip>
            )}
          </TooltipProvider>
        </div>
      }
    >
      <Head title={t('Find Google Leads')} />

      <Card className="shadow-sm">
        <CardContent className="p-6 border-b bg-gray-50/50">
          <div className="flex items-center justify-between gap-4">
            <div className="flex-1 max-w-md">
              <SearchInput
                value={filters.name}
                onChange={(value) => setFilters({...filters, name: value})}
                onSearch={handleFilter}
                placeholder={t('Search Leads...')}
              />
            </div>
            <div className="flex items-center gap-3">
              <PerPageSelector
                routeName="find-google-leads.index"
                filters={filters}
              />
              <div className="relative">
                <FilterButton
                  showFilters={showFilters}
                  onToggle={() => setShowFilters(!showFilters)}
                />
                {(() => {
                  const activeFilters = [filters.keywords, filters.address].filter(f => f !== '' && f !== null && f !== undefined).length;
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
                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Keywords')}</label>
                <Input
                  placeholder={t('Filter by keywords')}
                  value={filters.keywords}
                  onChange={(e) => setFilters({...filters, keywords: e.target.value})}
                  onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">{t('Address')}</label>
                <Input
                  placeholder={t('Filter by address')}
                  value={filters.address}
                  onChange={(e) => setFilters({...filters, address: e.target.value})}
                  onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                />
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
                data={leads?.data || []}
                columns={tableColumns}
                onSort={handleSort}
                sortKey={sortField}
                sortDirection={sortDirection as 'asc' | 'desc'}
                className="rounded-none"
                emptyState={
                  <NoRecordsFound
                    icon={MapPin}
                    title={t('No Google Leads found')}
                    description={t('Get started by creating your first Google Lead search.')}
                    hasFilters={!!(filters.name || filters.keywords || filters.address)}
                    onClearFilters={clearFilters}
                    createPermission="create-find-google-leads"
                    onCreateClick={() => openModal('add')}
                    createButtonText={t('Search Google Lead')}
                    className="h-auto"
                  />
                }
              />
            </div>
          </div>
        </CardContent>

        <CardContent className="px-4 py-2 border-t bg-gray-50/30">
          <Pagination
            data={leads || { data: [], links: [], meta: {} }}
            routeName="find-google-leads.index"
            filters={{...filters, per_page: perPage}}
          />
        </CardContent>
      </Card>

      <Dialog open={modalState.isOpen} onOpenChange={closeModal}>
        {modalState.mode === 'add' && (
          <Create onSuccess={closeModal} />
        )}
      </Dialog>

      <ConfirmationDialog
        open={deleteState.isOpen}
        onOpenChange={closeDeleteDialog}
        title={t('Delete Google Lead Search')}
        message={deleteState.message}
        confirmText={t('Delete')}
        onConfirm={confirmDelete}
        variant="destructive"
      />
    </AuthenticatedLayout>
  );
}
