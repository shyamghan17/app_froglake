import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { History } from 'lucide-react';
import { SuggestionStatusHistory } from './types';

interface ViewProps {
    suggestionstatushistory: SuggestionStatusHistory;
}

export default function View({ suggestionstatushistory }: ViewProps) {
    const { t } = useTranslation();

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            'new': { label: 'New', color: 'bg-blue-100 text-blue-800' },
            'under_review': { label: 'Under Review', color: 'bg-yellow-100 text-yellow-800' },
            'accepted': { label: 'Accepted', color: 'bg-purple-100 text-purple-800' },
            'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-800' },
            'complete': { label: 'Complete', color: 'bg-green-100 text-green-800' }
        };

        const config = statusConfig[status as keyof typeof statusConfig];
        return (
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}>
                {t(config?.label || status || '-')}
            </span>
        );
    };

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <History className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Status History Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Suggestion')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                            {suggestionstatushistory.suggestion?.title || '-'}
                        </p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Changed By')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                            {suggestionstatushistory.user?.name || '-'}
                        </p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Old Status')}</label>
                        <div className="p-2 rounded">
                            {suggestionstatushistory.old_status ?
                                getStatusBadge(suggestionstatushistory.old_status.toString()) :
                                <span className="text-gray-500 text-sm">{t('Initial Status')}</span>
                            }
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('New Status')}</label>
                        <div className="p-2 rounded">
                            {getStatusBadge(suggestionstatushistory.new_status.toString())}
                        </div>
                    </div>
                </div>

                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Response')}</label>
                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                        {suggestionstatushistory.comment || '-'}
                    </p>
                </div>
            </div>
        </DialogContent>
    );
}