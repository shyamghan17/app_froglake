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
import { ArrowLeft, Trash2, UserPlus, RefreshCw } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Badge } from '@/components/ui/badge';
import NoRecordsFound from '@/components/no-records-found';
import LeadCreateWrapper from '../components/LeadCreateWrapper';



interface FindGoogleLeadContact {
  id: number;
  name: string;
  mobile_no: string;
  website: string;
  address: string;
  email: string;
}

interface FindGoogleLead {
  id: number;
  name: string;
  keywords: string;
  address: string;
  contact: number;
}

interface ShowProps {
  lead: FindGoogleLead;
  contacts: FindGoogleLeadContact[];
  auth: any;
}

export default function Show() {
  const { t } = useTranslation();
  const { lead, contacts, auth } = usePage<ShowProps>().props;
  const [convertingContact, setConvertingContact] = useState<FindGoogleLeadContact | null>(null);

  useFlashMessages();

  const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
    routeName: 'find-google-leads.contacts.destroy',
    defaultMessage: t('Are you sure you want to delete this contact?')
  });

  const tableColumns = [
    {
      key: 'name',
      header: t('Name'),
      sortable: false
    },
    {
      key: 'mobile_no',
      header: t('Phone'),
      sortable: false,
      render: (value: string) => value || '-'
    },
    {
      key: 'website',
      header: t('Website'),
      sortable: false,
      render: (value: string) => value || '-'
    },
    {
      key: 'address',
      header: t('Address'),
      sortable: false
    },
    ...(auth.user?.permissions?.some((p: string) => ['create-leads', 'delete-find-google-leads'].includes(p)) ? [{
      key: 'actions',
      header: t('Actions'),
      render: (_: any, contact: FindGoogleLeadContact) => (
        <div className="flex gap-1">
          <TooltipProvider>
            {auth.user?.permissions?.includes('create-leads') && (
              <Tooltip delayDuration={0}>
                <TooltipTrigger asChild>
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    onClick={() => setConvertingContact(contact)}
                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                  >
                    <RefreshCw className="h-4 w-4" />
                  </Button>
                </TooltipTrigger>
                <TooltipContent>
                  <p>{t('Convert to Lead')}</p>
                </TooltipContent>
              </Tooltip>
            )}
            {auth.user?.permissions?.includes('delete-find-google-leads') && (
              <Tooltip delayDuration={0}>
                <TooltipTrigger asChild>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => openDeleteDialog(contact.id)}
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
        {label: t('Find Google Leads'), url: route('find-google-leads.index')},
        {label: lead.name}
      ]}
      pageTitle={t('Google Lead Details')}     
    >
      <Head title={t('Google Lead Details')} />

      {/* Lead Info Card */}
      <Card className="shadow-sm mb-6">
        <CardContent className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <h3 className="font-medium text-gray-700 mb-1">{t('Title')}</h3>
              <p className="text-gray-900">{lead.name}</p>
            </div>
            <div>
              <h3 className="font-medium text-gray-700 mb-1">{t('Keywords')}</h3>
              <p className="text-gray-900">{lead.keywords}</p>
            </div>
            <div>
              <h3 className="font-medium text-gray-700 mb-1">{t('Address')}</h3>
              <p className="text-gray-900">{lead.address}</p>
            </div>
            <div>
              <h3 className="font-medium text-gray-700 mb-1">{t('Total Contacts')}</h3>
              <Badge variant="secondary">
                {contacts?.length || 0}
              </Badge>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Contacts Table */}
      <Card className="shadow-sm">
        <CardContent className="p-0">
          <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
            <div className="min-w-[800px]">
              <DataTable
                data={contacts || []}
                columns={tableColumns}
                className="rounded-none"
                emptyState={
                  <NoRecordsFound
                    icon={UserPlus}
                    title={t('No contacts found')}
                    description={t('No contacts were found for this Google lead search.')}
                    hasFilters={false}
                    className="h-auto"
                  />
                }
              />
            </div>
          </div>
        </CardContent>
      </Card>



      <Dialog open={!!convertingContact} onOpenChange={() => setConvertingContact(null)}>
        {convertingContact && (
          <LeadCreateWrapper 
            onSuccess={() => setConvertingContact(null)}
            initialData={{
              name: convertingContact.name,
              email: convertingContact.email,
              phone: convertingContact.mobile_no,
              website: convertingContact.website,
              google_contact_lead_id: convertingContact.id
            }}
          />
        )}
      </Dialog>

      <ConfirmationDialog
        open={deleteState.isOpen}
        onOpenChange={closeDeleteDialog}
        title={t('Delete Contact')}
        message={deleteState.message}
        confirmText={t('Delete')}
        onConfirm={confirmDelete}
        variant="destructive"
      />
    </AuthenticatedLayout>
  );
}