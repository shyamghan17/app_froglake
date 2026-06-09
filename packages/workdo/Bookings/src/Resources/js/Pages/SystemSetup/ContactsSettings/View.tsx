import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { formatDate } from '@/utils/helpers';

interface Contact {
    id: number;
    name: string;
    email: string;
    phone?: string;
    subject?: string;
    message?: string;
    created_at?: string;
}

interface ViewProps {
    contact: Contact;
}

export default function View({ contact }: ViewProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-hidden">
            <DialogHeader className="pb-4">
                <DialogTitle>{t('Contact Details')}</DialogTitle>
            </DialogHeader>

            <div className="overflow-y-auto max-h-[calc(90vh-140px)] p-2">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    {/* Contact Information Card */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                        <h4 className="text-lg font-semibold text-gray-900 mb-4">
                            {t('Contact Information')}
                        </h4>
                        <div className="space-y-3">
                            <div>
                                <span className="text-gray-600">{t('Name')}:</span>
                                <p className="font-medium">{contact.name}</p>
                            </div>
                            <div>
                                <span className="text-gray-600">{t('Email')}:</span>
                                <p className="font-medium text-sm break-all">{contact.email}</p>
                            </div>
                            {contact.phone && (
                                <div>
                                    <span className="text-gray-600">{t('Phone')}:</span>
                                    <p className="font-medium">{contact.phone}</p>
                                </div>
                            )}
                            {contact.created_at && (
                                <div>
                                    <span className="text-gray-600">{t('Date')}:</span>
                                    <p className="font-medium">{formatDate(contact.created_at)}</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Message Information Card */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                        <h4 className="text-lg font-semibold text-gray-900 mb-4">
                            {t('Message Details')}
                        </h4>
                        <div className="space-y-3">
                            {contact.subject && (
                                <div>
                                    <span className="text-gray-600">{t('Subject')}:</span>
                                    <p className="font-medium">{contact.subject}</p>
                                </div>
                            )}
                            {contact.message && (
                                <div>
                                    <span className="text-gray-600">{t('Message')}:</span>
                                    <div className="mt-2 p-3 bg-gray-50 rounded-md border">
                                        <p className="text-sm leading-relaxed whitespace-pre-wrap">{contact.message}</p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    );
}
