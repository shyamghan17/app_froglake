import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { Users } from 'lucide-react';
import { BulkSmsGroup } from './types';
import { usePage } from '@inertiajs/react';

interface ViewProps {
    bulksmsgroup: BulkSmsGroup;
}

export default function View({ bulksmsgroup }: ViewProps) {
    const { t } = useTranslation();
    const { bulksmscontacts } = usePage<any>().props;

    // Get contact details for the selected contact IDs
    const selectedContacts = bulksmscontacts?.filter((contact: any) => 
        bulksmsgroup.contacts?.includes(contact.id.toString())
    ) || [];

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-hidden">
            <DialogHeader className="pb-4">
                <DialogTitle>{t('Group Details')}</DialogTitle>
            </DialogHeader>
            
            <div className="overflow-y-auto max-h-[calc(90vh-140px)] p-2">
                <div className="grid grid-cols-1 gap-4 md:gap-6">
                    {/* Group Information Card */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                        <h4 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Users className="h-5 w-5 text-primary" />
                            {t('Group Information')}
                        </h4>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1">
                                <span className="text-gray-600">{t('Group Name')}:</span>
                                <span className="font-medium">{bulksmsgroup.name}</span>
                            </div>
                            <div className="flex flex-col gap-1">
                                <span className="text-gray-600">{t('Total Contacts')}:</span>
                                <span className="font-medium">{selectedContacts.length}</span>
                            </div>
                        </div>
                    </div>

                    {/* Contacts Table Card */}
                    {selectedContacts.length > 0 && (
                        <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                            <h4 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                {t('Contacts')}
                            </h4>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {t('Name')}
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {t('Phone Number')}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {selectedContacts.map((contact: any) => (
                                            <tr key={contact.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">{contact.name}</div>
                                                    <div className="text-sm text-gray-500">{contact.email}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-primary">{contact.mobile_no || '-'}</div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {selectedContacts.length === 0 && (
                        <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                            <div className="text-center py-8">
                                <Users className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-medium text-gray-900 mb-2">{t('No Contacts')}</h3>
                                <p className="text-gray-500">{t('This group has no contacts assigned')}</p>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DialogContent>
    );
}