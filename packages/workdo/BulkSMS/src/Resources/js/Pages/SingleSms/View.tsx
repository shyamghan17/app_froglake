import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { MessageSquare } from 'lucide-react';
import { SingleSms } from './types';
import { usePage } from '@inertiajs/react';

interface ViewProps {
    singlesms: SingleSms;
}

export default function View({ singlesms }: ViewProps) {
    const { t } = useTranslation();
    const { bulksmscontacts } = usePage().props as any;

    const contact = bulksmscontacts?.find((item: any) => item.id.toString() === singlesms.contact_id?.toString());
    const statusOptions: any = {"0":"pending","1":"sent","2":"failed"};
    const statusColors = {
        delivered: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800'
    };
    const status = statusOptions[singlesms.status] || singlesms.status || '-';
    const colorClass = statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800';

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <MessageSquare className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('SMS Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{contact?.name || singlesms.contact_id}</p>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-6">
                <div className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('Contact')}</label>
                            <p className="text-sm text-gray-900">{contact?.name || singlesms.contact_id || '-'}</p>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">{t('Mobile Number')}</label>
                            <p className="text-sm text-gray-900">{singlesms.mobile_no || '-'}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">{t('Status')}</label>
                        <span className={`px-3 py-1 rounded-full text-sm ${colorClass}`}>
                            {t(status)}
                        </span>
                    </div>
                    
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">{t('SMS Message')}</label>
                        <div className="bg-gray-50 rounded-lg p-4 border">
                            <p className="text-sm text-gray-900 whitespace-pre-wrap">{singlesms.sms || t('No message content')}</p>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    );
}